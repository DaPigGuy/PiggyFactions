<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\SubCommandCollision;
use DaPigGuy\PiggyFactions\chat\ChatManager;
use DaPigGuy\PiggyFactions\commands\subcommands\admin\AdminSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\ChatSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\ClaimSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\MapSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\SeeChunkSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\claims\UnclaimSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\flags\FlagSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\homes\HomeSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\homes\SetHomeSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\InfoSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\JoinSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\LeaveSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\CreateSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\DescriptionSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\DisbandSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\InviteSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\KickSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\MotdSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\management\NameSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\AllySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\EnemySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\TruceSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\relations\UnallySubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\DemoteSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\LeaderSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\PermissionSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\roles\PromoteSubCommand;
use DaPigGuy\PiggyFactions\commands\subcommands\TopSubCommand;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;

class FactionCommand extends BaseCommand
{
    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin, string $name, string $description = "", array $aliases = [])
    {
        $this->plugin = $plugin;
        parent::__construct($name, $description, $aliases);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        //TODO: Form UI
        $this->sendUsage();
    }

    /**
     * @throws SubCommandCollision
     */
    protected function prepare(): void
    {
        $this->setPermission("piggyfactions.command.faction.use");
        $this->registerSubCommand(new AdminSubCommand($this->plugin, "admin", "Toggle admin mode"));
        $this->registerSubCommand(new AllySubCommand($this->plugin, "ally", "Ally with other factions"));
        $this->registerSubCommand(new ChatSubCommand($this->plugin, ChatManager::ALLY_CHAT, "allychat", "Toggle ally chat", ["ac"]));
        $this->registerSubCommand(new ChatSubCommand($this->plugin, ChatManager::FACTION_CHAT, "chat", "Toggle faction chat", ["c"]));
        $this->registerSubCommand(new ClaimSubCommand($this->plugin, "claim", "Claim a chunk"));
        $this->registerSubCommand(new CreateSubCommand($this->plugin, "create", "Create a faction"));
        $this->registerSubCommand(new DescriptionSubCommand($this->plugin, "description", "Set faction description", ["desc"]));
        $this->registerSubCommand(new DemoteSubCommand($this->plugin, "demote", "Demote a faction member"));
        $this->registerSubCommand(new DisbandSubCommand($this->plugin, "disband", "Disband your faction"));
        $this->registerSubCommand(new EnemySubCommand($this->plugin, "enemy", "Mark faction as an enemy"));
        $this->registerSubCommand(new FlagSubCommand($this->plugin, "flag", "Manage faction flags"));
        $this->registerSubCommand(new HomeSubCommand($this->plugin, "home", "Teleport to faction home"));
        $this->registerSubCommand(new InfoSubCommand($this->plugin, "info", "Display faction info", ["who"]));
        $this->registerSubCommand(new InviteSubCommand($this->plugin, "invite", "Invite a player to your faction"));
        $this->registerSubCommand(new JoinSubCommand($this->plugin, "join", "Join a faction"));
        $this->registerSubCommand(new KickSubCommand($this->plugin, "kick", "Kick a member from your faction"));
        $this->registerSubCommand(new LeaderSubCommand($this->plugin, "leader", "Transfer leadership of your faction"));
        $this->registerSubCommand(new LeaveSubCommand($this->plugin, "leave", "Leave your faction"));
        $this->registerSubCommand(new MapSubCommand($this->plugin, "map", "View map of area"));
        $this->registerSubCommand(new MotdSubCommand($this->plugin, "motd", "Set faction MOTD"));
        $this->registerSubCommand(new NameSubCommand($this->plugin, "name", "Rename your faction"));
        $this->registerSubCommand(new PermissionSubCommand($this->plugin, "permission", "Set faction role permissions", ["perms"]));
        $this->registerSubCommand(new PromoteSubCommand($this->plugin, "promote", "Promote a faction member"));
        $this->registerSubCommand(new SeeChunkSubCommand($this->plugin, "seechunk", "Toggle chunk visualizer", ["sc"]));
        $this->registerSubCommand(new SetHomeSubCommand($this->plugin, "sethome", "Set faction home"));
        $this->registerSubCommand(new TopSubCommand($this->plugin, "top", "Display top factions", ["list"]));
        $this->registerSubCommand(new TruceSubCommand($this->plugin, "truce", "Truce with other factions"));
        $this->registerSubCommand(new UnallySubCommand($this->plugin, "unally", "End faction alliance"));
        $this->registerSubCommand(new UnclaimSubCommand($this->plugin, "unclaim", "Unclaim a chunk"));
    }
}