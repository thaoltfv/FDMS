<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseComparisonFactorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::Create('appraise_comparison_factor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id')->nullable();
            $table->integer('asset_general_id')->nullable();
            $table->integer('status');
            $table->string('type');
            $table->string('appraise_title');
            $table->string('asset_title');
            $table->string('description');
            $table->integer('adjust_percent');
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
        Schema::dropIfExists('appraise_comparison_factor');
    }
}
