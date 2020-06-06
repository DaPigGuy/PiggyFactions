<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\Plugin;

class PoggitBuildInfo
{
    /** @var bool */
    private $runningFromSource = false;
    /** @var bool */
    private $runningPhar = false;
    /** @var bool */
    private $runningPoggitPhar = false;
    /** @var array */
    private $poggitPharMetadata;

    public function __construct(Plugin $plugin, string $file)
    {
        $extension = ".phar";
        if (is_dir($file)) {
            $this->runningFromSource = true;
        } else if ($plugin->getPluginLoader() instanceof PharPluginLoader && substr($file, -strlen($extension)) === $extension) {
            $this->runningPhar = true;
            $phar = new \Phar($file);
            if ($phar->hasMetadata()) {
                $metadata = $phar->getMetadata();
                if (isset($metadata["poggitBuildId"])) {
                    $this->runningPoggitPhar = true;
                    $this->poggitPharMetadata = $metadata;
                }
            }
        }
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

    public function getPoggitPharMetadata(): array
    {
        return $this->poggitPharMetadata;
    }
}