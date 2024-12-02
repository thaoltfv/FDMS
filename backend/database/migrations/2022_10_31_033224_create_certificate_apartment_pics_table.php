<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateApartmentPicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_apartment_pics', function (Blueprint $table) {
            $table->id();
            $table->integer('apartment_asset_id');
            $table->string('link')->nullable();
            $table->string('description')->nullable();
            $table->integer('type_id')->nullable();
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
        Schema::dropIfExists('certificate_apartment_pics');
    }
}
