<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\factions;

use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\permissions\FactionPermission;
use DaPigGuy\PiggyFactions\permissions\PermissionFactory;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use DaPigGuy\PiggyFactions\utils\Relations;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\UUID;

class Faction
{
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
    /** @var FactionPermission[] */
    private $permissions;
    /** @var Flag[] */
    private $flags;
    /** @var ?Position */
    private $home;

    /** @var array */
    private $relations;
    /** @var array */
    private $relationWish;

    /** @var array */
    private $invitedPlayers;

    public function __construct(int $id, string $name, UUID $leader, ?string $description, ?string $motd, array $members, array $permissions, array $flags, ?Position $home, array $relations)
    {
        $this->id = $id;
        $this->name = $name;
        $this->leader = $leader;
        $this->description = $description;
        $this->motd = $motd;
        $this->members = array_map(function (string $uuid): UUID {
            return UUID::fromString($uuid);
        }, $members);
        foreach (PermissionFactory::getPermissions() as $name => $permission) {
            $permission = clone $permission;
            if (isset($permissions[$name])) $permission->setHolders($permissions[$name]);
            $this->permissions[$name] = $permission;
        }
        foreach (FlagFactory::getFlags() as $name => $flag) {
            $flag = clone $flag;
            if (isset($flags[$name])) $flag->setValue($flags[$name]);
            $this->flags[$name] = $flag;
        }
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
        PlayerManager::getInstance()->getPlayer($member->getUniqueId())->setRole(Roles::RECRUIT);
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
        $role = $member->getRole();
        if (($faction = $member->getFaction()) !== $this) $role = $faction === null ? Relations::NONE : $faction->getRelation($this);
        if ($role === Roles::LEADER || $member->isInAdminMode()) return true;
        return $this->getPermission($role, $permission);
    }

    public function getPermission(string $role, string $permission): bool
    {
        return in_array($role, $this->permissions[$permission]->getHolders());
    }

    public function setPermission(string $role, string $permission, bool $value): void
    {
        if ($value) {
            $this->permissions[$permission]->addHolder($role);
        } else {
            $this->permissions[$permission]->removeHolder($role);
        }
        $this->update();
    }

    public function getFlag(string $flag): bool
    {
        return $this->flags[$flag]->getValue();
    }

    public function setFlag(string $flag, bool $value): void
    {
        $this->flags[$flag]->setValue($value);
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
        return $this->relationWish[$faction->getId()] ?? Relations::NONE;
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
        return $this->relations[$faction->getId()] ?? Relations::NONE;
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
            case Relations::ALLY:
            case Relations::TRUCE:
                if ($faction->getRelation($this) !== Relations::NONE) $faction->revokeRelation($faction);
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
            if ($relation === Relations::ALLY) {
                $allies[] = FactionsManager::getInstance()->getFaction($id);
            }
        }
        return $allies;
    }

    /**
     * @return Faction[]
     */
    public function getEnemies(): array
    {
        $allies = [];
        foreach ($this->relations as $id => $relation) {
            if ($relation === Relations::ENEMY) {
                $allies[] = FactionsManager::getInstance()->getFaction($id);
            }
        }
        return $allies;
    }

    public function isAllied(Faction $faction): bool
    {
        return ($this->relations[$faction->getId()] ?? Relations::NONE) === Relations::ALLY;
    }

    public function isTruced(Faction $faction): bool
    {
        return ($this->relations[$faction->getId()] ?? Relations::NONE) === Relations::TRUCE;
    }

    public function isEnemy(Faction $faction): bool
    {
        return ($this->relations[$faction->getId()] ?? Relations::NONE) === Relations::ENEMY;
    }

    public function disband(): void
    {
        foreach ($this->getMembers() as $member) {
            $member->setFaction(null);
            $member->setRole(null);
        }
        foreach (ClaimsManager::getInstance()->getFactionClaims($this) as $claim) {
            ClaimsManager::getInstance()->deleteClaim($claim);
        }
        foreach ($this->relations as $id => $relation) {
            if ($relation === Relations::ALLY || $relation === Relations::TRUCE) {
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
            "flags" => json_encode($this->flags),
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