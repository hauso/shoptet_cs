<?php

declare(strict_types=1);

namespace App\Model\PickupPoint;

enum PickupPointStatus: string
{
    case Available = 'available';
    case TemporarilyUnavailable = 'temporarily_unavailable';
    case Closed = 'closed';
    case Terminated = 'terminated';
}
