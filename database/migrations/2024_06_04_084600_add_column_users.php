<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'is_guest')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_guest')->nullable()->default(false);
            });
        }
        if (!Schema::hasColumn('users', 'name_lv_1')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('name_lv_1')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'name_lv_2')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('name_lv_2')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'name_lv_3')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('name_lv_3')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'name_lv_4')) {
            Schema::table('users', function (Blueprint $table) {
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
        if (Schema::hasColumn('users', 'is_guest')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_guest');
            });
        }
        if (Schema::hasColumn('users', 'name_lv_1')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name_lv_1');
            });
        }
        if (Schema::hasColumn('users', 'name_lv_2')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name_lv_2');
            });
        }
        if (Schema::hasColumn('users', 'name_lv_3')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name_lv_3');
            });
        }
        if (Schema::hasColumn('users', 'name_lv_4')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name_lv_4');
            });
        }
    }
}
