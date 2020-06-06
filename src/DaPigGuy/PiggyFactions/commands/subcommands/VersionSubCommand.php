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
            "Taylarity" => ["Chinese (Traditional)"],
            "SalmonDE" => ["German"],
            "yuriiscute53925" => ["Serbian"],
            "UnEnanoMas" => ["Spanish"]
        ];

        $poggitBuildInfo = $this->plugin->getPoggitBuildInfo();
        $specificVersion = "";
        if ($poggitBuildInfo->isRunningFromSource()) {
            $specificVersion = "(from source)";
        } elseif ($poggitBuildInfo->isRunningPoggitPhar()) {
            $metadata = $poggitBuildInfo->getPoggitPharMetadata();
            $specificVersion = "(poggit build #" . $metadata["projectBuildNumber"] . ")";
        } elseif ($poggitBuildInfo->isRunningPhar()) {
            $specificVersion = "(from custom compiled phar)";
        }

        $sender->sendMessage("PiggyFactions version " . TextFormat::GOLD . $this->plugin->getDescription()->getVersion() . TextFormat::RESET . " " . $specificVersion . TextFormat::EOL .
            TextFormat::GREEN . "PiggyFactions is a modern factions plugin developed by DaPigGuy (MCPEPIG) and Aericio." . TextFormat::EOL .
            TextFormat::GREEN . "Translations provided by " . implode(", ", array_map(function (string $translator, array $languages): string {
                return $translator . " (" . implode(", ", $languages) . ")";
            }, array_keys($translators), $translators)) . TextFormat::EOL .
            TextFormat::GRAY . "Copyright 2020 DaPigGuy; Licensed under the Apache License.");
    }
}