<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_type_id');
            $table->string('appraise_asset')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('ward_id')->nullable();
            $table->integer('street_id')->nullable();
            $table->string('coordinates')->nullable();
            $table->integer('step')->nullable();
            $table->integer('status')->nullable();
            $table->uuid('created_by')->nullable();
            $table->integer('real_estate_id')->nullable();
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
        Schema::dropIfExists('apartment_assets');
    }
}
