<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use DaPigGuy\PiggyCustomEnchants\utils\AllyChecks;
use DaPigGuy\PiggyFactions\addons\scorehud\ScoreHudListener;
use DaPigGuy\PiggyFactions\addons\scorehud\ScoreHudManager;
use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\FactionCommand;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\logs\LogsListener;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\permissions\PermissionFactory;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use DaPigGuy\PiggyFactions\addons\hrkchat\TagManager;
use DaPigGuy\PiggyFactions\tasks\CheckUpdatesTask;
use DaPigGuy\PiggyFactions\tasks\ShowChunksTask;
use DaPigGuy\PiggyFactions\tasks\UpdatePowerTask;
use DaPigGuy\PiggyFactions\utils\PoggitBuildInfo;
use Exception;
use jojoe77777\FormAPI\Form;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class PiggyFactions extends PluginBase
{
    const CURRENT_DB_VERSION = 4;

    private static PiggyFactions $instance;
    private PoggitBuildInfo $poggitBuildInfo;

    private DataConnector $database;
    private ?EconomyProvider $economyProvider = null;

    private FactionsManager $factionsManager;
    private ClaimsManager $claimsManager;
    private PlayerManager $playerManager;

    private LanguageManager $languageManager;
    private TagManager $tagManager;
    private LogsManager $logsManager;

    private ScoreHudManager $scoreHudManager;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        foreach (
            [
                "libasynql" => libasynql::class,
                "Commando" => BaseCommand::class,
                "libformapi" => Form::class
            ] as $virion => $class
        ) {
            if (!class_exists($class)) {
                $this->getLogger()->error($virion . " virion was not found. Download PiggyFactions at https://poggit.pmmp.io/p/PiggyFactions for pre-compiled phars.");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        }

        self::$instance = $this;
        $this->poggitBuildInfo = new PoggitBuildInfo($this, $this->getFile(), str_starts_with($this->getFile(), "phar://"));

        $this->saveDefaultConfig();
        $this->initDatabase();
        libPiggyEconomy::init();
        try {
            if ($this->getConfig()->getNested("economy.enabled", false)) $this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
        } catch (Exception $exception) {
            $this->getLogger()->error($exception->getMessage());
        }

        PermissionFactory::init();
        FlagFactory::init();

        $this->factionsManager = new FactionsManager($this);
        $this->claimsManager = new ClaimsManager($this);
        $this->playerManager = new PlayerManager($this);

        $this->languageManager = new LanguageManager($this);
        $this->tagManager = new TagManager($this);
        $this->logsManager = new LogsManager($this);

        $this->scoreHudManager = new ScoreHudManager($this);

        $this->checkSoftDependencies();

        if (!PacketHooker::isRegistered()) PacketHooker::register($this);
        $this->getServer()->getCommandMap()->register("piggyfactions", new FactionCommand($this, "faction", "The PiggyFactions command", ["f", "factions"]));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new LogsListener(), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ShowChunksTask($this), 10);
        $this->getScheduler()->scheduleRepeatingTask(new UpdatePowerTask($this), UpdatePowerTask::INTERVAL);
        $this->getServer()->getAsyncPool()->submitTask(new CheckUpdatesTask());
    }

    public function onDisable(): void
    {
        if ($this->database !== null) {
            $this->database->waitAll();
            $this->database->close();
        }
    }

    private function initDatabase(): void
    {
        $this->database = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);

        $this->database->executeGeneric("piggyfactions.factions.init");
        $this->database->executeGeneric("piggyfactions.players.init");
        $this->database->executeGeneric("piggyfactions.claims.init");
        $this->database->executeGeneric("piggyfactions.logs.init");
        $this->database->waitAll();

        $this->saveResource("internals.yml");
        $config = new Config($this->getDataFolder() . "internals.yml");
        for ($i = $config->get("database-version", 4); $i < self::CURRENT_DB_VERSION; $i++) {
            $this->database->executeGeneric("piggyfactions.patches." . $i, [], null, function (): void {
            });
        }
        $this->database->waitAll();
        $config->set("database-version", self::CURRENT_DB_VERSION);
        $config->save();
    }

    private function checkSoftDependencies(): void
    {
        if ($this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants") !== null) {
            AllyChecks::addCheck($this, function (Player $player, Entity $entity): bool {
                if ($entity instanceof Player) return $this->playerManager->areAlliedOrTruced($player, $entity);
                return false;
            });
        }
        if (($scorehud = $this->getServer()->getPluginManager()->getPlugin("ScoreHud")) !== null) {
            $version = $scorehud->getDescription()->getVersion();
            if (version_compare($version, "6.0.0") === 1) {
                if (version_compare($version, "6.1.0") === -1) {
                    $this->getLogger()->warning("Outdated version of ScoreHud (v" . $version . ") detected, requires >= v6.1.0. Integration disabled.");
                    return;
                }
                $this->getServer()->getPluginManager()->registerEvents(new ScoreHudListener($this), $this);
            }
        }
    }

    public static function getInstance(): PiggyFactions
    {
        return self::$instance;
    }

    public function getPoggitBuildInfo(): PoggitBuildInfo
    {
        return $this->poggitBuildInfo;
    }

    public function getDatabase(): DataConnector
    {
        return $this->database;
    }

    public function getEconomyProvider(): ?EconomyProvider
    {
        return $this->economyProvider;
    }

    public function getFactionsManager(): FactionsManager
    {
        return $this->factionsManager;
    }

    public function getClaimsManager(): ClaimsManager
    {
        return $this->claimsManager;
    }

    public function getPlayerManager(): PlayerManager
    {
        return $this->playerManager;
    }

    public function getLanguageManager(): LanguageManager
    {
        return $this->languageManager;
    }

    public function getTagManager(): TagManager
    {
        return $this->tagManager;
    }

    public function getLogsManager(): LogsManager
    {
        return $this->logsManager;
    }

    public function getScoreHudManager(): ScoreHudManager
    {
        return $this->scoreHudManager;
    }

    public function areFormsEnabled(): bool
    {
        return $this->getConfig()->get("forms", true);
    }

    public function isFactionBankEnabled(): bool
    {
        return $this->economyProvider !== null && $this->getConfig()->getNested("economy.faction-bank.enabled", true);
    }
}