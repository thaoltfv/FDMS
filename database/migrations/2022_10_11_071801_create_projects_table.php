<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('province_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('ward_id')->nullable();
            $table->integer('street_id')->nullable();
            $table->string('address')->nullable();
            $table->json('rank')->nullable();
            $table->integer('total_blocks')->nullable();
            $table->integer('total_apartments')->nullable();
            $table->string('coordinates')->nullable();
            $table->string('developer_name')->nullable();
            $table->json('elevator')->nullable();
            $table->json('basement')->nullable();
            $table->json('utilities')->nullable();
            $table->integer('nb_swim_dens')->nullable();
            $table->integer('handover_year')->nullable();
            $table->boolean('status')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
