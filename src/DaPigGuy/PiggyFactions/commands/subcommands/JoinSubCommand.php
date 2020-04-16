<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class JoinSubCommand extends FactionSubCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }
        $targetFaction = $this->plugin->getFactionsManager()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction");
            return;
        }
        if (!$targetFaction->hasInvite($sender)) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.join.no-invite", ["{FACTION}" => $targetFaction->getName()]);
            return;
        }
        $faction = $this->plugin->getPlayerManager()->getPlayerFaction($sender->getUniqueId());
        if ($faction !== null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.already-in-faction");
            return;
        }
        $targetFaction->revokeInvite($sender);
        $targetFaction->addMember($sender);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }

}