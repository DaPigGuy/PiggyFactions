<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

class Relations
{
    const ENEMY = "enemy";
    const NONE = "none";
    const TRUCE = "truce";
    const ALLY = "ally";

    const ALL = [
        Relations::ALLY,
        Relations::TRUCE,
        Relations::NONE,
        Relations::ENEMY
    ];
}