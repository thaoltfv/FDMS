<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCerificateSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'service_fee')) {
			Schema::table('certificates', function (Blueprint $table) {
				$table->integer('service_fee')->unsigned()->default(0)->before('created_at');
			});
		}
		if (!Schema::hasColumn('certificates', 'appraiser_sale_id')) {
			Schema::table('certificates', function (Blueprint $table) {
				$table->string('appraiser_sale_id')->nullable()->before('created_at');
			});
		}
		if (!Schema::hasColumn('certificates', 'appraiser_perform_id')) {
			Schema::table('certificates', function (Blueprint $table) {
				$table->string('appraiser_perform_id')->nullable()->before('created_at');
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
