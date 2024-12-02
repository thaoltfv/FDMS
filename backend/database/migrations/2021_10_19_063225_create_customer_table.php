<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('phone')->default('');
            $table->string('status')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('address')->nullable();
            $table->string('customer_picture')->nullable();
            $table->string('created_by')->default('');
            $table->string('created_date')->default('');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('customer_pics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
            $table->string('link')->nullable();
            $table->string('picture_type')->nullable();
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
        Schema::dropIfExists('customer_pics');
        Schema::dropIfExists('customers');
    }
}
