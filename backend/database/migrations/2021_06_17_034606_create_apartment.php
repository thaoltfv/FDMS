<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('coordinates');

            $table->integer('province_id');
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');

            $table->integer('district_id');
            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('cascade');

            $table->integer('ward_id');
            $table->foreign('ward_id')
                ->references('id')
                ->on('wards')
                ->onDelete('cascade');

            $table->integer('street_id');
            $table->foreign('street_id')
                ->references('id')
                ->on('streets')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
        Schema::dropIfExists('apartments');

    }
}
