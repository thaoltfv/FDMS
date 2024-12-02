<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('province_id');
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('district_id');
            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('cascade');
            $table->integer('province_id');
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('streets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('ward_id')->nullable();
            $table->integer('district_id');
            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('cascade');
            $table->integer('province_id');
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('distances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('detail');
            $table->integer('vt1')->nullable();
            $table->integer('vt2')->nullable();
            $table->integer('vt3')->nullable();
            $table->integer('vt4')->nullable();

            $table->integer('street_id');
            $table->foreign('street_id')
                ->references('id')
                ->on('streets')
                ->onDelete('cascade');

            $table->integer('district_id');
            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('cascade');
            $table->integer('province_id');
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
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
        Schema::dropIfExists('distances');
        Schema::dropIfExists('streets');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
}
