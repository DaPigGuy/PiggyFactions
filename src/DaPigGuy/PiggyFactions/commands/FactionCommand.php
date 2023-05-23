<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\admin\AddPowerSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\admin\AdminSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\admin\powerboost\PowerBoostSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\admin\SetPowerSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\ChatSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\claim\ClaimSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\MapSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\SeeChunkSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim\UnclaimSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\DebugSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\flags\FlagSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\FlySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\HelpSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\homes\HomeSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\homes\SetHomeSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\homes\UnsetHomeSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\InfoSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\JoinSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\LanguageSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\LeaveSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\BanSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\CreateSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\DescriptionSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\DisbandSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\InviteSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\KickSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\LogsSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\MotdSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\NameSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\UnbanSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\money\DepositSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\money\MoneySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\money\WithdrawSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\PlayerSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\AllySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\EnemySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\NeutralSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\TruceSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\UnallySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\DemoteSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\LeaderSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\PermissionSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\PromoteSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\TopSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\VersionSubCommand;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\utils\ChatTypes;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FactionCommand extends BaseCommand
{
    /** @var PiggyFactions */
    protected $plugin;

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($this->plugin->areFormsEnabled() && $sender instanceof Player) {
            $subcommands = array_filter($this->getSubCommands(), function (BaseSubCommand $subCommand, string $alias) use ($sender): bool {
                return $subCommand->getName() === $alias && $sender->hasPermission($subCommand->getPermission());
            }, ARRAY_FILTER_USE_BOTH);
            $form = new SimpleForm(function (Player $player, ?int $data) use ($subcommands): void {
                if ($data !== null) {
                    $subcommand = $subcommands[array_keys($subcommands)[$data]];
                    $subcommand->onRun($player, $subcommand->getName(), []);
                }
            });
            $form->setTitle($this->plugin->getLanguageManager()->getMessage($this->plugin->getLanguageManager()->getPlayerLanguage($sender), "forms.title"));
            foreach ($subcommands as $key => $subcommand) {
                $form->addButton(ucfirst($subcommand->getName()));
            }
            $sender->sendForm($form);
            return;
        }
        $this->sendUsage();
    }

    protected function prepare(): void
    {
        $this->setPermission("piggyfactions.command.faction.use");

        $commands = [
            new AddPowerSubCommand($this->plugin, "addpower", "Add player power"),
            new AdminSubCommand($this->plugin, "admin", "Toggle admin mode"),
            new ChatSubCommand($this->plugin, ChatTypes::ALLY, "allychat", "Toggle ally chat", ["ac"]),
            new AllySubCommand($this->plugin, "ally", "Ally with other factions"),
            new BanSubCommand($this->plugin, "ban", "Ban a member from your faction"),
            new ChatSubCommand($this->plugin, ChatTypes::FACTION, "chat", "Toggle faction chat", ["c"]),
            new ClaimSubCommand($this->plugin, "claim", "Claim a chunk"),
            new CreateSubCommand($this->plugin, "create", "Create a faction"),
            new DebugSubCommand($this->plugin, "debug", "Dumps information for debugging"),
            new DemoteSubCommand($this->plugin, "demote", "Demote a faction member"),
            new DescriptionSubCommand($this->plugin, "description", "Set faction description", ["desc"]),
            new DisbandSubCommand($this->plugin, "disband", "Disband your faction"),
            new EnemySubCommand($this->plugin, "enemy", "Mark faction as an enemy"),
            new FlagSubCommand($this->plugin, "flag", "Manage faction flags"),
            new FlySubCommand($this->plugin, "fly", "Fly within faction territories"),
            new HelpSubCommand($this->plugin, $this, "help", "Display command information"),
            new HomeSubCommand($this->plugin, "home", "Teleport to faction home"),
            new InfoSubCommand($this->plugin, "info", "Display faction info", ["who"]),
            new InviteSubCommand($this->plugin, "invite", "Invite a player to your faction"),
            new JoinSubCommand($this->plugin, "join", "Join a faction"),
            new KickSubCommand($this->plugin, "kick", "Kick a member from your faction"),
            new LanguageSubCommand($this->plugin, "language", "Change personal language for PiggyFactions", ["lang"]),
            new LeaderSubCommand($this->plugin, "leader", "Transfer leadership of your faction"),
            new LeaveSubCommand($this->plugin, "leave", "Leave your faction"),
            new LogsSubCommand($this->plugin, "logs", "View your Factions logs", ["log"]),
            new MapSubCommand($this->plugin, "map", "View map of area"),
            new MotdSubCommand($this->plugin, "motd", "Set faction MOTD"),
            new NameSubCommand($this->plugin, "name", "Rename your faction"),
            new NeutralSubCommand($this->plugin, "neutral", "Reset relation with another faction"),
            new PermissionSubCommand($this->plugin, "permission", "Set faction role permissions", ["perms"]),
            new PlayerSubCommand($this->plugin, "player", "Display player info", ["p"]),
            new PowerBoostSubCommand($this->plugin, "powerboost", "Increases max power"),
            new PromoteSubCommand($this->plugin, "promote", "Promote a faction member"),
            new SeeChunkSubCommand($this->plugin, "seechunk", "Toggle chunk visualizer", ["sc"]),
            new SetHomeSubCommand($this->plugin, "sethome", "Set faction home"),
            new SetPowerSubCommand($this->plugin, "setpower", "Set player power"),
            new TopSubCommand($this->plugin, "top", "Display top factions", ["list"]),
            new TruceSubCommand($this->plugin, "truce", "Truce with other factions"),
            new UnallySubCommand($this->plugin, "unally", "End faction alliance"),
            new UnbanSubCommand($this->plugin, "unban", "Unban a member from your faction"),
            new UnclaimSubCommand($this->plugin, "unclaim", "Unclaim a chunk"),
            new UnsetHomeSubCommand($this->plugin, "unsethome", "Unset faction home", ["delhome"]),
            new VersionSubCommand($this->plugin, "version", "Display version & credits for PiggyFactions", ["v", "ver"]),
        ];

        $bank_commands = [
            new DepositSubCommand($this->plugin, "deposit", "Deposit money into faction bank"),
            new MoneySubCommand($this->plugin, "money", "View faction bank balance"),
            new WithdrawSubCommand($this->plugin, "withdraw", "Withdraw money from faction bank")
        ];

        foreach ($commands as $command) {
            if (!in_array($command->getName(), $this->plugin->getConfig()->getNested("commands.disabled", []))){
                $this->registerSubCommand($command);
            }
        }

        if ($this->plugin->isFactionBankEnabled()) {
            foreach ($bank_commands as $command) {
                $this->registerSubCommand($command);
            }
        }
    }
}