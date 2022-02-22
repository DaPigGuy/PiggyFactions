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
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Faction
{
    /** @var UuidInterface[] */
    private array $members;
    /** @var FactionPermission[] */
    private array $permissions;
    /** @var Flag[] */
    private array $flags;

    /** @var string[] */
    private array $relationWish;

    /** @var Player[] */
    private array $invitedPlayers;

    public function __construct(private string $id, private string $name, private int $creationTime, private ?string $description, private ?string $motd, array $members, array $permissions, array $flags, private ?Position $home, private ?World $homeWorld, private array $relations, private array $banned, private float $money, private float $powerboost)
    {
        $this->members = array_map(function (string $uuid): UuidInterface {
            return Uuid::fromString($uuid);
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
    }

    public function getId(): string
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

    public function getCreationTime(): int
    {
        return $this->creationTime;
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
        $power = $this->getPowerBoost();
        foreach ($this->getMembers() as $member) {
            $power += $member->getPower();
        }
        return $power;
    }

    public function getMaxPower(): float
    {
        return array_sum(array_map(static function (FactionsPlayer $p): float {
                return $p->getMaxPower();
            }, $this->getMembers())) + $this->powerboost;
    }

    public function getPowerBoost(): float
    {
        return $this->powerboost;
    }

    public function setPowerBoost(float $powerboost): void
    {
        $this->powerboost = $powerboost;
        $this->update();
    }

    /**
     * @return FactionsPlayer[]
     */
    public function getMembers(): array
    {
        return array_map(function (UuidInterface $uuid): FactionsPlayer {
            return PlayerManager::getInstance()->getPlayerByUUID($uuid);
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

    public function broadcastMessage(string $message, array $extraTags = []): void
    {
        foreach ($this->getOnlineMembers() as $player) {
            LanguageManager::getInstance()->sendMessage($player, $message, $extraTags);
        }
    }

    public function getMember(string $member): ?FactionsPlayer
    {
        foreach ($this->getMembers() as $m) {
            if (strtolower($m->getUsername()) === strtolower($member)) return $m;
        }
        return null;
    }

    public function getMemberByUUID(UuidInterface $uuid): ?FactionsPlayer
    {
        foreach ($this->getMembers() as $m) {
            if ($m->getUuid()->equals($uuid)) return $m;
        }
        return null;
    }

    public function addMember(Player $member): void
    {
        $this->members[] = $member->getUniqueId();
        PlayerManager::getInstance()->getPlayer($member)->setFaction($this);
        PlayerManager::getInstance()->getPlayer($member)->setRole(Roles::RECRUIT);
        $this->broadcastMessage("commands.join.joined", ["{PLAYER}" => $member->getName()]);
        $this->update();
    }

    public function removeMember(UuidInterface $uuid): void
    {
        unset($this->members[array_search($uuid, $this->members)]);
        PlayerManager::getInstance()->getPlayerByUUID($uuid)->setFaction(null);
        PlayerManager::getInstance()->getPlayerByUUID($uuid)->setRole(null);
        $this->update();
    }

    public function getLeader(): ?FactionsPlayer
    {
        foreach ($this->members as $member) {
            if (($member = PlayerManager::getInstance()->getPlayerByUUID($member)) !== null && $member->getRole() === Roles::LEADER) {
                return $member;
            }
        }
        return null;
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

    public function getHomeWorld(): ?World
    {
        return $this->homeWorld;
    }

    public function setHome(Position $home): void
    {
        $this->home = $home;
        $this->update();
    }

    public function unsetHome(): void
    {
        $this->home = null;
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
        if ($relation === Relations::NONE) unset($this->relations[$faction->getId()]);
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
                if (($ally = FactionsManager::getInstance()->getFaction($id)) !== null) $allies[] = $ally;
            }
        }
        return $allies;
    }

    /**
     * @return Faction[]
     */
    public function getEnemies(): array
    {
        $enemies = [];
        foreach ($this->relations as $id => $relation) {
            if ($relation === Relations::ENEMY) {
                if (($enemy = FactionsManager::getInstance()->getFaction($id)) !== null) {
                    $enemies[] = $enemy;
                } else {
                    unset($this->relations[$id]);
                    $this->update();
                }
            }
        }
        return $enemies;
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

    public function getBanned(): array
    {
        return $this->banned;
    }

    public function isBanned(UuidInterface $uuid): bool
    {
        return in_array($uuid->toString(), $this->banned);
    }

    public function banPlayer(UuidInterface $uuid): void
    {
        $this->banned[] = $uuid->toString();
        $this->update();
    }

    public function unbanPlayer(UuidInterface $uuid): void
    {
        $key = array_search($uuid->toString(), $this->banned);
        if ($key !== false) unset($this->banned[$key]);
    }

    public function getMoney(): float
    {
        return $this->money;
    }

    public function setMoney(float $money): void
    {
        $this->money = $money;
        $this->update();
    }

    public function addMoney(float $money): void
    {
        $this->money += $money;
        $this->update();
    }

    public function removeMoney(float $money): void
    {
        $this->money -= $money;
        $this->update();
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
                $faction?->revokeRelation($this);
            }
        }
        FactionsManager::getInstance()->deleteFaction($this->getId());
    }

    public function update(): void
    {
        PiggyFactions::getInstance()->getDatabase()->executeChange("piggyfactions.factions.update", [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "motd" => $this->motd,
            "members" => json_encode(array_values(array_map(function (UuidInterface $uuid): string {
                return $uuid->toString();
            }, $this->members))),
            "permissions" => json_encode($this->permissions),
            "flags" => json_encode($this->flags),
            "home" => $this->home === null ? null : json_encode([
                "x" => $this->home->x,
                "y" => $this->home->y,
                "z" => $this->home->z,
                "level" => $this->home->world->getFolderName()
            ]),
            "relations" => json_encode($this->relations),
            "banned" => json_encode($this->banned),
            "money" => $this->money,
            "powerboost" => $this->powerboost
        ]);
    }
}