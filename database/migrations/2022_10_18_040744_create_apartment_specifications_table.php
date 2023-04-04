<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_specifications', function (Blueprint $table) {
            $table->id();
            $table->integer('asset_general_id');
            $table->string('name')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('total_floors')->nullable();
            $table->integer('nb_living_floor')->nullable();
            $table->integer('total_apartments')->nullable();
            $table->integer('first_floor')->nullable();
            $table->integer('last_floor')->nullable();
            $table->integer('apartments_per_floor')->nullable();
            $table->integer('rank_id')->nullable();
            $table->integer('nb_basement')->nullable();
            $table->integer('nb_elevator')->nullable();
            $table->integer('handover_year')->nullable();
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
        Schema::dropIfExists('apartment_specifications');
    }
}
