<?php

namespace App\Enum;

class OrderStatusType
{
    const PENDING = "Pending";
    const PROCESSING = "Processing";
    const SHIPPED = "Shipped";
    const DELIVERED = "Delivered";
    const USER_CANCELLED = "User-Cancelled";
}