<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraisePropertiesSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_properties', 'land_no')) {
			Schema::table('appraise_properties', function (Blueprint $table) {
				$table->string('land_no')->nullable()->change();
			});
		}
        if (Schema::hasColumn('appraise_properties', 'doc_no')) {
			Schema::table('appraise_properties', function (Blueprint $table) {
				$table->string('doc_no')->nullable()->change();
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
        //
    }
}
