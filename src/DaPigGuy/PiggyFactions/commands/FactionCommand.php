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
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Vecnavium\FormsUI\SimpleForm;

class FactionCommand extends BaseCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($this->getOwningPlugin()->areFormsEnabled() && $sender instanceof Player) {
            $subcommands = array_filter($this->getSubCommands(), function (BaseSubCommand $subCommand, string $alias) use ($sender): bool {
                return $subCommand->getName() === $alias && count(array_filter($subCommand->getPermissions(), $sender->hasPermission(...))) > 0;
            }, ARRAY_FILTER_USE_BOTH);
            $form = new SimpleForm(function (Player $player, ?int $data) use ($subcommands): void {
                if ($data !== null) {
                    $subcommand = $subcommands[array_keys($subcommands)[$data]];
                    $subcommand->onRun($player, $subcommand->getName(), []);
                }
            });
            $form->setTitle($this->getOwningPlugin()->getLanguageManager()->getMessage($this->getOwningPlugin()->getLanguageManager()->getPlayerLanguage($sender), "forms.title"));
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
            new AddPowerSubCommand($this->getOwningPlugin(), "addpower", "Add player power"),
            new AdminSubCommand($this->getOwningPlugin(), "admin", "Toggle admin mode"),
            new ChatSubCommand($this->getOwningPlugin(), ChatTypes::ALLY, "allychat", "Toggle ally chat", ["ac"]),
            new AllySubCommand($this->getOwningPlugin(), "ally", "Ally with other factions"),
            new BanSubCommand($this->getOwningPlugin(), "ban", "Ban a member from your faction"),
            new ChatSubCommand($this->getOwningPlugin(), ChatTypes::FACTION, "chat", "Toggle faction chat", ["c"]),
            new ClaimSubCommand($this->getOwningPlugin(), "claim", "Claim a chunk"),
            new CreateSubCommand($this->getOwningPlugin(), "create", "Create a faction"),
            new DebugSubCommand($this->getOwningPlugin(), "debug", "Dumps information for debugging"),
            new DemoteSubCommand($this->getOwningPlugin(), "demote", "Demote a faction member"),
            new DescriptionSubCommand($this->getOwningPlugin(), "description", "Set faction description", ["desc"]),
            new DisbandSubCommand($this->getOwningPlugin(), "disband", "Disband your faction"),
            new EnemySubCommand($this->getOwningPlugin(), "enemy", "Mark faction as an enemy"),
            new FlagSubCommand($this->getOwningPlugin(), "flag", "Manage faction flags"),
            new FlySubCommand($this->getOwningPlugin(), "fly", "Fly within faction territories"),
            new HelpSubCommand($this->getOwningPlugin(), $this, "help", "Display command information"),
            new HomeSubCommand($this->getOwningPlugin(), "home", "Teleport to faction home"),
            new InfoSubCommand($this->getOwningPlugin(), "info", "Display faction info", ["who"]),
            new InviteSubCommand($this->getOwningPlugin(), "invite", "Invite a player to your faction"),
            new JoinSubCommand($this->getOwningPlugin(), "join", "Join a faction"),
            new KickSubCommand($this->getOwningPlugin(), "kick", "Kick a member from your faction"),
            new LanguageSubCommand($this->getOwningPlugin(), "language", "Change personal language for PiggyFactions", ["lang"]),
            new LeaderSubCommand($this->getOwningPlugin(), "leader", "Transfer leadership of your faction"),
            new LeaveSubCommand($this->getOwningPlugin(), "leave", "Leave your faction"),
            new LogsSubCommand($this->getOwningPlugin(), "logs", "View your Factions logs", ["log"]),
            new MapSubCommand($this->getOwningPlugin(), "map", "View map of area"),
            new MotdSubCommand($this->getOwningPlugin(), "motd", "Set faction MOTD"),
            new NameSubCommand($this->getOwningPlugin(), "name", "Rename your faction"),
            new NeutralSubCommand($this->getOwningPlugin(), "neutral", "Reset relation with another faction"),
            new PermissionSubCommand($this->getOwningPlugin(), "permission", "Set faction role permissions", ["perms"]),
            new PlayerSubCommand($this->getOwningPlugin(), "player", "Display player info", ["p"]),
            new PowerBoostSubCommand($this->getOwningPlugin(), "powerboost", "Increases max power"),
            new PromoteSubCommand($this->getOwningPlugin(), "promote", "Promote a faction member"),
            new SeeChunkSubCommand($this->getOwningPlugin(), "seechunk", "Toggle chunk visualizer", ["sc"]),
            new SetHomeSubCommand($this->getOwningPlugin(), "sethome", "Set faction home"),
            new SetPowerSubCommand($this->getOwningPlugin(), "powerboost", "Set player power"),
            new TopSubCommand($this->getOwningPlugin(), "top", "Display top factions", ["list"]),
            new TruceSubCommand($this->getOwningPlugin(), "truce", "Truce with other factions"),
            new UnallySubCommand($this->getOwningPlugin(), "unally", "End faction alliance"),
            new UnbanSubCommand($this->getOwningPlugin(), "unban", "Unban a member from your faction"),
            new UnclaimSubCommand($this->getOwningPlugin(), "unclaim", "Unclaim a chunk"),
            new UnsetHomeSubCommand($this->getOwningPlugin(), "unsethome", "Unset faction home", ["delhome"]),
            new VersionSubCommand($this->getOwningPlugin(), "version", "Display version & credits for PiggyFactions", ["v", "ver"]),
        ];

        $bank_commands = [
            new DepositSubCommand($this->getOwningPlugin(), "deposit", "Deposit money into faction bank"),
            new MoneySubCommand($this->getOwningPlugin(), "money", "View faction bank balance"),
            new WithdrawSubCommand($this->getOwningPlugin(), "withdraw", "Withdraw money from faction bank")
        ];

        foreach ($commands as $command) {
            if (!in_array($command->getName(), $this->getOwningPlugin()->getConfig()->getNested("commands.disabled", []))){
                $this->registerSubCommand($command);
            }
        }

        if ($this->getOwningPlugin()->isFactionBankEnabled()) {
            foreach ($bank_commands as $command) {
                $this->registerSubCommand($command);
            }
        }
    }
}
