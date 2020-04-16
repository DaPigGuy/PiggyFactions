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
                $this->claims[$row["id"]] = new Claim($row["id"], $row["faction"], $row["chunkX"], $row["chunkZ"], $row["level"]);
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
        foreach ($this->claims as $claim) {
            if ($level === $claim->getLevel() && $claim->getChunk() === $chunk) {
                return $claim;
            }
        }
        return null;
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

    public function createClaim(Faction $faction, Level $level, Chunk $chunk): void
    {
        $args = [
            "faction" => $faction->getId(),
            "chunkX" => $chunk->getX(),
            "chunkZ" => $chunk->getZ(),
            "level" => $level->getFolderName()
        ];
        $this->plugin->getDatabase()->executeInsert("piggyfactions.claims.create", $args, function (int $id) use ($args): void {
            $this->claims[$id] = new Claim($id, ...array_values($args));
        });
    }

    public function deleteClaim(int $id): void
    {
        unset($this->claims[$id]);
        $this->plugin->getDatabase()->executeGeneric("piggyfactions.claims.delete", ["id" => $id]);
    }
}