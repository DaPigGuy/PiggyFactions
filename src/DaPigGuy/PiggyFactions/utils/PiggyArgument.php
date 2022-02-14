<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

use CortexPE\Commando\args\BaseArgument;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;

class PiggyArgument extends BaseArgument
{
    private BaseArgument $argument;

    public function __construct(BaseArgument $argument)
    {
        $this->argument = $argument;
        parent::__construct($argument->getName(), $argument->isOptional());
        $this->parameterData = $this->argument->parameterData;
    }

    public function getWrappedArgument(): BaseArgument
    {
        return $this->argument;
    }

    public function isOptional(): bool
    {
        return PiggyFactions::getInstance()->areFormsEnabled() || $this->argument->isOptional();
    }

    public function getNetworkType(): int
    {
        return $this->argument->getNetworkType();
    }

    public function canParse(string $testString, CommandSender $sender): bool
    {
        return $this->argument->canParse($testString, $sender);
    }

    public function parse(string $argument, CommandSender $sender)
    {
        return $this->argument->parse($argument, $sender);
    }

    public function getTypeName(): string
    {
        return $this->argument->getTypeName();
    }

    public function getSpanLength(): int
    {
        return $this->argument->getSpanLength();
    }
}