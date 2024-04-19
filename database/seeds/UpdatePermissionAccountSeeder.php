<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UpdatePermissionAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lstInsertPRELIMINARYASSET = ['VIEW_PRELIMINARY_ASSET', 'ADD_PRELIMINARY_ASSET', 'EDIT_PRELIMINARY_ASSET', 'DELETE_PRELIMINARY_ASSET', 'IMPORT_PRELIMINARY_ASSET', 'EXPORT_PRELIMINARY_ASSET', 'ACCEPT_PRELIMINARY_ASSET'];

        $lstRoleInsertAutoPermission = ['ADMIN', 'USER', 'ROOT_ADMIN', 'SUB_ADMIN'];

        foreach ($lstInsertPRELIMINARYASSET as $permissionName) {
            $retryCount = 0;
            do {
                try {
                    $uuid = (string) Str::uuid(); // Generate a new UUID

                    $permission = Permission::create([
                        'id' => $uuid,
                        'name' => $permissionName,
                        'guard_name' => 'api'
                    ]);

                    $inserted = true; // If the record was inserted successfully, set $inserted to true

                    // Assign the permission to each role
                    foreach ($lstRoleInsertAutoPermission as $roleName) {
                        $role = Role::findByName($roleName, 'api');
                        $role->givePermissionTo($permission);
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    $retryCount++;
                    $inserted = false; // If there was a conflict, set $inserted to false and the loop will retry
                    if ($retryCount >= 5) {
                        break; // If we've retried 5 times, break out of the loop
                    }
                }
            } while (!$inserted);
        }
    }
}
