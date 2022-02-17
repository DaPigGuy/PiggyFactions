<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\info;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\players\{FactionsPlayer, PlayerManager};
use SOFe\InfoAPI\{Info, InfoAPI, PlayerInfo, StringInfo};
use pocketmine\player\Player;

final class FactionMemberInfo extends Info
{
    /** @var FactionsPlayer */
    private $member;
    /** @var Faction */
    private $faction;

    // This type should not be constructed on a factionless player.
    // For type safety, we accept both parameters here to ensure caller checks faction is non-null.
    public function __construct(FactionsPlayer $member, Faction $faction) {
        $this->member = $member;
        $this->faction = $faction;
    }

    public function getMember() : FactionsPlayer {
        return $this->member;
    }

    public function getFaction() : Faction {
        return $this->faction;
    }

    public function toString() : string {
        return (new FactionInfo($this->faction))->toString();
    }

    public function getInfoType() : string {
        return "faction member";
    }

    public static function register(PlayerManager $pm) : void
    {
        InfoAPI::provideFallback(PlayerInfo::class, self::class, function(PlayerInfo $info) use($pm) : ?FactionInfo {
            $member = $pm->getPlayer($info->getValue());
            if($member === null) return null;
            return new FactionMemberInfo($member);
        });
        InfoAPI::provideInfo(self::class, StringInfo::class, "piggyfactions.faction.role", fn($info) => new StringInfo($info->getMember()->getRole()));
        InfoAPI::provideInfo(self::class, StringInfo::class, "piggyfactions.faction.power", fn($info) => new StringInfo($info->getMember()->getPower()));
        InfoAPI::provideInfo(self::class, StringInfo::class, "piggyfactions.faction.maxPower", fn($info) => new StringInfo($info->getMember()->getMaxPower()));
        InfoAPI::provideInfo(self::class, StringInfo::class, "piggyfactions.faction.powerBoost", fn($info) => new StringInfo($info->getMember()->getPowerBoost()));
        InfoAPI::provideInfo(self::class, FactionInfo::class, "piggyfactions.faction", fn($info) => new FactionInfo($info->getFaction()));
    }
}
