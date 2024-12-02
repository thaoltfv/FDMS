<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (Schema::hasColumn('appraises', 'land_no')) {
			Schema::table('appraises', function (Blueprint $table) {
				$table->string('land_no')->nullable()->change();
			});
		}
        if (Schema::hasColumn('appraises', 'doc_no')) {
			Schema::table('appraises', function (Blueprint $table) {
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
