<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiserCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraisers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('appraiser_number');
            $table->integer('appraise_position_id');
            $table->foreign('appraise_position_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('appraiser_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('acronym');
            $table->string('address');
            $table->string('phone_number');
            $table->string('fax_number');
            $table->string('link')->nullable();
            $table->integer('appraiser_id');
            $table->foreign('appraiser_id')
                ->references('id')
                ->on('appraisers')
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
        Schema::dropIfExists('appraiser_companies');
        Schema::dropIfExists('appraisers');
    }
}
