<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class PermissionSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getRole() !== Faction::ROLE_LEADER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-leader");
            return;
        }
        if (!in_array($args["permission"], Faction::PERMISSIONS)) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.permission.permission-not-found");
            return;
        }
        if (!isset(Faction::ROLES[$args["role"]])) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.permission.role-not-found");
            return;
        }
        $value = $args["value"] ?? !$faction->getPermission($args["role"], $args["permission"]);
        $faction->setPermission($args["role"], $args["permission"], $value);
        LanguageManager::getInstance()->sendMessage($sender, "commands.permission.success", ["{PERMISSION}" => $args["permission"], "{ROLE}" => $args["role"], "{TOGGLED}" => $value ? "enabled" : "disabled"]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("role"));
        $this->registerArgument(1, new RawStringArgument("permission"));
        $this->registerArgument(2, new BooleanArgument("value", true));
    }
}