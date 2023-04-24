<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\libs\DaPigGuy\libPiggyUpdateChecker\tasks;

use pocketmine\plugin\ApiVersion;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class CheckUpdatesTask extends AsyncTask
{
    public function __construct(private string $name, private string $version)
    {
    }

    public function onRun(): void
    {
        $result = Internet::getURL("https://poggit.pmmp.io/releases.min.json?name=" . $this->name, 10, [], $error);
        $this->setResult([$result?->getBody(), $error]);
    }

    public function onCompletion(): void
    {
        $logger = Server::getInstance()->getLogger();
        [$body, $error] = $this->getResult();
        if ($error) {
            $logger->warning("Auto-update check failed.");
            $logger->debug($error);
        } else {
            $versions = json_decode($body, true);
            if ($versions) foreach ($versions as $version) {
                if (version_compare($this->version, $version["version"]) === -1) {
                    if (ApiVersion::isCompatible(Server::getInstance()->getApiVersion(), $version["api"][0])) {
                        $logger->notice($this->name . " v" . $version["version"] . " is available for download at " . $version["artifact_url"] . "/" . $this->name . ".phar");
                        break;
                    }
                }
            }
        }
    }
}