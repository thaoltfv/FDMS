<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnCustomerGroupUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'first_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('first_id')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'second_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('second_id')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'third_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('third_id')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'fourth_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('fourth_id')->nullable();
            });
        }


        if (!Schema::hasColumn('dictionaries', 'first_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('first_id')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'second_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('second_id')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'third_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('third_id')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'fourth_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('fourth_id')->nullable();
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
        if (Schema::hasColumn('users', 'first_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('first_id');
            });
        }
        if (Schema::hasColumn('users', 'second_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('second_id');
            });
        }
        if (Schema::hasColumn('users', 'third_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('third_id');
            });
        }
        if (Schema::hasColumn('users', 'fourth_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('fourth_id');
            });
        }
        if (Schema::hasColumn('dictionaries', 'first_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('first_id');
            });
        }
        if (Schema::hasColumn('dictionaries', 'second_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('second_id');
            });
        }
        if (Schema::hasColumn('dictionaries', 'third_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('third_id');
            });
        }
        if (Schema::hasColumn('dictionaries', 'fourth_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('fourth_id');
            });
        }
    }
}
