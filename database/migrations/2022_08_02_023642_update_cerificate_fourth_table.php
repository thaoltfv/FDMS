<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCerificateFourthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('certificates', 'appraiser_sale_id')) {
			Schema::table('certificates', function (Blueprint $table) {
				$table->string('appraiser_sale_id')->change();
			});
		}
		if (Schema::hasColumn('certificates', 'appraiser_perform_id')) {
			Schema::table('certificates', function (Blueprint $table) {
				$table->string('appraiser_perform_id')->change();
			});
		}
		if (Schema::hasColumn('certificates', 'document_description')) {
			Schema::table('certificates', function (Blueprint $table) {
				$table->text('document_description')->nullable()->change();
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
