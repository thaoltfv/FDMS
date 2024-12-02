<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetPicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE certificate_asset_pics ( LIKE appraise_pics INCLUDING ALL )');
		\DB::statement('INSERT INTO certificate_asset_pics SELECT * FROM appraise_pics');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_asset_pics');
    }
}
