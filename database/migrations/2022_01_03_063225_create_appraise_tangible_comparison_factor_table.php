<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseTangibleComparisonFactorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::Create('appraise_tangible_comparison_factor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id')->nullable();
            $table->integer('p1')->default(0);
            $table->integer('h1')->default(0);
            $table->integer('p2')->default(0);
            $table->integer('h2')->default(0);
            $table->integer('p3')->default(0);
            $table->integer('h3')->default(0);
            $table->integer('d4')->default(0);
            $table->integer('h4')->default(0);
            $table->integer('p5')->default(0);
            $table->integer('h5')->default(0);

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
        Schema::dropIfExists('appraise_tangible_comparison_factor');
    }
}
