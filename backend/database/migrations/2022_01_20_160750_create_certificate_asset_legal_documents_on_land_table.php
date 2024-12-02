<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetLegalDocumentsOnLandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE certificate_asset_legal_documents_on_land ( LIKE appraise_legal_documents_on_land INCLUDING ALL )');
		\DB::statement('INSERT INTO certificate_asset_legal_documents_on_land SELECT * FROM appraise_legal_documents_on_land');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_asset_legal_documents_on_land');
    }
}
