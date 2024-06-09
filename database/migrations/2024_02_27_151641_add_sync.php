<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSync extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('compare_asset_generals', 'is_synced_els')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->integer('is_synced_els')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('compare_asset_generals', 'is_synced_els')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->dropColumn('is_synced_els');
            });
        }
    }
}
