<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompareAssetGeneralPendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compare_asset_general_pends', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_property_pends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('casset_general_pend_id');
            $table->foreign('casset_general_pend_id')
                ->references('id')
                ->on('compare_asset_general_pends')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_tangible_asset_pends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('casset_general_pend_id');
            $table->foreign('casset_general_pend_id')
                ->references('id')
                ->on('compare_asset_general_pends')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_machine_pends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('casset_general_pend_id');
            $table->foreign('casset_general_pend_id')
                ->references('id')
                ->on('compare_asset_general_pends')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_pic_pends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('casset_general_pend_id');
            $table->foreign('casset_general_pend_id')
                ->references('id')
                ->on('compare_asset_general_pends')
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
        Schema::dropIfExists('compare_property_pends');
        Schema::dropIfExists('compare_tangible_asset_pends');
        Schema::dropIfExists('compare_machine_pends');
        Schema::dropIfExists('compare_pic_pends');

        Schema::dropIfExists('compare_asset_general_pends');
    }
}
