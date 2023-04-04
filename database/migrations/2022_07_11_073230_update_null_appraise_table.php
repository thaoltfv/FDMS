<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNullAppraiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraises', function (Blueprint $table) {
            $table->integer('appraise_basis_property_id')->unsigned()->nullable()->change();
            $table->integer('appraise_approach_id')->unsigned()->nullable()->change();
            $table->integer('appraise_method_used_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
