<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRoomDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('room_details', 'description')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }
        if (!Schema::hasColumn('room_details', 'legal_id')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->integer('legal_id')->nullable();
            });
        }
        if (Schema::hasColumn('room_details', 'floor')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->integer('floor')->nullable()->unsigned()->change();
            });
        }
        if (Schema::hasColumn('room_details', 'unit_price')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->bigInteger('unit_price')->nullable()->unsigned()->change();
            });
        }
        if (Schema::hasColumn('room_details', 'bedroom_num')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->integer('bedroom_num')->nullable()->unsigned()->change();
            });
        }
        if (Schema::hasColumn('room_details', 'wc_num')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->integer('wc_num')->nullable()->unsigned()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('room_details', 'description')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
        if (Schema::hasColumn('room_details', 'legal_id')) {
            Schema::table('room_details', function (Blueprint $table) {
                $table->dropColumn('legal_id');
            });
        }
    }
}
