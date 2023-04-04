<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateApartmentAppraisalBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_apartment_appraisal_base', function (Blueprint $table) {
            $table->id();
            $table->integer('apartment_asset_id');
            $table->integer('approach_id')->nullable();
            $table->integer('method_used_id')->nullable();
            $table->integer('principle_id')->nullable();
            $table->integer('basis_property_id')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('certificate_apartment_appraisal_base');
    }
}
