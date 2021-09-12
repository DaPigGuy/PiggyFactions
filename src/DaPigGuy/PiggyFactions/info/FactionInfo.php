<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\info;

use function count;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use SOFe\InfoAPI\{Info, InfoAPI, NumberInfo, PlayerInfo, StringInfo};
use pocketmine\Server;

final class FactionInfo extends Info
{
    /** @var Faction */
    private $faction;

    public function __construct(Faction $faction) {
        $this->faction = $faction;
    }

    public function toString() : string {
        return $this->faction->getName();
    }

    public function getInfoType() : string {
        return "faction";
    }

    public static function register() : void {
        InfoAPI::provideInfo(self::class, StringInfo::class, "piggyfactions.faction.name", fn($info) => new StringInfo($info->faction->getName()));
        InfoAPI::provideInfo(self::class, StringInfo::class, "piggyfactions.faction.description", fn($info) => new StringInfo($info->faction->getDescription()));
        InfoAPI::provideInfo(self::class, StringInfo::class, "piggyfactions.faction.motd", fn($info) => new StringInfo($info->faction->getMotd()));
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.power", fn($info) => new NumberInfo($info->faction->getPower()));
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.maxPower", fn($info) => new NumberInfo($info->faction->getMaxPower()));
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.powerBoost", fn($info) => new NumberInfo($info->faction->getPowerBoost()));
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.onlineMembers", fn($info) => new NumberInfo(count($info->faction->getOnlineMembers())));
        InfoAPI::provideInfo(self::class, PlayerInfo::class, "piggyfactions.faction.leader", function(FactionInfo $info) : ?PlayerInfo {
            $leader = $info->getFaction()->getLeader();
            if($leader === null) return null;
            $player = Server::getInstance()->getPlayerByUUID($leader->getUuid());
            return $player !== null ? new PlayerInfo($player) : null;
        });
        InfoAPI::provideInfo(self::class, PositionInfo::class, "piggyfactions.faction.home", function(FactionInfo $info) : ?PositionInfo {
            $home = $info->getFaction()->getHome();
            return $home !== null ? new PositionInfo($home) : null;
        });
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.allies", fn($info) => new NumberInfo(count($info->faction->getAllies())));
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.enemies", fn($info) => new NumberInfo(count($info->faction->getEnemies())));
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.banned", fn($info) => new NumberInfo(count($info->faction->getBanned())));
        InfoAPI::provideInfo(self::class, NumberInfo::class, "piggyfactions.faction.money", fn($info) => new NumberInfo($info->faction->getMoney()));
    }
}
