<?php

namespace DaPigGuy\PiggyFactions\logs;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;

class LogsManager
{
    /** @var PiggyFactions */
    private $plugin;

    public function __construct(PiggyFactions $plugin)
    {
        $this->plugin = $plugin;
        $plugin->getDatabase()->executeGeneric("piggyfactions.logs.init");
    }

    public function getLogs(string $action, Faction $faction): void
    {
        $this->plugin->getDatabase()->executeSelect("piggyfactions.logs.loadlogs", ["action" => $action, "faction" => $faction->getId()], function (array $rows): void {
            var_dump($rows);
        });
    }
}