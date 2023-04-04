<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_lists', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('apartment_id')->nullable();
            $table->foreign('apartment_id')
                ->references('id')
                ->on('apartments')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('coordinates');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('block_specifications', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('asset_general_id');
            $table->foreign('asset_general_id')
                ->references('id')
                ->on('compare_asset_generals')
                ->onDelete('cascade');

            $table->integer('block_list_id');
            $table->foreign('block_list_id')
                ->references('id')
                ->on('block_lists')
                ->onDelete('cascade');

            $table->string('built_year');
            $table->integer('total_floor');
            $table->integer('basement_floor');
            $table->integer('commercial_floor');
            $table->integer('living_floor');
            $table->integer('lift_number');
            $table->string('other_utilities')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('room_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('asset_general_id');
            $table->foreign('asset_general_id')
                ->references('id')
                ->on('compare_asset_generals')
                ->onDelete('cascade');

            $table->integer('block_list_id');
            $table->foreign('block_list_id')
                ->references('id')
                ->on('block_lists')
                ->onDelete('cascade');

            $table->boolean('two_sides_room');
            $table->decimal('area', 18, 2)->nullable();
            $table->integer('bedroom_num');
            $table->integer('wc_num');
            $table->string('room_num');
            $table->integer('floor');

            $table->integer('direction_id')->nullable();
            $table->foreign('direction_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('furniture_quality_id')->nullable();
            $table->foreign('furniture_quality_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');


            $table->bigInteger('unit_price');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('room_furniture_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('room_detail_id');
            $table->foreign('room_detail_id')
                ->references('id')
                ->on('room_details')
                ->onDelete('cascade');

            $table->string('name');
            $table->integer('number');
            $table->string('description');

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
    }
}
