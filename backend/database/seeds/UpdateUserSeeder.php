<?php

use App\Contracts\UserRepository;
use App\Enum\PermissionsDefault;
use App\Enum\RoleDefault;
use App\Enum\ScreensDefault;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UpdateUserSeeder extends Seeder
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
        $rootAdminRole = Role::create(['name' => RoleDefault::ROOT_ADMIN['role'], 'guard_name' => 'api','role_name'=>RoleDefault::ROOT_ADMIN['name']]);
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            foreach (ScreensDefault::ROOT_ADMIN_SCREENS as $screen) {
                $rootAdminRole->givePermissionTo(Permission::where('name', '=', $permission . '_' . $screen)->get());
            }
        }
        $this->userRepository->createUser(
            [
                'id' => Uuid::uuid4()->toString(),
                'email' => 'donava@gmail.com',
                'name' => 'admin',
                'phone' => '',
                'branch_id' => 6,
                'mailing_address' =>'donava@gmail.com',
                'role'=> RoleDefault::ADMIN['role'],
            ]
        );

        return $this->userRepository->createUser(
            [
                'id' => Uuid::uuid4()->toString(),
                'email' => 'admin@thamdinhgiadongnai.vn',
                'name' => 'root_admin',
                'phone' => '',
                'branch_id' => 6,
                'mailing_address' =>'admin@thamdinhgiadongnai.vn',
                'role'=> RoleDefault::ROOT_ADMIN['role'],
            ]
        );
    }
}
