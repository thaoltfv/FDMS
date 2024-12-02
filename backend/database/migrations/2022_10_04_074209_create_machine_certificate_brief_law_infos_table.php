<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineCertificateBriefLawInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_certificate_brief_law_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('machine_asset_id');
            $table->integer('principle_id')->nullable();
            $table->integer('basis_property_id')->nullable();
            $table->integer('approach_id')->nullable();
            $table->integer('method_used_id')->nullable();
            $table->text('document_description')->nullable();
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
        Schema::dropIfExists('machine_certificate_brief_law_infos');
    }
}
