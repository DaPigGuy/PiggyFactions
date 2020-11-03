<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use const pocketmine\GIT_COMMIT;

class DebugSubCommand extends FactionSubCommand
{
    /** @var bool */
    protected $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        if ($sender instanceof ConsoleCommandSender) {
            $plugin = $this->plugin;
            $config = $plugin->getConfig();
            $plugin->getLogger()->info("Showing debug info..." . PHP_EOL .
                "-- PLUGIN INFO --" . PHP_EOL .
                "NAME: " . $plugin->getName() . PHP_EOL .
                "VERSION: " . $plugin->getDescription()->getVersion() . PHP_EOL .
                "VIA: " . $plugin->getPoggitBuildInfo()->getSpecificVersion() . PHP_EOL .
                "DATABASE: " . $config->getNested("database.type") . PHP_EOL .
                "ECONOMY: " . ($config->getNested("economy.enabled") ? $config->getNested("economy.economyapi") : "DISABLED") . PHP_EOL .
                "FORMS: " . ($config->get("forms") ? "ENABLED" : "DISABLED") . PHP_EOL .
                "-- PMMP INFO --" . PHP_EOL .
                "NAME: " . $plugin->getServer()->getName() . PHP_EOL .
                "VERSION: " . $plugin->getServer()->getApiVersion() . PHP_EOL .
                "GIT COMMIT: " . GIT_COMMIT . PHP_EOL .
                "-- MC INFO --" . PHP_EOL .
                "VERSION: " . ProtocolInfo::MINECRAFT_VERSION_NETWORK . PHP_EOL .
                "PROTOCOL: " . ProtocolInfo::CURRENT_PROTOCOL . PHP_EOL .
                "-- SYSTEM INFO --" . PHP_EOL .
                "OS TYPE: " . PHP_OS . ", " . Utils::getOS() . PHP_EOL .
                "OS VERSION: " . php_uname("v") . PHP_EOL .
                "PHP VERSION: " . PHP_VERSION . PHP_EOL .
                "-- PLUGINS --" . PHP_EOL .
                implode(", ", array_map(function (Plugin $plugin): string {
                    return $plugin->getDescription()->getFullName();
                }, $plugin->getServer()->getPluginManager()->getPlugins()))
            );
        } else {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in the console.");
        }
    }
}