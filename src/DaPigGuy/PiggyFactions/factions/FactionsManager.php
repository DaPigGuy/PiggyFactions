<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\factions;

use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\UUID;

class FactionsManager
{
    /** @var PiggyFactions */
    private $plugin;

    /** @var self */
    private static $instance;

    /** @var Faction[] */
    private $factions = [];

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;

        $this->plugin = $plugin;
        $plugin->getDatabase()->executeGeneric("piggyfactions.factions.init");
        $plugin->getDatabase()->executeSelect("piggyfactions.factions.load", [], function (array $rows): void {
            foreach ($rows as $row) {
                if ($row["home"] !== null) {
                    $decodedHome = json_decode($row["home"], true);
                    $row["home"] = new Position($decodedHome["x"], $decodedHome["y"], $decodedHome["z"], $this->plugin->getServer()->getLevelByName($decodedHome["level"]));
                }

                $this->factions[$row["id"]] = new Faction($row["id"], $row["name"], UUID::fromString($row["leader"]), $row["description"], $row["motd"], json_decode($row["members"], true), json_decode($row["permissions"], true), $row["home"], json_decode($row["relations"], true) ?? []);
            }
            $this->plugin->getLogger()->debug("Loaded " . count($rows) . " factions");
        });
    }

    public static function getInstance(): FactionsManager
    {
        return self::$instance;
    }

    public function getFaction(int $id): ?Faction
    {
        return $this->factions[$id] ?? null;
    }

    public function getFactionByName(string $name): ?Faction
    {
        foreach ($this->factions as $faction) {
            if ($faction->getName() === $name) return $faction;
        }
        return null;
    }

    public function createFaction(string $name, Player $leader): void
    {
        $this->plugin->getDatabase()->executeInsert("piggyfactions.factions.create", ["name" => $name, "leader" => $leader->getUniqueId()->toString(), "members" => json_encode([$leader->getUniqueId()->toString()]), "permissions" => json_encode(Faction::DEFAULT_PERMISSIONS)], function (int $id) use ($name, $leader): void {
            $this->factions[$id] = new Faction($id, $name, $leader->getUniqueId(), null, null, [$leader->getUniqueId()->toString()], Faction::DEFAULT_PERMISSIONS, null, []);
            $this->plugin->getPlayerManager()->getPlayer($leader->getUniqueId())->setFaction($this->factions[$id]);
            $this->plugin->getPlayerManager()->getPlayer($leader->getUniqueId())->setRole(Faction::ROLE_LEADER);
        });
    }

    public function deleteFaction(int $id): void
    {
        unset($this->factions[$id]);
        $this->plugin->getDatabase()->executeGeneric("piggyfactions.factions.delete", ["id" => $id]);
    }
}