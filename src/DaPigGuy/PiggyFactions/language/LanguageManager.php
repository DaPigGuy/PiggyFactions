<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\language;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use ReflectionClass;

class LanguageManager
{
    const DEFAULT_LANGUAGE = "english";
    const LANGUAGES = [
        "en_US" => "english",
        "en_GB" => "english"
    ];

    /** @var self */
    private static $instance;

    /** @var PiggyFactions */
    private $plugin;

    /** @var Config[] */
    private $messages;
    /** @var array */
    private $playerLanguage;

    /** @var array */
    private $colorTags;

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;

        $this->plugin = $plugin;
        foreach (self::LANGUAGES as $language) {
            $file = "languages/" . $language . ".yml";
            $plugin->saveResource($file);

            $this->messages[$language] = new Config($plugin->getDataFolder() . $file);
        }
        foreach ((new ReflectionClass(TextFormat::class))->getConstants() as $color => $code) {
            $this->colorTags["{" . $color . "}"] = $code;
        }
    }

    public static function getInstance(): LanguageManager
    {
        return self::$instance;
    }

    public function getMessage(string $language, string $message, array $extraTags = []): string
    {
        $message = $this->messages[$language]->getNested($message, $this->messages[self::DEFAULT_LANGUAGE]->getNested($message, $message));
        $message = $this->translateColorTags($message);
        $message = str_replace(array_keys($extraTags), $extraTags, $message);
        return $message;
    }

    public function sendMessage(CommandSender $commandSender, string $message, array $extraTags = []): void
    {
        $language = $commandSender instanceof Player ? $this->getPlayerLanguage($commandSender) : self::DEFAULT_LANGUAGE;
        $message = $this->getMessage($language, $message, $extraTags);
        if ($commandSender instanceof Player) {
            $faction = $this->plugin->getPlayerManager()->getPlayerFaction($commandSender->getUniqueId());
            if ($faction instanceof Faction) {
                $message = str_replace("{FACTION}", $faction->getName(), $message);
            }
        }
        $commandSender->sendMessage($message);
    }

    public function getColorFor(Player $player, Faction $faction): string
    {
        $playerFaction = PlayerManager::getInstance()->getPlayerFaction($player->getUniqueId());
        $relation = "neutral";
        if ($faction->getFlag(Flag::WARZONE)) {
            $relation = "warzone";
        } elseif ($faction->getFlag(Flag::SAFEZONE)) {
            $relation = "safezone";
        } elseif ($playerFaction === null) {
            $relation = "neutral";
        } elseif ($playerFaction->getId() === $faction->getId()) {
            $relation = "member";
        } elseif ($playerFaction->isAllied($faction)) {
            $relation = "ally";
        } else if ($playerFaction->isTruced($faction)) {
            $relation = "truce";
        } elseif ($playerFaction->isEnemy($faction)) {
            $relation = "enemy";
        }
        return $this->translateColorTags($this->plugin->getConfig()->getNested("symbols.colors.relations." . $relation, ""));
    }

    public function translateColorTags(string $message): string
    {
        return str_replace(array_keys($this->colorTags), $this->colorTags, TextFormat::colorize($message));
    }

    public function getPlayerLanguage(Player $player): string
    {
        return $this->playerLanguage[$player->getName()] ?? self::DEFAULT_LANGUAGE;
    }

    public function setPlayerLanguage(Player $player, string $language): void
    {
        $this->playerLanguage[$player->getName()] = $language;
    }
}