<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentAssetPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_asset_properties', function (Blueprint $table) {
            $table->id();
            $table->integer('apartment_asset_id');
            $table->integer('project_id')->nullable();
            $table->integer('block_id')->nullable();
            $table->integer('floor_id')->nullable();
            $table->integer('apartment_id')->nullable();
            $table->decimal('area', 9, 2, true)->nullable();
            $table->integer('bedroom_num')->nullable();
            $table->integer('wc_num')->nullable();
            $table->integer('direction_id')->nullable();
            $table->integer('legal_id')->nullable();
            $table->integer('furniture_quality_id')->nullable();
            $table->integer('handover_year')->nullable();
            $table->text('description')->nullable();
            $table->json('utilities')->nullable();
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
        Schema::dropIfExists('apartment_asset_properties');
    }
}
