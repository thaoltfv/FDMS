<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateApartmentOtherAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_apartment_other_assets', function (Blueprint $table) {
            $table->id();
            $table->integer('apartment_asset_id');
            $table->string('name')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('unit')->nullable();
            $table->bigInteger('unit_price')->nullable();
            $table->bigInteger('total_price')->nullable();
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
        Schema::dropIfExists('certificate_apartment_other_assets');
    }
}
