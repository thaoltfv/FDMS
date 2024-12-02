<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnCustomerGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'customer_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('customer_group_id')->nullable();
            });
        }
        if (!Schema::hasColumn('customers', 'customer_group_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->integer('customer_group_id')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'customer_group_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('customer_group_id')->nullable();
            });
        }
        if (!Schema::hasColumn('pre_certificates', 'customer_group_id')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->integer('customer_group_id')->nullable();
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
        if (Schema::hasColumn('users', 'customer_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('customer_group_id');
            });
        }
        if (Schema::hasColumn('customers', 'customer_group_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('customer_group_id');
            });
        }
        if (Schema::hasColumn('certificates', 'customer_group_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('customer_group_id');
            });
        }
        if (Schema::hasColumn('pre_certificates', 'customer_group_id')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('customer_group_id');
            });
        }
    }
}
