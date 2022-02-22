<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\players;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\player\Player;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class PlayerManager
{
    private static PlayerManager $instance;

    /** @var FactionsPlayer[] */
    private array $players = [];

    public function __construct(private PiggyFactions $plugin)
    {
        self::$instance = $this;

        $plugin->getDatabase()->executeSelect("piggyfactions.players.load", [], function (array $rows): void {
            foreach ($rows as $row) {
                $this->players[$row["uuid"]] = new FactionsPlayer(Uuid::fromString($row["uuid"]), $row["username"], $row["faction"], $row["role"], $row["power"], $row["powerboost"], $row["language"]);
            }
            $this->plugin->getLogger()->debug("Loaded " . count($rows) . " players");
        });
    }

    public static function getInstance(): PlayerManager
    {
        return self::$instance;
    }

    public function createPlayer(Player $player): FactionsPlayer
    {
        $this->plugin->getDatabase()->executeInsert("piggyfactions.players.create", [
            "uuid" => $player->getUniqueId()->toString(),
            "username" => $player->getName(),
            "faction" => null,
            "role" => null,
            "power" => $this->plugin->getConfig()->getNested("factions.power.default", 20),
            "language" => LanguageManager::LOCALE_CODE_TABLE[$player->getLocale()] ?? $this->plugin->getLanguageManager()->getDefaultLanguage()
        ]);
        $this->players[$player->getUniqueId()->toString()] = new FactionsPlayer($player->getUniqueId(), $player->getName(), null, null, $this->plugin->getConfig()->getNested("factions.power.default", 20), 0, LanguageManager::LOCALE_CODE_TABLE[$player->getLocale()] ?? $this->plugin->getLanguageManager()->getDefaultLanguage());
        return $this->players[$player->getUniqueId()->toString()];
    }

    public function getPlayer(Player $player): ?FactionsPlayer
    {
        return $this->getPlayerByUUID($player->getUniqueId());
    }

    public function getPlayerByUUID(UuidInterface $uuid): ?FactionsPlayer
    {
        return $this->players[$uuid->toString()] ?? null;
    }

    public function getPlayerByName(string $name): ?FactionsPlayer
    {
        foreach ($this->players as $player) {
            if (strtolower($player->getUsername()) === strtolower($name)) return $player;
        }
        return null;
    }

    public function getPlayerFaction(UuidInterface $uuid): ?Faction
    {
        return ($player = $this->getPlayerByUUID($uuid)) === null ? null : $player->getFaction();
    }

    public function areAlliedOrTruced(Player $a, Player $b): bool
    {
        $factionA = $this->getPlayerFaction($a->getUniqueId());
        $factionB = $this->getPlayerFaction($b->getUniqueId());
        if ($factionA === null || $factionB === null) return false;
        if ($factionA->isAllied($factionB) || $factionA->isTruced($factionB)) return true;
        if ($factionA->getId() === $factionB->getId()) return true;
        return false;
    }
}