<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTangibleNameIntoAppraiseTangibleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_tangible_assets', 'tangible_name')) {
            Schema::table('appraise_tangible_assets', function (Blueprint $table) {
				$table->string('tangible_name')->nullable()->before('created_at');
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
        if (Schema::hasColumn('appraise_tangible_assets', 'tangible_name')) {
            Schema::table('appraise_tangible_assets', function (Blueprint $table) {
				$table->dropColumn('tangible_name');
            });
        }
    }
}
