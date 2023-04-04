<?php

use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app('cache')->forget('spatie.permission.cache');
        DB::table('permissions')->truncate();
        foreach (PermissionsDefault::PERMISSIONS as $permission) {
            foreach (ScreensDefault::ROOT_ADMIN_SCREENS as $screen) {
                Permission::firstOrCreate(['name' => $permission.'_'.$screen, 'guard_name' => 'api'],['name' => $permission.'_'.$screen, 'guard_name' => 'api']);
            }
        }
    }
}
