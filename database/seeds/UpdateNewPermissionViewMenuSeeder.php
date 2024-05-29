<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UpdateNewPermissionViewMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lstInsertPRELIMINARYASSET = ['VIEW_MENU_FOLLOW_PROFILE', 'ADD_MENU_FOLLOW_PROFILE', 'EDIT_MENU_FOLLOW_PROFILE', 'DELETE_MENU_FOLLOW_PROFILE', 'IMPORT_MENU_FOLLOW_PROFILE', 'EXPORT_MENU_FOLLOW_PROFILE', 'ACCEPT_MENU_FOLLOW_PROFILE'];

        $lstRoleInsertAutoPermission = ['ADMIN', 'USER', 'ROOT_ADMIN', 'SUB_ADMIN'];

        foreach ($lstInsertPRELIMINARYASSET as $permissionName) {
            $retryCount = 0;
            do {
                try {
                    DB::beginTransaction(); // Start a new database transaction

                    $uuid = (string) Str::uuid(); // Generate a new UUID

                    $permission = Permission::firstOrCreate(
                        ['name' => $permissionName, 'guard_name' => 'api'],
                        ['id' => $uuid]
                    );


                    $inserted = true; // If the record was inserted successfully, set $inserted to true

                    // Assign the permission to each role
                    if ($permission->wasRecentlyCreated) {
                        foreach ($lstRoleInsertAutoPermission as $roleName) {
                            $role = Role::findByName($roleName, 'api');
                            if (!$role->hasPermissionTo($permission)) {
                                $role->givePermissionTo($permission);
                            }
                        }
                    }

                    DB::commit(); // Commit the transaction
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack(); // Roll back the transaction
                    Log::error('Database query exception: ' . $e->getMessage());

                    $retryCount++;
                    $inserted = false; // If there was a conflict, set $inserted to false and the loop will retry
                    if ($retryCount >= 5) {
                        break; // If we've retried 5 times, break out of the loop
                    }
                } catch (\Exception $e) {
                    DB::rollBack(); // Roll back the transaction
                    Log::error('General exception: ' . $e->getMessage());

                    break; // If there was another type of exception, break out of the loop
                }
            } while (!$inserted);
        }
    }
}
