<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVitriNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('certificate_asset_comparison_factor', 'appraise_title')) {
            Schema::table('certificate_asset_comparison_factor', function (Blueprint $table) {
                $table->text('appraise_title')->change();
            });
        }
        if (Schema::hasColumn('certificate_asset_comparison_factor', 'asset_title')) {
            Schema::table('certificate_asset_comparison_factor', function (Blueprint $table) {
                $table->text('asset_title')->change();
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
        // if (!Schema::hasColumn('appraise_comparison_factor', 'is_synced_els')) {
        //     Schema::table('appraise_comparison_factor', function (Blueprint $table) {
        //         $table->dropColumn('is_synced_els');
        //     });
        // }
    }
}
