<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\players;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\utils\ChatTypes;
use pocketmine\player\Player;
use pocketmine\Server;
use Ramsey\Uuid\UuidInterface;

class FactionsPlayer
{
    private bool $canSeeChunks = false;
    private bool $isAutoClaiming = false;
    private bool $isAutoUnclaiming = false;
    private bool $flying = false;

    private string $chat = ChatTypes::ALL;

    private bool $adminMode = false;

    public function __construct(private UuidInterface $uuid, private string $username, private ?string $faction, private ?string $role, private float $power, private float $powerboost, private string $language)
    {
    }

    public function getUuid(): UuidInterface
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
        $this->faction = $faction?->getId();
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

    public function getMaxPower(): float
    {
        return PiggyFactions::getInstance()->getConfig()->getNested("factions.power.max") + $this->powerboost;
    }

    public function setPower(float $power): void
    {
        $this->power = $power;
        if ($this->power < ($min = PiggyFactions::getInstance()->getConfig()->getNested("factions.power.min", 0))) $this->power = $min;
        if ($this->power > ($max = $this->getMaxPower())) $this->power = $max;
        $this->update();
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

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
        $this->update();
    }

    public function sendMessage(string $message, array $extraTags = []): bool
    {
        $player = Server::getInstance()->getPlayerByUUID($this->getUuid());
        if ($player instanceof Player) {
            LanguageManager::getInstance()->sendMessage($player, $message, $extraTags);
            return true;
        }
        return false;
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

    public function isAutoUnclaiming(): bool
    {
        return $this->isAutoUnclaiming;
    }

    public function setAutoUnclaiming(bool $value): void
    {
        $this->isAutoUnclaiming = $value;
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
            "power" => $this->power,
            "powerboost" => $this->powerboost,
            "language" => $this->language
        ]);
    }
}