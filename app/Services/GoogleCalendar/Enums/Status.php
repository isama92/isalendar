<?php

namespace App\Services\GoogleCalendar\Enums;

enum Status: string
{
    case CONFIRMED = 'confirmed';
    case TENTATIVE = 'tentative';
    case CANCELLED = 'cancelled';
}
