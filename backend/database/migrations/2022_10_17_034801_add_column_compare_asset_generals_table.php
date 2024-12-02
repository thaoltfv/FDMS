<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCompareAssetGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('compare_asset_generals', 'project_id')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->integer('project_id')->nullable();
            });
        }
        if (!Schema::hasColumn('compare_asset_generals', 'block_id')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->integer('block_id')->nullable();
            });
        }
        if (!Schema::hasColumn('compare_asset_generals', 'floor_id')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->integer('floor_id')->nullable();
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
        if (Schema::hasColumn('compare_asset_generals', 'project_id')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->dropColumn('project_id');
            });
        }
        if (Schema::hasColumn('compare_asset_generals', 'block_id')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->dropColumn('block_id');
            });
        }
        if (Schema::hasColumn('compare_asset_generals', 'floor_id')) {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->dropColumn('floor_id');
            });
        }
    }
}
