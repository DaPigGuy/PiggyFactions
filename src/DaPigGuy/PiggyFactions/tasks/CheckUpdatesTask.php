<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\tasks;

use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class CheckUpdatesTask extends AsyncTask
{
    public function onRun(): void
    {
        $this->setResult(Internet::getURL("https://poggit.pmmp.io/releases.json?name=PiggyFactions"));
    }

    public function onCompletion(Server $server): void
    {
        $plugin = PiggyFactions::getInstance();
        try {
            if ($plugin->isEnabled()) {
                $data = json_decode($this->getResult(), true);
                if (version_compare($plugin->getDescription()->getVersion(), $data[0]["version"]) === -1) {
                    if ($server->getPluginManager()->isCompatibleApi($data[0]["api"][0]["from"])) {
                        PiggyFactions::getInstance()->getLogger()->info("PiggyFactions v" . $data[0]["version"] . " is available for download at " . $data[0]["artifact_url"] . "/PiggyFactions.phar");
                    }
                }
            }
        } catch (\Exception $exception) {
            $plugin->getLogger()->warning("Auto-update check failed.");
            $plugin->getLogger()->debug($exception);
        }
    }
}