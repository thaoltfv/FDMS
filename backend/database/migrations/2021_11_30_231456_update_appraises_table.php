<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraise_other_assets', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        Schema::table('appraise_other_information', function (Blueprint $table) {
            $table->boolean('is_defaults')->default(false);
        });

        Schema::table('appraise_law', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->string('expiry_type')->nullable();
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
