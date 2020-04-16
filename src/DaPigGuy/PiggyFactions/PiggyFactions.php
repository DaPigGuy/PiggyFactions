<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\exceptions\MissingProviderDependencyException;
use DaPigGuy\libPiggyEconomy\exceptions\UnknownProviderException;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use DaPigGuy\PiggyCustomEnchants\utils\AllyChecks;
use DaPigGuy\PiggyFactions\chat\ChatManager;
use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\commands\FactionCommand;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use DaPigGuy\PiggyFactions\task\ShowChunksTask;
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
    /** @var EconomyProvider */
    private $economyProvider;

    /** @var FactionsManager */
    private $factionsManager;
    /** @var ClaimsManager */
    private $claimsManager;
    /** @var PlayerManager */
    private $playerManager;

    /** @var LanguageManager */
    private $languageManager;
    /** @var ChatManager */
    private $chatManager;

    /**
     * @throws MissingProviderDependencyException
     * @throws UnknownProviderException
     * @throws HookAlreadyRegistered
     */
    public function onEnable(): void
    {
        self::$instance = $this;

        $this->saveDefaultConfig();

        $this->database = libasynql::create($this, $this->getConfig()->get("database"), [
            // "sqlite" => "sqlite.sql", //TODO: SQLite3 support
            "mysql" => "mysql.sql"
        ]);

        libPiggyEconomy::init();
        $this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));

        $this->factionsManager = new FactionsManager($this);
        $this->claimsManager = new ClaimsManager($this);
        $this->playerManager = new PlayerManager($this);

        $this->languageManager = new LanguageManager($this);
        $this->chatManager = new ChatManager($this);

        $this->checkSoftDependencies();

        if (!PacketHooker::isRegistered()) PacketHooker::register($this);
        $this->getServer()->getCommandMap()->register("piggyfactions", new FactionCommand($this, "faction", "The PiggyFactions command", ["f", "factions"]));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ShowChunksTask($this), 10);
        //TODO: Poggit Update Checks
    }

    private function checkSoftDependencies(): void
    {
        if ($this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants") !== null) {
            AllyChecks::addCheck($this, function (Player $player, Entity $entity): bool {
                if ($entity instanceof Player) {
                    $playerFaction = $this->playerManager->getPlayerFaction($player->getUniqueId());
                    $entityFaction = $this->playerManager->getPlayerFaction($entity->getUniqueId());
                    if ($playerFaction === $entityFaction && $playerFaction !== null) return true;
                }
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

    public function getEconomyProvider(): EconomyProvider
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

    public function getChatManager(): ChatManager
    {
        return $this->chatManager;
    }
}