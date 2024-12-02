<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetPropertyTurningTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE certificate_asset_property_turning_time ( LIKE appraise_property_turning_time INCLUDING ALL )');
		\DB::statement('INSERT INTO certificate_asset_property_turning_time SELECT * FROM appraise_property_turning_time');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_asset_property_turning_time');
    }
}
