<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

use Phar;
use pocketmine\plugin\Plugin;

class PoggitBuildInfo
{
    /** @var bool */
    private $runningFromSource = false;
    /** @var bool */
    private $runningPhar = false;
    /** @var bool */
    private $runningPoggitPhar = false;
    /** @var bool */
    private $isPoggitRelease = false;
    /** @var array */
    private $poggitPharMetadata;

    public function __construct(Plugin $plugin, string $file, bool $isPhar)
    {
        if ($isPhar) {
            $this->runningPhar = true;
            $phar = new Phar($file);
            if ($phar->hasMetadata()) {
                $metadata = $phar->getMetadata();
                if (isset($metadata["poggitBuildId"])) {
                    $this->runningPoggitPhar = true;
                    $this->isPoggitRelease = isset($metadata["poggitRelease"]);
                    $this->poggitPharMetadata = $metadata;
                }
            }
        } else if (is_dir(substr($file, strlen($plugin->getPluginLoader()->getAccessProtocol())))) {
            $this->runningFromSource = true;
        }
    }

    public function getSpecificVersion(): string
    {
        $specificVersion = "";
        if ($this->isRunningFromSource()) {
            $specificVersion = "SOURCE";
        } elseif ($this->isRunningPoggitPhar()) {
            $metadata = $this->getPoggitPharMetadata();
            $specificVersion = "BUILD #" . $metadata["projectBuildNumber"];
        } elseif ($this->isRunningPhar()) {
            $specificVersion = "CUSTOM PHAR";
        }
        return $specificVersion;
    }

    public function isRunningFromSource(): bool
    {
        return $this->runningFromSource;
    }

    public function isRunningPhar(): bool
    {
        return $this->runningPhar;
    }

    public function isRunningPoggitPhar(): bool
    {
        return $this->runningPoggitPhar;
    }

    public function isPoggitRelease(): bool
    {
        return $this->isPoggitRelease;
    }

    public function getPoggitPharMetadata(): array
    {
        return $this->poggitPharMetadata;
    }
}