<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentDictionariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_dictionaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('appraise_title')->nullable();
            $table->integer('appraise_point')->nullable();
            $table->string('asset_title')->nullable();
            $table->integer('asset_point')->nullable();
            $table->string('description')->nullable();
            $table->integer('difference_point')->nullable();
            $table->integer('appraise_percent')->nullable();
            $table->integer('asset_percent')->nullable();
            $table->integer('adjust_percent')->nullable();
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
        Schema::dropIfExists('apartment_dictionaries');
    }
}
