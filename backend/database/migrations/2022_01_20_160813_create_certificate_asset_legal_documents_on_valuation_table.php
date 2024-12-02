<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetLegalDocumentsOnValuationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE certificate_asset_legal_documents_on_valuation ( LIKE appraise_legal_documents_on_valuation INCLUDING ALL )');
		\DB::statement('INSERT INTO certificate_asset_legal_documents_on_valuation SELECT * FROM appraise_legal_documents_on_valuation');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_asset_legal_documents_on_valuation');
    }
}
