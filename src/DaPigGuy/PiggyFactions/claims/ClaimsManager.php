<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\world\Position;
use pocketmine\world\World;

class ClaimsManager
{
    /** @var PiggyFactions */
    private $plugin;

    /** @var ClaimsManager */
    private static $instance;

    /** @var Claim[] */
    private $claims = [];

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;

        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new ClaimsListener($plugin, $this), $plugin);
        $plugin->getDatabase()->executeSelect("piggyfactions.claims.load", [], function (array $rows): void {
            foreach ($rows as $row) {
                $this->claims[$row["chunkX"] . ":" . $row["chunkZ"] . ":" . $row["level"]] = new Claim($row["faction"], $row["chunkX"], $row["chunkZ"], $row["level"]);
            }
            $this->plugin->getLogger()->debug("Loaded " . count($rows) . " claims");
        });
    }

    public static function getInstance(): ClaimsManager
    {
        return self::$instance;
    }

    public function getClaim(int $chunkX, int $chunkZ, string $level): ?Claim
    {
        return $this->claims[$chunkX . ":" . $chunkZ . ":" . $level] ?? null;
    }

    public function getClaimByPosition(Position $position): ?Claim
    {
        return $this->getClaim($position->getFloorX() >> 4, $position->getFloorZ() >> 4, $position->getWorld()->getFolderName());
    }

    /**
     * @return Claim[]
     */
    public function getFactionClaims(Faction $faction): array
    {
        return array_filter($this->claims, function (Claim $claim) use ($faction): bool {
            return $claim->getFaction() === $faction;
        });
    }

    public function createClaim(Faction $faction, World $world, int $chunkX, int $chunkZ): Claim
    {
        $args = [
            "faction" => $faction->getId(),
            "chunkX" => $chunkX,
            "chunkZ" => $chunkZ,
            "level" => $world->getFolderName()
        ];
        $this->claims[$args["chunkX"] . ":" . $args["chunkZ"] . ":" . $args["level"]] = new Claim($args["faction"], $args["chunkX"], $args["chunkZ"], $args["level"]);
        $this->plugin->getDatabase()->executeInsert("piggyfactions.claims.create", $args);
        return $this->claims[$args["chunkX"] . ":" . $args["chunkZ"] . ":" . $args["level"]];
    }

    public function deleteClaim(Claim $claim): void
    {
        unset($this->claims[($chunkX = $claim->getChunkX()) . ":" . ($chunkZ = $claim->getChunkZ()) . ":" . ($level = $claim->getLevel()->getFolderName())]);
        $this->plugin->getDatabase()->executeGeneric("piggyfactions.claims.delete", ["chunkX" => $chunkX, "chunkZ" => $chunkZ, "level" => $level]);
    }
}