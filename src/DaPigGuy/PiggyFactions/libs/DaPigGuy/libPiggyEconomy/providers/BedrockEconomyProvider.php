<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\libs\DaPigGuy\libPiggyEconomy\providers;

use cooldogedev\BedrockEconomy\BedrockEconomy;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\api\version\LegacyBEAPI;
use cooldogedev\BedrockEconomy\currency\CurrencyManager;
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use pocketmine\player\Player;
use pocketmine\Server;

class BedrockEconomyProvider extends EconomyProvider
{
    private LegacyBEAPI $api;
    private CurrencyManager $currency;

    public static function checkDependencies(): bool
    {
        return Server::getInstance()->getPluginManager()->getPlugin("BedrockEconomy") !== null;
    }

    public function __construct()
    {
        $this->api = BedrockEconomyAPI::legacy();
        $this->currency = BedrockEconomy::getInstance()->getCurrencyManager();
    }

    public function getMonetaryUnit(): string
    {
        return $this->currency->getSymbol();
    }

    public function getMoney(Player $player, callable $callback): void
    {
        $this->api->getPlayerBalance($player->getName(), ClosureContext::create(fn(?int $balance) => $callback($balance ?? $this->currency->getDefaultBalance())));
    }

    public function giveMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $this->api->addToPlayerBalance($player->getName(), (int)$amount, $callback ? ClosureContext::create($callback) : null);
    }

    public function takeMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $this->api->subtractFromPlayerBalance($player->getName(), (int)$amount, $callback ? ClosureContext::create($callback) : null);
    }

    public function setMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $this->api->setPlayerBalance($player->getName(), (int)$amount, $callback ? ClosureContext::create($callback) : null);
    }
}
