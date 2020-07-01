<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\tasks;

use DaPigGuy\PiggyFactions\PiggyFactions;
use Exception;
use pocketmine\plugin\ApiVersion;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;

class CheckUpdatesTask extends AsyncTask
{
    public function onRun(): void
    {
        $this->setResult([Internet::getURL("https://poggit.pmmp.io/releases.json?name=PiggyFactions", 10, [], $error), $error]);
    }

    public function onCompletion(): void
    {
        $plugin = PiggyFactions::getInstance();
        try {
            if ($plugin->isEnabled()) {
                $results = $this->getResult();

                $error = $results[1];
                if ($error !== null) throw new Exception($error);

                $data = json_decode($results[0], true);
                if (version_compare($plugin->getDescription()->getVersion(), $data[0]["version"]) === -1) {
                    if (ApiVersion::isCompatible($plugin->getServer()->getApiVersion(), $data[0]["api"][0]["from"])) {
                        PiggyFactions::getInstance()->getLogger()->info("PiggyFactions v" . $data[0]["version"] . " is available for download at " . $data[0]["artifact_url"] . "/PiggyFactions.phar");
                    }
                }
            }
        } catch (Exception $exception) {
            $plugin->getLogger()->warning("Auto-update check failed.");
            $plugin->getLogger()->debug((string)$exception);
        }
    }
}