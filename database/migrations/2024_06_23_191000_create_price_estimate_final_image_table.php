<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceEstimateFinalImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('price_estimate_final_images')) {
            Schema::create('price_estimate_final_images', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('price_estimate_final_id');
                $table->text('link');
                $table->text('picture_type');
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
        Schema::dropIfExists('price_estimate_final_images');
    }
}
