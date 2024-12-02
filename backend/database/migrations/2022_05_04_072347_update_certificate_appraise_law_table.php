<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCertificateAppraiseLawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_law', 'appraise_law_id')) {
			Schema::table('appraise_law', function (Blueprint $table) {
				$table->integer('appraise_law_id')->nullable()->change();
			});
		}
		if (Schema::hasColumn('certificate_asset_law', 'appraise_law_id')) {
			Schema::table('certificate_asset_law', function (Blueprint $table) {
				$table->integer('appraise_law_id')->nullable()->change();
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
