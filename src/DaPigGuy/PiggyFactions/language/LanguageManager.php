<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\language;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use ReflectionClass;

class LanguageManager
{
    const LANGUAGES = [
        "chinese_simplified",
        "chinese_traditional",
        "english",
        "french",
        "german",
        "indonesian",
        "serbian",
        "spanish"
    ];
    const LOCALE_CODE_TABLE = [
        "en_US" => "english",
        "en_GB" => "english",
        "es_ES" => "spanish",
        "es_MX" => "spanish",
        "de_DE" => "german",
        "fr_FR" => "french",
        "id_ID" => "indonesian",
        "sr_SP" => "serbian",
        "zh_CN" => "chinese_simplified",
        "zh_TW" => "chinese_traditional"
    ];

    private static LanguageManager $instance;
    private PiggyFactions $plugin;

    private string $defaultLanguage;

    /** @var Config[] */
    private array $messages;
    private array $colorTags;

    public function __construct(PiggyFactions $plugin)
    {
        self::$instance = $this;

        $this->plugin = $plugin;

        $defaultLanguage = $plugin->getConfig()->getNested("languages.default", "english");
        if (!in_array($defaultLanguage, self::LANGUAGES)) {
            $this->plugin->getLogger()->warning("Default language '" . $defaultLanguage . "' does not exist, defaulting to english.");
            $defaultLanguage = "english";
        }
        $this->defaultLanguage = $defaultLanguage;

        foreach (self::LANGUAGES as $language) {
            $file = "languages/" . $language . ".yml";
            $plugin->saveResource($file);

            $this->messages[$language] = new Config($plugin->getDataFolder() . $file);
        }
        foreach ((new ReflectionClass(TextFormat::class))->getConstants() as $color => $code) {
            if (is_string($code)) $this->colorTags["{" . $color . "}"] = $code;
        }
    }

    public static function getInstance(): LanguageManager
    {
        return self::$instance;
    }

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    public function getPlayerLanguage(Player $player): string
    {
        $member = $this->plugin->getPlayerManager()->getPlayer($player);
        if ($member === null) return $this->defaultLanguage;
        return $member->getLanguage();
    }

    public function getMessage(string $language, string $message, array $extraTags = []): string
    {
        $message = $this->messages[$language]->getNested($message, $this->messages[$this->defaultLanguage]->getNested($message, $message));
        $message = $this->translateColorTags($message);
        $message = str_replace(array_keys($extraTags), $extraTags, $message);
        return $message;
    }

    public function sendMessage(CommandSender $commandSender, string $message, array $extraTags = []): void
    {
        $language = $commandSender instanceof Player ? $this->getPlayerLanguage($commandSender) : $this->defaultLanguage;
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
        $playerFaction = $this->plugin->getPlayerManager()->getPlayerFaction($player->getUniqueId());
        $relation = "neutral";
        if ($faction->getFlag(Flag::WARZONE)) {
            $relation = "warzone";
        } elseif ($faction->getFlag(Flag::SAFEZONE)) {
            $relation = "safezone";
        } elseif ($playerFaction?->getId() === $faction->getId()) {
            $relation = "member";
        } elseif ($playerFaction?->isAllied($faction)) {
            $relation = "ally";
        } else if ($playerFaction?->isTruced($faction)) {
            $relation = "truce";
        } elseif ($playerFaction?->isEnemy($faction)) {
            $relation = "enemy";
        }
        return $this->translateColorTags($this->plugin->getConfig()->getNested("symbols.colors.relations." . $relation, ""));
    }

    public function translateColorTags(string $message): string
    {
        return str_replace(array_keys($this->colorTags), $this->colorTags, TextFormat::colorize($message));
    }
}