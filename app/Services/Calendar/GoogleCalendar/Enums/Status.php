<?php

namespace App\Services\Calendar\GoogleCalendar\Enums;

enum Status: string
{
    case CONFIRMED = 'confirmed';
    case TENTATIVE = 'tentative';
    case CANCELLED = 'cancelled';
}
