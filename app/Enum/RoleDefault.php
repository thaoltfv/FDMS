<?php


namespace App\Enum;


class RoleDefault
{
    public const ROLES = ['USER','ADMIN','ROOT_ADMIN'];
    public const USER = [
        'role' => 'USER',
        'name' => 'Thẩm định viên',
    ];
    public const SUB_ADMIN = [
        'role' => 'SUB_ADMIN',
        'name' => 'Kiểm soát viên',
    ];
    public const ADMIN = [
        'role' => 'ADMIN',
        'name' => 'Quản trị viên',
    ];
    public const SUPER_ADMIN = [
        'role' => 'SUPER_ADMIN',
        'name' => 'Ban giám đốc',
    ];
    public const ROOT_ADMIN = [
        'role' => 'ROOT_ADMIN',
        'name' => 'Quản trị hệ thống',
    ];
}
