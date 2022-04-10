<?php

namespace App\Enum;

class OrderStatusType
{
    const PENDING = "Pending";
    const PROCESSING = "Processing";
    const SHIPPED = "Shipped";
    const DELIVERED = "Delivered";
    const USER_CANCELLED = "User-Cancelled";

    /** string[] */
    public static function getOrderStatusTypes(): array
    {
        return [
            self::PENDING,
            self::PROCESSING,
            self::SHIPPED,
            self::DELIVERED,
            self::USER_CANCELLED,
        ];
    }
}