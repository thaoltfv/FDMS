<?php

use App\Contracts\UserRepository;
use App\Enum\PermissionsDefault;
use App\Enum\RoleDefault;
use App\Enum\ScreensDefault;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Firebase\FirebaseClient;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class RolesTableSeeder extends Seeder
{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app('cache')->forget('spatie.roles.cache');
        DB::table('roles')->truncate();
        //Default User
        $userRole = Role::create(['name' => RoleDefault::USER['role'], 'guard_name' => 'api','role_name'=>RoleDefault::USER['name']]);
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            foreach (ScreensDefault::USER as $screen) {
                $userRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
            }
        }
        //SUB_ADMIN
        $subAdminRole = Role::create(['name' => RoleDefault::SUB_ADMIN['role'], 'guard_name' => 'api','role_name'=>RoleDefault::SUB_ADMIN['name']]);
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            foreach (ScreensDefault::USER as $screen) {
                $subAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
            }
        }
        //Default Admin
        $adminRole = Role::create(['name' => RoleDefault::ADMIN['role'], 'guard_name' => 'api','role_name'=>RoleDefault::ADMIN['name']]);
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            foreach (ScreensDefault::ADMIN_SCREENS as $screen) {
                $adminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
            }
        }
        //SUPER_ADMIN
        $superAdminRole = Role::create(['name' => RoleDefault::SUPER_ADMIN['role'], 'guard_name' => 'api','role_name'=>RoleDefault::SUPER_ADMIN['name']]);
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            foreach (ScreensDefault::ADMIN_SCREENS as $screen) {
                $superAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
            }
        }
        $rootAdminRole = Role::create(['name' => RoleDefault::ROOT_ADMIN['role'], 'guard_name' => 'api','role_name'=>RoleDefault::ROOT_ADMIN['name']]);
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            foreach (ScreensDefault::ROOT_ADMIN_SCREENS as $screen) {
                $rootAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
            }
        }

        $users = User::where('email', '!=', 'admin@fastvalue.vn')->get();
        foreach ($users as $user) {
            $user->assignRole(RoleDefault::USER['role']);
        }
        $rootUser = User::where('email', 'admin@fastvalue.vn')->first();
        if ($rootUser) {
            $rootUser->assignRole(RoleDefault::ROOT_ADMIN['role']);
        } else {
            //Create SUPER_ADMIN
            $this->userRepository->createUser(
                [
                    'email' => 'admin@fastvalue.vn',
                    'name' => 'Root Admin',
                    'phone' => '0909000000',
                    'mailing_address' => 'admin@fastvalue.vn',
                    'role'=> RoleDefault::ROOT_ADMIN['role'],
                    'appraiser' => [
                        'name' => 'Root Admin',
                        'appraise_position_id' => 160,
                    ]
                ]
            );
            (new FirebaseClient())->createUser('admin@fastvalue.vn', 'FastValue@2023');
        }
    }
}
