<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\args\StringEnumArgument;
use CortexPE\Commando\BaseSubCommand;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\permissions\PermissionFactory;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use DaPigGuy\PiggyFactions\utils\PiggyArgument;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class FactionSubCommand extends BaseSubCommand
{
    /** @var PiggyFactions */
    protected $plugin;
    /** @var bool */
    protected $requiresPlayer = true;
    /** @var bool */
    protected $requiresFaction = true;

    public function __construct(PiggyFactions $plugin, string $name, string $description = "", array $aliases = [])
    {
        $this->plugin = $plugin;
        $this->setPermission("piggyfactions.command.faction." . $name);
        parent::__construct($name, $description, $aliases);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player && $this->requiresPlayer) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game.");
            return;
        }

        $member = $sender instanceof Player ? PlayerManager::getInstance()->getPlayer($sender->getUniqueId()) : null;
        $faction = $member === null ? null : $member->getFaction();

        if ($this->requiresFaction && $this->requiresPlayer) {
            if ($faction === null) {
                LanguageManager::getInstance()->sendMessage($sender, "commands.not-in-faction");
                return;
            }
            if (PermissionFactory::getPermission($this->getName()) !== null) {
                if (!$faction->hasPermission($member, $this->getName())) {
                    LanguageManager::getInstance()->sendMessage($sender, "commands.no-permission");
                    return;
                }
            }
        }

        foreach ($this->getArgumentList() as $arguments) {
            /** @var PiggyArgument $argument */
            foreach ($arguments as $argument) {
                if (!$argument->getWrappedArgument()->isOptional() && !isset($args[$argument->getName()])) {
                    if ($sender instanceof Player) {
                        $this->onFormRun($sender, $faction, $member, $aliasUsed, $args);
                    } else {
                        $this->sendUsage();
                    }
                    return;
                }
            }
        }

        if ($this->requiresPlayer) {
            $this->onNormalRun($sender, $faction, $member, $aliasUsed, $args);
        } else {
            $this->onBasicRun($sender, $args);
        }
    }

    public function onBasicRun(CommandSender $sender, array $args): void
    {
    }

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
    }

    public function onFormRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $commandArguments = [];
        $enums = [];
        foreach ($this->getArgumentList() as $position => $arguments) {
            /** @var PiggyArgument $argument */
            foreach ($arguments as $argument) {
                $argument = $argument->getWrappedArgument();
                $commandArguments[$position] = $argument;
                if ($argument instanceof StringEnumArgument) $enums[$position] = $argument->getEnumValues();
            }
        }

        $form = new CustomForm(function (Player $player, ?array $data) use ($enums): void {
            if ($data !== null) {
                $args = [];
                foreach ($this->getArgumentList() as $position => $arguments) {
                    /** @var PiggyArgument $argument */
                    foreach ($arguments as $argument) {
                        $args[$argument->getName()] = (($enum = $argument->getWrappedArgument()) instanceof StringEnumArgument && !$enum instanceof BooleanArgument) ? $enums[$position][$data[$position]] : $data[$position];
                    }
                }
                $this->onRun($player, $this->getName(), $args);
            }
        });
        $form->setTitle("/f " . $this->getName());
        foreach ($commandArguments as $argument) {
            if ($argument instanceof BooleanArgument) {
                $form->addToggle(ucfirst($argument->getName()), $args[$argument->getName()] ?? null);
            } elseif ($argument instanceof StringEnumArgument) {
                $form->addDropdown(ucfirst($argument->getName()), $argument->getEnumValues(), (int)(array_search($args[$argument->getName()] ?? "", $argument->getEnumValues())));
            } else {
                $form->addInput(ucfirst($argument->getName()), "", $args[$argument->getName()] ?? null);
            }
        }
        $sender->sendForm($form);
    }

    public function registerArgument(int $position, BaseArgument $argument): void
    {
        parent::registerArgument($position, new PiggyArgument($argument));
    }

    protected function prepare(): void
    {
    }
}