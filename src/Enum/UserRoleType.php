<?php


namespace App\Enum;


class UserRoleType
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SALES = 'ROLE_SALES';

    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN    => 'Administrator'
        ];
    }
}