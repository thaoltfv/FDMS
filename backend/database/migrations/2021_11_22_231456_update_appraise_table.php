<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraises', function (Blueprint $table) {
            $table->integer('appraise_principle_id')->nullable();
            $table->foreign('appraise_principle_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');
        });

        Schema::table('appraise_pics', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->integer('type_id')->nullable();
            $table->foreign('type_id')
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
