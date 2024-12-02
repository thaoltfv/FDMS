<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetOtherAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE certificate_asset_other_assets ( LIKE appraise_other_assets INCLUDING ALL )');
		\DB::statement('INSERT INTO certificate_asset_other_assets SELECT * FROM appraise_other_assets');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_asset_other_assets');
    }
}
