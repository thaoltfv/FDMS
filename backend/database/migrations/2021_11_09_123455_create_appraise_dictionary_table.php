<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseDictionaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraise_dictionaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('appraise_title');
            $table->integer('appraise_point');
            $table->string('asset_title');
            $table->integer('asset_point');
            $table->string('description');
            $table->integer('difference_point');
            $table->integer('appraise_percent');
            $table->integer('asset_percent');
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
        Schema::dropIfExists('appraise_dictionaries');

    }
}
