<?php


namespace App\Utility;


class TokenGenerator
{
    public static function generateProductToken()
    {
        return uniqid('PRD_');
    }

    public static function generateOrderToken()
    {
        return uniqid('SPK_ORD_');
    }

    public static function generateUserToken()
    {
        return uniqid('SPK_USER_');
    }
}