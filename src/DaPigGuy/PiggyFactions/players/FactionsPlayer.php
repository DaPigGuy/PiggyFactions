<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\players;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\utils\ChatTypes;
use pocketmine\utils\UUID;

class FactionsPlayer
{
    /** @var UUID */
    private $uuid;
    /** @var string */
    private $username;
    /** @var ?string */
    private $faction;
    /** @var ?string */
    private $role;
    /** @var float */
    private $power;

    /** @var bool */
    private $canSeeChunks = false;
    /** @var bool */
    private $isAutoClaiming = false;
    /** @var bool */
    private $flying = false;

    /** @var string */
    private $chat = ChatTypes::ALL;

    /** @var bool */
    private $adminMode = false;

    public function __construct(UUID $uuid, string $username, ?string $faction, ?string $role, float $power)
    {

        $this->uuid = $uuid;
        $this->username = $username;
        $this->faction = $faction;
        $this->role = $role;
        $this->power = $power;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
        $this->update();
    }

    public function getFaction(): ?Faction
    {
        return $this->faction === null ? null : FactionsManager::getInstance()->getFaction($this->faction);
    }

    public function setFaction(?Faction $faction): void
    {
        $this->faction = $faction === null ? null : $faction->getId();
        $this->update();
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
        $this->update();
    }

    public function getPower(): float
    {
        return $this->power;
    }

    public function setPower(float $power): void
    {
        $this->power = $power;
        if ($this->power < ($min = PiggyFactions::getInstance()->getConfig()->getNested("factions.power.min", 0))) $this->power = $min;
        if ($this->power > ($max = PiggyFactions::getInstance()->getConfig()->getNested("factions.power.max", 10))) $this->power = $max;
        $this->update();
    }

    public function canSeeChunks(): bool
    {
        return $this->canSeeChunks;
    }

    public function setCanSeeChunks(bool $value): void
    {
        $this->canSeeChunks = $value;
    }

    public function isAutoClaiming(): bool
    {
        return $this->isAutoClaiming;
    }

    public function setAutoClaiming(bool $value): void
    {
        $this->isAutoClaiming = $value;
    }

    public function isFlying(): bool
    {
        return $this->flying;
    }

    public function setFlying(bool $value): void
    {
        $this->flying = $value;
    }

    public function getCurrentChat(): string
    {
        return $this->chat;
    }

    public function setCurrentChat(string $chat): void
    {
        $this->chat = $chat;
    }

    public function isInAdminMode(): bool
    {
        return $this->adminMode;
    }

    public function setInAdminMode(bool $value): void
    {
        $this->adminMode = $value;
    }

    public function update(): void
    {
        PiggyFactions::getInstance()->getDatabase()->executeChange("piggyfactions.players.update", [
            "uuid" => $this->uuid->toString(),
            "username" => $this->username,
            "faction" => $this->faction,
            "role" => $this->role,
            "power" => $this->power
        ]);
    }
}