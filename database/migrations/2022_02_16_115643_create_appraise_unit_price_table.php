<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseUnitPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraise_unit_price', function (Blueprint $table) {
            $table->id();
            $table->integer('appraise_id');
            $table->integer('asset_general_id');
            $table->integer('update');
            $table->integer('land_type_id');
            $table->integer('position_type_id');
            $table->integer('original_value');
            $table->integer('update_value')->nullable();
			$table->uuid('created_by');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('appraise_unit_price');
    }
}
