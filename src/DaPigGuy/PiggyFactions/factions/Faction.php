<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\factions;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\UUID;

class Faction
{
    const ROLE_LEADER = "leader";
    const ROLE_OFFICER = "officer";
    const ROLE_MEMBER = "member";
    const ROLE_RECRUIT = "recruit";

    const ROLES = [
        self::ROLE_RECRUIT => 1,
        self::ROLE_MEMBER => 2,
        self::ROLE_OFFICER => 3,
        self::ROLE_LEADER => 4
    ];

    const RELATION_ALLY = "ally";
    const RELATION_TRUCE = "truce";
    const RELATION_ENEMY = "enemy";
    const RELATION_NONE = "none";

    const PERMISSIONS = [
        "ally",
        "claim",
        "demote",
        "description",
        "invite",
        "kick",
        "motd",
        "name",
        "promote",
        "sethome",
        "unally",
        "unclaim"
    ];
    const DEFAULT_PERMISSIONS = [
        self::ROLE_OFFICER => [
            "claim" => true,
            "description" => true,
            "invite" => true,
            "kick" => true,
            "motd" => true,
            "sethome" => true
        ]
    ];

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var UUID */
    private $leader;
    /** @var ?string */
    private $description;
    /** @var ?string */
    private $motd;
    /** @var UUID[] */
    private $members;
    /** @var array */
    private $permissions;
    /** @var ?Position */
    private $home;

    /** @var array */
    private $relations;
    /** @var array */
    private $relationWish;

    /** @var array */
    private $invitedPlayers;

    public function __construct(int $id, string $name, UUID $leader, ?string $description, ?string $motd, array $members, array $permissions, ?Position $home, array $relations)
    {
        $this->id = $id;
        $this->name = $name;
        $this->leader = $leader;
        $this->description = $description;
        $this->motd = $motd;
        $this->members = array_map(function (string $uuid): UUID {
            return UUID::fromString($uuid);
        }, $members);
        $this->permissions = $permissions;
        $this->home = $home;
        $this->relations = $relations;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->update();
    }

    public function getLeader(): UUID
    {
        return $this->leader;
    }

    public function setLeader(UUID $leader): void
    {
        $this->leader = $leader;
        $this->update();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
        $this->update();
    }

    public function getMotd(): ?string
    {
        return $this->motd;
    }

    public function setMotd(?string $motd): void
    {
        $this->motd = $motd;
        $this->update();
    }

    public function getPower(): float
    {
        $power = 0;
        foreach ($this->getMembers() as $member) {
            $power += $member->getPower();
        }
        return $power;
    }

    /**
     * @return FactionsPlayer[]
     */
    public function getMembers(): array
    {
        return array_map(function (UUID $uuid): FactionsPlayer {
            return PlayerManager::getInstance()->getPlayer($uuid);
        }, $this->members);
    }

    /**
     * @return Player[]
     */
    public function getOnlineMembers(): array
    {
        $online = [];
        foreach ($this->members as $uuid) {
            if (($p = PiggyFactions::getInstance()->getServer()->getPlayerByUUID($uuid)) instanceof Player) $online[] = $p;
        }
        return $online;
    }

    public function getMember(string $member): ?FactionsPlayer
    {
        foreach ($this->getMembers() as $m) {
            if (strtolower($m->getUsername()) === strtolower($member)) return $m;
        }
        return null;
    }

    public function getMemberByUUID(UUID $uuid): ?FactionsPlayer
    {
        foreach ($this->getMembers() as $m) {
            if ($m->getUuid()->equals($uuid)) return $m;
        }
        return null;
    }

    public function addMember(Player $member): void
    {
        $this->members[] = $member->getUniqueId();
        PlayerManager::getInstance()->getPlayer($member->getUniqueId())->setFaction($this);
        PlayerManager::getInstance()->getPlayer($member->getUniqueId())->setRole(self::ROLE_RECRUIT);
        foreach ($this->getOnlineMembers() as $online) {
            LanguageManager::getInstance()->sendMessage($online, "commands.join.joined", ["{PLAYER}" => $member->getName()]);
        }
        $this->update();
    }

    public function removeMember(UUID $uuid): void
    {
        unset($this->members[array_search($uuid, $this->members)]);
        PlayerManager::getInstance()->getPlayer($uuid)->setFaction(null);
        PlayerManager::getInstance()->getPlayer($uuid)->setRole(null);
        $this->update();
    }

    public function hasInvite(Player $player): bool
    {
        return isset($this->invitedPlayers[$player->getName()]);
    }

    public function invitePlayer(Player $player): void
    {
        $this->invitedPlayers[$player->getName()] = $player;
    }

    public function revokeInvite(Player $player): void
    {
        unset($this->invitedPlayers[$player->getName()]);
    }

    public function hasPermission(FactionsPlayer $member, string $permission): bool
    {
        if ($member->getRole() === Faction::ROLE_LEADER) return true;
        return $this->getPermission($member->getRole(), $permission);
    }

    public function getPermission(string $role, string $permission): bool
    {
        return $this->permissions[$role][$permission] ?? false;
    }

    public function setPermission(string $role, string $permission, bool $value): void
    {
        $this->permissions[$role][$permission] = $value;
        $this->update();
    }

    public function getHome(): ?Position
    {
        return $this->home;
    }

    public function setHome(Position $home): void
    {
        $this->home = $home;
        $this->update();
    }

    public function getRelationWish(Faction $faction): string
    {
        return $this->relationWish[$faction->getId()] ?? self::RELATION_NONE;
    }

    public function setRelationWish(Faction $faction, string $relation): void
    {
        $this->relationWish[$faction->getId()] = $relation;
    }

    public function revokeRelationWish(Faction $faction): void
    {
        unset($this->relationWish[$faction->getId()]);
    }

    public function getRelation(Faction $faction): string
    {
        return $this->relations[$faction->getId()] ?? self::RELATION_NONE;
    }

    public function setRelation(Faction $faction, string $relation): void
    {
        $this->relations[$faction->getId()] = $relation;
        $this->update();
    }

    public function revokeRelation(Faction $faction): void
    {
        $relation = $this->getRelation($faction);
        unset($this->relations[$faction->getId()]);
        switch ($relation) {
            case self::RELATION_ALLY:
            case self::RELATION_TRUCE:
                if ($faction->getRelation($this) !== self::RELATION_NONE) $faction->revokeRelation($faction);
                break;
        }
        $this->update();
    }

    /**
     * @return Faction[]
     */
    public function getAllies(): array
    {
        $allies = [];
        foreach ($this->relations as $id => $relation) {
            if ($relation === self::RELATION_ALLY) {
                $allies[] = FactionsManager::getInstance()->getFaction($id);
            }
        }
        return $allies;
    }

    public function disband(): void
    {
        foreach ($this->getMembers() as $member) {
            $member->setFaction(null);
            $member->setRole(null);
        }
        foreach (ClaimsManager::getInstance()->getFactionClaims($this) as $claim) {
            ClaimsManager::getInstance()->deleteClaim($claim->getId());
        }
        foreach ($this->relations as $id => $relation) {
            if ($relation === self::RELATION_ALLY || $relation === self::RELATION_TRUCE) {
                $faction = FactionsManager::getInstance()->getFaction($id);
                $faction->revokeRelation($this);
            }
        }
        FactionsManager::getInstance()->deleteFaction($this->getId());
    }

    public function update(): void
    {
        PiggyFactions::getInstance()->getDatabase()->executeChange("piggyfactions.factions.update", [
            "id" => $this->id,
            "name" => $this->name,
            "leader" => $this->leader->toString(),
            "description" => $this->description,
            "motd" => $this->motd,
            "members" => json_encode(array_map(function (UUID $uuid): string {
                return $uuid->toString();
            }, $this->members)),
            "permissions" => json_encode($this->permissions),
            "home" => $this->home === null ? null : json_encode([
                "x" => $this->home->x,
                "y" => $this->home->y,
                "z" => $this->home->z,
                "level" => $this->home->level->getFolderName()
            ]),
            "relations" => json_encode($this->relations)
        ]);
    }
}