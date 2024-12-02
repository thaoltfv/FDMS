<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentAssetLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_asset_laws', function (Blueprint $table) {
            $table->id();
            $table->integer('apartment_asset_id');
            $table->integer('appraise_law_id');
            $table->string('document_num')->nullable();
            $table->dateTime('document_date')->nullable();
            $table->string('description')->nullable();
            $table->string('legal_name_holder')->nullable();
            $table->string('certifying_agency')->nullable();
            $table->text('origin_of_use')->nullable();
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
        Schema::dropIfExists('apartment_asset_laws');
    }
}
