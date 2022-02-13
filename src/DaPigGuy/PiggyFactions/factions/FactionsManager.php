<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\factions;

use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\permissions\PermissionFactory;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\world\Position;
use Ramsey\Uuid\Uuid;

class FactionsManager
{
    private PiggyFactions $plugin;

    private static FactionsManager $instance;

    /** @var Faction[] */
    private array $factions = [];

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;

        $this->plugin = $plugin;
        $plugin->getDatabase()->executeSelect("piggyfactions.factions.load", [], function (array $rows): void {
            foreach ($rows as $row) {
                $homeWorld = null;
                if ($row["home"] !== null) {
                    $decodedHome = json_decode($row["home"], true);
                    $homeWorld = $this->plugin->getServer()->getWorldManager()->getWorldByName($decodedHome["level"]);
                    $row["home"] = new Position($decodedHome["x"], $decodedHome["y"], $decodedHome["z"], $homeWorld);
                }
                $this->factions[$row["id"]] = new Faction($row["id"], $row["name"], $row["creation_time"], $row["description"], $row["motd"], json_decode($row["members"], true), json_decode($row["permissions"], true), json_decode($row["flags"], true), $row["home"], $homeWorld, isset($row["relations"]) ? json_decode($row["relations"], true) : [], isset($row["banned"]) ? json_decode($row["banned"], true) : [], $row["money"], $row["powerboost"]);
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
        $id = Uuid::uuid4()->toString();
        while (isset($this->factions[$id])) $id = Uuid::uuid4()->toString();
        $this->factions[$id] = new Faction($id, $name, time(), null, null, $members,
            array_map(function (FactionPermission $permission): array {
                return $permission->getHolders();
            }, PermissionFactory::getPermissions()),
            array_map(function (Flag $flag): bool {
                return $flag->getValue();
            }, $flags), null, [], [], 0, 0);
        foreach ($members as $member) {
            $this->plugin->getPlayerManager()->getPlayerByUUID(UUID::fromString($member))->setFaction($this->factions[$id]);
        }
        $this->plugin->getDatabase()->executeInsert("piggyfactions.factions.create", ["id" => $id, "name" => $name, "members" => json_encode($members), "permissions" => json_encode(PermissionFactory::getPermissions()), "flags" => json_encode($flags)]);
    }

    public function deleteFaction(string $id): void
    {
        unset($this->factions[$id]);
        $this->plugin->getDatabase()->executeGeneric("piggyfactions.factions.delete", ["id" => $id]);
    }
}