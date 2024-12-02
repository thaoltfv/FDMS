<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBlockSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('block_specifications', function (Blueprint $table) {
            $table->integer('block_list_id')->unsigned()->nullable()->change();
            $table->string('built_year')->unsigned()->nullable()->change();
        });
        Schema::table('room_details', function (Blueprint $table) {
            $table->integer('block_list_id')->unsigned()->nullable()->change();
            $table->boolean('two_sides_room')->unsigned()->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
