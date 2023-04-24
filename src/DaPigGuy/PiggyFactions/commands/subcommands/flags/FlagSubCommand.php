<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\flags;

use DaPigGuy\PiggyFactions\libs\CortexPE\Commando\args\BooleanArgument;
use DaPigGuy\PiggyFactions\commands\arguments\FlagEnumArgument;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\flags\FactionFlagChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\FlagFactory;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class FlagSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if (($flag = FlagFactory::getFlag($args["flag"])) === null) {
            $member->sendMessage("commands.flag.invalid-flag", ["{FLAG}" => $args["flag"]]);
            return;
        }
        if (!$flag->isEditable() && !$member->isInAdminMode()) {
            $member->sendMessage("commands.flag.not-editable", ["{FLAG}" => $args["flag"]]);
            return;
        }

        $ev = new FactionFlagChangeEvent($faction, $member, $args["flag"], $args["value"] ?? !$faction->getFlag($args["flag"]));
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setFlag($args["flag"], $ev->getValue());
        $member->sendMessage("commands.flag.toggled" . ($ev->getValue() ? "" : "-off"), ["{FLAG}" => $args["flag"]]);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new FlagEnumArgument("flag"));
        $this->registerArgument(1, new BooleanArgument("value", true));
    }
}