<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

class Roles
{
    const LEADER = "leader";
    const OFFICER = "officer";
    const MEMBER = "member";
    const RECRUIT = "recruit";

    const ALL = [
        Roles::RECRUIT => 1,
        Roles::MEMBER => 2,
        Roles::OFFICER => 3,
        Roles::LEADER => 4
    ];
}