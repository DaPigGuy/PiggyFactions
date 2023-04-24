<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\libs\DaPigGuy\libPiggyEconomy\providers;

use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;

class EconomySProvider extends EconomyProvider
{
    private EconomyAPI $economyAPI;

    public static function checkDependencies(): bool
    {
        return Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI") !== null;
    }

    public function __construct()
    {
        $this->economyAPI = EconomyAPI::getInstance();
    }

    public function getMonetaryUnit(): string
    {
        return $this->economyAPI->getMonetaryUnit();
    }

    public function getMoney(Player $player, callable $callback): void
    {
        $callback($this->economyAPI->myMoney($player) ?: 0);
    }

    public function giveMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $ret = $this->economyAPI->addMoney($player, $amount);
        if ($callback) $callback($ret === EconomyAPI::RET_SUCCESS);
    }

    public function takeMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $ret = $this->economyAPI->reduceMoney($player, $amount);
        if ($callback) $callback($ret === EconomyAPI::RET_SUCCESS);
    }

    public function setMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $ret = $this->economyAPI->setMoney($player, $amount);
        if ($callback) $callback($ret === EconomyAPI::RET_SUCCESS);
    }
}