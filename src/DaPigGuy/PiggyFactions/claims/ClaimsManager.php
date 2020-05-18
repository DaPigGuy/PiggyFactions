<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;

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
        $plugin->getDatabase()->executeGeneric("piggyfactions.claims.init");
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

    public function getClaim(Level $level, Chunk $chunk): ?Claim
    {
        return $this->claims[$chunk->getX() . ":" . $chunk->getZ() . ":" . $level->getFolderName()] ?? null;
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

    public function createClaim(Faction $faction, Level $level, Chunk $chunk): Claim
    {
        $args = [
            "faction" => $faction->getId(),
            "chunkX" => $chunk->getX(),
            "chunkZ" => $chunk->getZ(),
            "level" => $level->getFolderName()
        ];
        $this->claims[$args["chunkX"] . ":" . $args["chunkZ"] . ":" . $args["level"]] = new Claim(...array_values($args));
        $this->plugin->getDatabase()->executeInsert("piggyfactions.claims.create", $args);
        return $this->claims[$args["chunkX"] . ":" . $args["chunkZ"] . ":" . $args["level"]];
    }

    public function deleteClaim(Claim $claim): void
    {
        unset($this->claims[($chunkX = $claim->getChunk()->getX()) . ":" . ($chunkZ = $claim->getChunk()->getZ()) . ":" . ($level = $claim->getLevel()->getFolderName())]);
        $this->plugin->getDatabase()->executeGeneric("piggyfactions.claims.delete", ["chunkX" => $chunkX, "chunkZ" => $chunkZ, "level" => $level]);
    }
}