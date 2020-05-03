<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\factions;

use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\permissions\PermissionFactory;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\level\Position;
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

                $this->factions[$row["id"]] = new Faction($row["id"], $row["name"], $row["description"], $row["motd"], json_decode($row["members"], true), json_decode($row["permissions"], true), json_decode($row["flags"], true), $row["home"], isset($row["relations"]) ? json_decode($row["relations"], true) : []);
            }
            $this->plugin->getLogger()->debug("Loaded " . count($rows) . " factions");
        });
    }

    public static function getInstance(): FactionsManager
    {
        return self::$instance;
    }

    public function getFaction(string $id): ?Faction
    {
        return $this->factions[$id] ?? null;
    }

    public function getFactionByName(string $name): ?Faction
    {
        foreach ($this->factions as $faction) {
            if (strtolower($faction->getName()) === strtolower($name)) return $faction;
        }
        return null;
    }

    /**
     * @return Faction[]
     */
    public function getFactions(): array
    {
        return $this->factions;
    }

    public function createFaction(string $name, array $members, ?array $flags = null): void
    {
        $flags = $flags ?? FlagFactory::getFlags();
        $id = UUID::fromRandom()->toString();
        while (isset($this->factions[$id])) $id = UUID::fromRandom()->toString();
        $this->factions[$id] = new Faction($id, $name, null, null, $members,
            array_map(function (FactionPermission $permission): array {
                return $permission->getHolders();
            }, PermissionFactory::getPermissions()),
            array_map(function (Flag $flag): bool {
                return $flag->getValue();
            }, $flags), null, []);
        foreach ($members as $member) {
            PlayerManager::getInstance()->getPlayer(UUID::fromString($member))->setFaction($this->factions[$id]);
        }
        $this->plugin->getDatabase()->executeInsert("piggyfactions.factions.create", ["id" => $id, "name" => $name, "members" => json_encode($members), "permissions" => json_encode(PermissionFactory::getPermissions()), "flags" => json_encode($flags)]);
    }

    public function deleteFaction(string $id): void
    {
        unset($this->factions[$id]);
        $this->plugin->getDatabase()->executeGeneric("piggyfactions.factions.delete", ["id" => $id]);
    }
}