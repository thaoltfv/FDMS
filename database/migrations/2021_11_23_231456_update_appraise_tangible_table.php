<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseTangibleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraise_other_assets', function (Blueprint $table) {
            $table->string('other_building')->nullable();
            $table->integer('rate_id')->nullable();
            $table->foreign('rate_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('structure_id')->nullable();
            $table->foreign('structure_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('crane_id')->nullable();
            $table->foreign('crane_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('aperture_id')->nullable();
            $table->foreign('aperture_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('factory_type_id')->nullable();
            $table->foreign('factory_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
