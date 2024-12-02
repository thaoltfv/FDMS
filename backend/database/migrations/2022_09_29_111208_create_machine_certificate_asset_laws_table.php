<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineCertificateAssetLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_certificate_asset_laws', function (Blueprint $table) {
            $table->id();
            $table->integer('machine_asset_id');
            $table->integer('appraise_law_id')->nullable();
            $table->string('document_num')->nullable();
            $table->dateTime('document_date')->nullable();
            $table->text('description')->nullable();
            $table->string('legal_name_holder')->nullable();
            $table->text('origin_of_use')->nullable();
            $table->string('certifying_agency')->nullable();
            $table->text('content')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_certificate_asset_laws');
    }
}
