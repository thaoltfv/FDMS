<?php

use App\Enum\PermissionsDefault;
use App\Enum\RoleDefault;
use App\Enum\ScreensDefault;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AddCustomerRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            $screen =ScreensDefault::CUSTOMER_SCREEN;
                Permission::create(['name' => $permission.'_'.$screen, 'guard_name' => 'api']);
        }
        $guestRole = Role::query()->where(['name' => RoleDefault::USER['role'], 'guard_name' => 'api','role_name'=>RoleDefault::USER['name']])->first();
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            $screen =ScreensDefault::CUSTOMER_SCREEN;
                $guestRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        }
        $adminRole = Role::query()->where(['name' => RoleDefault::ADMIN['role'], 'guard_name' => 'api','role_name'=>RoleDefault::ADMIN['name']])->first();
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            $screen =ScreensDefault::CUSTOMER_SCREEN;
            $adminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        }
        $rootAdminRole = Role::query()->where(['name' => RoleDefault::ROOT_ADMIN['role'], 'guard_name' => 'api','role_name'=>RoleDefault::ROOT_ADMIN['name']])->first();
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            $screen =ScreensDefault::CUSTOMER_SCREEN;
            $rootAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        }
    }
}
