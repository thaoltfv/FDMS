<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDataIntoComapreAssetVerionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('compare_asset_versions', 'asset_general_data')) {
            Schema::table('compare_asset_versions', function (Blueprint $table) {
                $table->longText('asset_general_data')->nullable();
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
        if (Schema::hasColumn('compare_asset_versions', 'asset_general_data')) {
            Schema::table('compare_asset_versions', function (Blueprint $table) {
                $table->dropColumn('asset_general_data');
            });
        }
    }
}
