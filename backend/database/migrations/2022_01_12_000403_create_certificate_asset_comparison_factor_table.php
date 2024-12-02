<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetComparisonFactorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE certificate_asset_comparison_factor ( LIKE appraise_tangible_comparison_factor INCLUDING ALL )');
		\DB::statement('INSERT INTO certificate_asset_comparison_factor SELECT * FROM appraise_tangible_comparison_factor');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_asset_comparison_factor');
    }
}
