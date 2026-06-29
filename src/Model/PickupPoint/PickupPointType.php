<?php

declare(strict_types=1);

namespace App\Model\PickupPoint;

enum PickupPointType: string
{
    case Box = 'box';
    case Point = 'point';
}
