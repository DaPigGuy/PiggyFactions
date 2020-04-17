<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\chat;

use CortexPE\HRKChat\event\PlaceholderResolveEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class TagListener implements Listener
{
    /** @var TagManager */
    private $tagManager;
    /** @var array */
    private $rankMap = [];
    /** @var string */
    private $noFaction = '';
    /** @var string */
    private $noPower = '';
    /** @var string */
    private $noRank = '';

    public function __construct(TagManager $tagManager, array $config)
    {
        $this->tagManager = $tagManager;

        if (isset($config['rankmap'])) $this->rankMap = $config['rankmap'];
        if (isset($config['nofaction'])) $this->noFaction = $config['nofaction'];
        if (isset($config['nopower'])) $this->noPower = $config['nopower'];
        if (isset($config['norank'])) $this->noRank = $config['norank'];
    }

    public function OnTagResolveEvent(PlaceholderResolveEvent $event): void
    {
        $player = $event->getMember()->getPlayer();
        if (!$player instanceof Player) return;
        $tag = $this->getTag($player, $event->getPlaceholderName());
        if ($tag === null) return;
        $event->setValue($tag);
    }

    protected function getTag(Player $player, string $tag): ?string
    {
        $tags = explode('.', $tag, 2);
        if ($tags[0] !== 'piggyfacs' or count($tags) < 2)
            return null;

        switch ($tags[1]) {
            case "name":
                return $this->tagManager->getFactionName($player, $this->noFaction);
            case "power":
                return $this->tagManager->getFactionPower($player, $this->noPower);
            case "rank.name":
                return $this->tagManager->getPlayerRankName($player, $this->noRank);
            case "rank.symbol":
                return $this->tagManager->getPlayerRankSymbol($player, $this->rankMap, $this->noRank);
            case "members.all":
                return $this->tagManager->getFactionSizeTotal($player, $this->noPower);
            case "members.online":
                return $this->tagManager->getFactionSizeOnline($player, $this->noPower);
            default:
                return null;
        }
    }
}