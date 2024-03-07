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
            $screen = ScreensDefault::ACCOUNTING_SCREEN;
            Permission::firstOrCreate(
                ['name' => $permission . '_' . $screen, 'guard_name' => 'api']
            );
        }
        $guestRole = Role::query()->where(['name' => RoleDefault::USER['role'], 'guard_name' => 'api', 'role_name' => RoleDefault::USER['name_2']])->first();
        Log::info('guestRole: ', ['result' => $guestRole->toArray()]);
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            $screen = ScreensDefault::ACCOUNTING_SCREEN;

            $permissionResult = Permission::where('name', '=', $permission . '_' . $screen)->get();

            Log::info('Permission Result: ', ['result' => $permissionResult->toArray()]);
            $guestRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        }
        $adminRole = Role::query()->where(['name' => RoleDefault::ADMIN['role'], 'guard_name' => 'api', 'role_name' => RoleDefault::ADMIN['name_2']])->first();
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            $screen = ScreensDefault::ACCOUNTING_SCREEN;
            $adminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        }
        $rootAdminRole = Role::query()->where(['name' => RoleDefault::ROOT_ADMIN['role'], 'guard_name' => 'api', 'role_name' => RoleDefault::ROOT_ADMIN['name_2']])->first();
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            $screen = ScreensDefault::ACCOUNTING_SCREEN;
            $rootAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        }
        // $superAdminRole = Role::query()->where(['name' => RoleDefault::SUPER_ADMIN['role'], 'guard_name' => 'api', 'role_name' => RoleDefault::SUPER_ADMIN['name_2']])->first();
        // foreach (PermissionsDefault::PERMISSIONS as $permission) {
        //     $screen = ScreensDefault::PAYMENT_SCREEN;
        //     $superAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        // }
        // $subAdminRole = Role::query()->where(['name' => RoleDefault::SUB_ADMIN['role'], 'guard_name' => 'api', 'role_name' => RoleDefault::SUB_ADMIN['name_2']])->first();
        // foreach (PermissionsDefault::PERMISSIONS as $permission) {
        //     $screen = ScreensDefault::PAYMENT_SCREEN;
        //     $subAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
        // }
    }
}
