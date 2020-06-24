<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class VersionSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $translators = [
            "Aericio" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "MrAshshiddiq" => ["Indonesian"],
            "prprprprprprpr" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "SalmonDE" => ["German"],
            "SillierShark195" => ["Indonesian"],
            "Taylarity" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "TGPNG" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "UnEnanoMas" => ["Spanish"],
            "yuriiscute53925" => ["Serbian"],
        ];

        $poggitBuildInfo = $this->plugin->getPoggitBuildInfo();
        $specificVersion = "";
        if ($poggitBuildInfo->isRunningFromSource()) {
            $specificVersion = "(source)";
        } elseif ($poggitBuildInfo->isRunningPoggitPhar()) {
            $metadata = $poggitBuildInfo->getPoggitPharMetadata();
            $specificVersion = "(build #" . $metadata["projectBuildNumber"] . ")";
        } elseif ($poggitBuildInfo->isRunningPhar()) {
            $specificVersion = "(custom phar)";
        }

        $sender->sendMessage(TextFormat::GOLD . "____________.[" . TextFormat::DARK_GREEN . "PiggyFactions " . TextFormat::GREEN . "v" . $this->plugin->getDescription()->getVersion() . " " . $specificVersion . TextFormat::GOLD . "].____________" . TextFormat::EOL .
            TextFormat::GOLD . "PiggyFactions is a modern factions plugin developed by " . TextFormat::YELLOW . "DaPigGuy (MCPEPIG) " . TextFormat::GOLD . "and " . TextFormat::YELLOW . "Aericio" . TextFormat::GOLD . "."  . TextFormat::EOL .
            TextFormat::GOLD . "Translations provided by " . implode(", ", array_map(function (string $translator, array $languages): string {
                return TextFormat::YELLOW . $translator . " (" . implode(", ", $languages) . ")" . TextFormat::GOLD;
            }, array_keys($translators), $translators)) . TextFormat::EOL .
            TextFormat::GRAY . "Copyright 2020 DaPigGuy; Licensed under the Apache License.");
    }
}