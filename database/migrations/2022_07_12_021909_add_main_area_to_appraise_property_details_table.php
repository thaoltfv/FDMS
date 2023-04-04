<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainAreaToAppraisePropertyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_property_details', 'main_area')) {
            Schema::table('appraise_property_details', function (Blueprint $table) {
				$table->decimal('main_area',19,2)->nullable()->before('created_at');
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
        if (Schema::hasColumn('appraise_property_details', 'main_area')){
            Schema::table('appraise_property_details', function (Blueprint $table) {
                $table->dropColumn('main_area');
            });
        }
    }
}
