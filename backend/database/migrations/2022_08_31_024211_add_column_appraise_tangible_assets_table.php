<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAppraiseTangibleAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_tangible_assets', 'total_desicion_average')) {
            Schema::table('appraise_tangible_assets', function (Blueprint $table) {
				$table->integer('total_desicion_average')->nullable()->before('created_at');
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
        if (Schema::hasColumn('appraise_tangible_assets', 'total_desicion_average')) {
            Schema::table('appraise_tangible_assets', function (Blueprint $table) {
				$table->dropColumn('total_desicion_average');
            });
        }
    }
}
