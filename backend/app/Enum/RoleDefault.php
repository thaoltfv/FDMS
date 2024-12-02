<?php


namespace App\Enum;


class RoleDefault
{
    public const ROLES = ['USER', 'ADMIN', 'ROOT_ADMIN'];
    public const USER = [
        'role' => 'USER',
        'name' => 'Thẩm định viên',
        'name_2' => 'User',
    ];
    public const SUB_ADMIN = [
        'role' => 'SUB_ADMIN',
        'name' => 'Kiểm soát viên',
        'name_2' => 'Sub Admin',
    ];
    public const ADMIN = [
        'role' => 'ADMIN',
        'name' => 'Quản trị viên',
        'name_2' => 'Admin',
    ];
    public const SUPER_ADMIN = [
        'role' => 'SUPER_ADMIN',
        'name' => 'Ban giám đốc',
        'name_2' => 'Super Admin',
    ];
    public const ROOT_ADMIN = [
        'role' => 'ROOT_ADMIN',
        'name' => 'Quản trị hệ thống',
        'name_2' => 'Quản trị hệ thống',
    ];
}
