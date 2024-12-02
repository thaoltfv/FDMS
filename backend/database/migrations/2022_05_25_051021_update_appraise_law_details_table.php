<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseLawDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_law_details', 'expiry_date')) {
			Schema::table('appraise_law_details', function (Blueprint $table) {
				$table->string('expiry_date')->nullable()->change();
			});
		}
		
		if (Schema::hasColumn('certificate_asset_law_details', 'expiry_date')) {
			Schema::table('certificate_asset_law_details', function (Blueprint $table) {
				$table->string('expiry_date')->nullable()->change();
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
