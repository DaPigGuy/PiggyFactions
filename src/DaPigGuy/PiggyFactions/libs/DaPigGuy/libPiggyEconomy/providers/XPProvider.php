<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\libs\DaPigGuy\libPiggyEconomy\providers;

use pocketmine\entity\utils\ExperienceUtils;
use pocketmine\player\Player;

class XPProvider extends EconomyProvider
{
    public function getMonetaryUnit(): string
    {
        return "Levels";
    }

    public function getMoney(Player $player, callable $callback): void
    {
        $callback($player->getXpManager()->getXpLevel() + $player->getXpManager()->getXpProgress());
    }

    public function giveMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $levels = (int)floor($amount);
        $ret = $player->getXpManager()->addXpLevels($levels) && $player->getXpManager()->addXp((int)(ExperienceUtils::getXpToCompleteLevel($player->getXpManager()->getXpLevel()) * ($amount - $levels)));
        if ($callback) $callback($ret);
    }

    public function takeMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $this->giveMoney($player, -$amount, $callback);
    }

    public function setMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $levels = (int)floor($amount);
        $ret = $player->getXpManager()->setXpLevel($levels) && $player->getXpManager()->setXpProgress($amount - $levels);
        if ($callback) $callback($ret);
    }
}