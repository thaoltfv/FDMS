<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnDictionary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dictionaries', 'name_lv_1')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->text('name_lv_1')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'name_lv_2')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->text('name_lv_2')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'name_lv_3')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->text('name_lv_3')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'name_lv_4')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->text('name_lv_4')->nullable();
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
        if (Schema::hasColumn('dictionaries', 'name_lv_1')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('name_lv_1');
            });
        }
        if (Schema::hasColumn('dictionaries', 'name_lv_2')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('name_lv_2');
            });
        }
        if (Schema::hasColumn('dictionaries', 'name_lv_3')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('name_lv_3');
            });
        }
        if (Schema::hasColumn('dictionaries', 'name_lv_4')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('name_lv_4');
            });
        }
    }
}
