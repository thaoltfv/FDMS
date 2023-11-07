<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurposeLandDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('appraise_law_land_details', ['land_type_purpose_id', 'total_area'])) {
            Schema::table('appraise_law_land_details', function (Blueprint $table) {
				$table->integer('land_type_purpose_id')->nullable()->before('created_at');
                $table->integer('total_area')->nullable()->before('created_at');
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
        if (!Schema::hasColumns('appraise_law_land_details', ['land_type_purpose_id', 'total_area'])) {
            Schema::table('appraise_law_land_details', function (Blueprint $table) {
                $table->dropColumn('land_type_purpose_id');
                $table->dropColumn('total_area');
            });
        }
    }
}
