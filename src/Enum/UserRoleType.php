<?php


namespace App\Enum;


class UserRoleType
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_STORE_USER = 'ROLE_STORE_USER';

    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN    => 'Administrator'
        ];
    }
}