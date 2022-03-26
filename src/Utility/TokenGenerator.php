<?php


namespace App\Utility;


class TokenGenerator
{
    public static function generateProductToken()
    {
        return uniqid('PRD_');
    }

    public static function generateSalesInvoiceToken()
    {
        return uniqid('INV_');
    }

    public static function generateServiceToken()
    {
        return uniqid('SRV_');
    }

    public static function generateUserToken()
    {
        return uniqid('USR_');
    }
}