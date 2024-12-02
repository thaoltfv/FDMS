<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnVerhicleCertificateAssetLawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('verhicle_certificate_asset_laws', 'machine_asset_id')) {
            Schema::table('verhicle_certificate_asset_laws', function (Blueprint $table) {
				$table->renameColumn('machine_asset_id','verhicle_asset_id');
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
