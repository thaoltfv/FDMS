<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseLawSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_law', 'content')) {
			Schema::table('appraise_law', function (Blueprint $table) {
				$table->text('content')->nullable()->change();
			});
		}
		
		if (Schema::hasColumn('certificate_asset_law', 'content')) {
			Schema::table('certificate_asset_law', function (Blueprint $table) {
				$table->text('content')->nullable()->change();
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
