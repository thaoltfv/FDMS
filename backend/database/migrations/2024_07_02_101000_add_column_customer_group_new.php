<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnCustomerGroupNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dictionaries', 'customer_first_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('customer_first_id')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'customer_second_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('customer_second_id')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'customer_third_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('customer_third_id')->nullable();
            });
        }
        if (!Schema::hasColumn('dictionaries', 'customer_fourth_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->integer('customer_fourth_id')->nullable();
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
        if (Schema::hasColumn('dictionaries', 'customer_first_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('customer_first_id');
            });
        }
        if (Schema::hasColumn('dictionaries', 'customer_second_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('customer_second_id');
            });
        }
        if (Schema::hasColumn('dictionaries', 'customer_third_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('customer_third_id');
            });
        }
        if (Schema::hasColumn('dictionaries', 'customer_fourth_id')) {
            Schema::table('dictionaries', function (Blueprint $table) {
                $table->dropColumn('customer_fourth_id');
            });
        }
    }
}
