<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\utils;

use DateTime;

class FormattedTime
{
    public static function getLong(int $timestamp): string
    {
        $dateInterval = (new DateTime())->diff((new DateTime())->setTimestamp($timestamp));
        if ($dateInterval->y >= 1) return $dateInterval->format("%y years, %m months, %d days, %h hours, %i minutes");
        if ($dateInterval->m >= 1) return $dateInterval->format("%m months, %d days, %h hours, %i minutes");
        if ($dateInterval->days >= 1) return $dateInterval->format("%d days, %h hours, %i minutes");
        if ($dateInterval->h >= 1) return $dateInterval->format("%h hours, %i minutes, %s seconds");
        if ($dateInterval->i >= 1) return $dateInterval->format("%i minutes, %s seconds");
        if ($dateInterval->s >= 1) return $dateInterval->format("%s seconds");
        return "";
    }

    public static function getShort(int $timestamp): string
    {
        $dateInterval = (new DateTime())->diff((new DateTime())->setTimestamp($timestamp));
        if ($dateInterval->y >= 1) return $dateInterval->format("%Y:%M:%D:%H:%I");
        if ($dateInterval->m >= 1) return $dateInterval->format("%M:%D:%H:%I");
        if ($dateInterval->days >= 1) return $dateInterval->format("%D:%H:%I");
        if ($dateInterval->h >= 1) return $dateInterval->format("%H:%I");
        if ($dateInterval->i >= 1) return $dateInterval->format("%I:%S");
        if ($dateInterval->s >= 1) return $dateInterval->format("%S");
        return "";
    }
}