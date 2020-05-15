<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use DaPigGuy\PiggyCustomEnchants\utils\AllyChecks;
use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\FactionCommand;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\logs\LogsListener;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\permissions\PermissionFactory;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use DaPigGuy\PiggyFactions\tag\TagManager;
use DaPigGuy\PiggyFactions\tasks\CheckUpdatesTask;
use DaPigGuy\PiggyFactions\tasks\ShowChunksTask;
use DaPigGuy\PiggyFactions\tasks\UpdatePowerTask;
use jojoe77777\FormAPI\Form;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class PiggyFactions extends PluginBase
{
    /** @var self */
    private static $instance;

    /** @var DataConnector */
    private $database;
    //private $economyProvider;

    /** @var FactionsManager */
    private $factionsManager;
    /** @var ClaimsManager */
    private $claimsManager;
    /** @var PlayerManager */
    private $playerManager;

    /** @var LanguageManager */
    private $languageManager;
    /** @var TagManager */
    private $tagManager;
    /** @var LogsManager */
    private $logsManager;

    /**
     * @throws HookAlreadyRegistered
     */
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
                $this->getLogger()->error($virion . " virion not found. Please download PiggyFactions from Poggit-CI or use DEVirion (not recommended).");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        }

        self::$instance = $this;

        $this->saveDefaultConfig();

        $this->database = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);

        /*
        libPiggyEconomy::init();
        try {
            if ($this->getConfig()->getNested("economy.enabled", false)) $this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
        } catch (\Exception $exception) {
            $this->getLogger()->error($exception->getMessage());
        }
        */

        PermissionFactory::init();
        FlagFactory::init();

        $this->factionsManager = new FactionsManager($this);
        $this->claimsManager = new ClaimsManager($this);
        $this->playerManager = new PlayerManager($this);

        $this->languageManager = new LanguageManager($this);
        $this->tagManager = new TagManager($this);
        $this->logsManager = new LogsManager($this);

        $this->checkSoftDependencies();

        if (!PacketHooker::isRegistered()) PacketHooker::register($this);
        $this->getServer()->getCommandMap()->register("piggyfactions", new FactionCommand($this, "faction", "The PiggyFactions command", ["f", "factions"]));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new LogsListener($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ShowChunksTask($this), 10);
        $this->getScheduler()->scheduleRepeatingTask(new UpdatePowerTask($this), UpdatePowerTask::INTERVAL);
        $this->getServer()->getAsyncPool()->submitTask(new CheckUpdatesTask($this->getDescription()->getVersion(), $this->getDescription()->getCompatibleApis()[0]));
    }

    public function onDisable(): void
    {
        if ($this->database !== null) $this->database->close();
    }

    private function checkSoftDependencies(): void
    {
        if ($this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants") !== null) {
            AllyChecks::addCheck($this, function (Player $player, Entity $entity): bool {
                if ($entity instanceof Player) return $this->playerManager->areAlliedOrTruced($player, $entity);
                return false;
            });
        }
    }

    public static function getInstance(): PiggyFactions
    {
        return self::$instance;
    }

    public function getDatabase(): DataConnector
    {
        return $this->database;
    }

    /*
    public function getEconomyProvider(): EconomyProvider
    {
        return $this->economyProvider;
    }
    */

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

    public function areFormsEnabled(): bool
    {
        return $this->getConfig()->get("forms", true);
    }
}