<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCustomerGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customer_groups_first')) {
            Schema::create('customer_groups_first', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('customer_groups_second')) {
            Schema::create('customer_groups_second', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('first_id')->nullable();
                // $table->foreign('first_id')
                //     ->references('id')
                //     ->on('customer_groups_first')
                //     ->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable('customer_groups_third')) {
            Schema::create('customer_groups_third', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('first_id')->nullable();
                // $table->foreign('first_id')
                //     ->references('id')
                //     ->on('customer_groups_first')
                //     ->onDelete('cascade');
                $table->integer('second_id')->nullable();
                // $table->foreign('second_id')
                //     ->references('id')
                //     ->on('customer_groups_second')
                //     ->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('customer_groups_fourth')) {
            Schema::create('customer_groups_fourth', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('first_id')->nullable();
                // $table->foreign('first_id')
                //     ->references('id')
                //     ->on('customer_groups_first')
                //     ->onDelete('cascade');

                $table->integer('second_id')->nullable();
                // $table->foreign('second_id')
                //     ->references('id')
                //     ->on('customer_groups_second')
                //     ->onDelete('cascade');
                $table->integer('third_id')->nullable();
                // $table->foreign('third_id')
                //     ->references('id')
                //     ->on('customer_groups_third')
                //     ->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
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
        Schema::dropIfExists('customer_groups_first');
        Schema::dropIfExists('customer_groups_second');
        Schema::dropIfExists('customer_groups_third');
        Schema::dropIfExists('customer_groups_fourth');
    }
}
