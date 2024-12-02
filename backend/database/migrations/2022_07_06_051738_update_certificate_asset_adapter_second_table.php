<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCertificateAssetAdapterSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificate_asset_adapter', 'change_purpose_price')) {
			Schema::table('certificate_asset_adapter', function (Blueprint $table) {
				$table->float('change_purpose_price')->nullable()->before('created_at');
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
