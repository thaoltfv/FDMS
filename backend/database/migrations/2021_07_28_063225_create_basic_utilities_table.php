<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicUtilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_specification_has_basic_utilities', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('basic_utility_id');
            $table->foreign('basic_utility_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('block_specification_id');
            $table->foreign('block_specification_id')
                ->references('id')
                ->on('block_specifications')
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
    }
}
