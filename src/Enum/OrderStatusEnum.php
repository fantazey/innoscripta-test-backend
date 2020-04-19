<?php


namespace App\Enum;


class OrderStatusEnum
{
    const
        INITIAL = 'initial',
        CONFIRMED = 'confirmed',
        IN_PROGRESS = 'in_progress',
        DELIVERY = 'delivery',
        CLOSED = 'closed';
}