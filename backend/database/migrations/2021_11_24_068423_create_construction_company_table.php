<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstructionCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('construction_company', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->integer('construction_company_id')->nullable();
            $table->foreign('construction_company_id')
                ->references('id')
                ->on('appraisal_construction_companies')
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
        Schema::dropIfExists('construction_company');
    }
}
