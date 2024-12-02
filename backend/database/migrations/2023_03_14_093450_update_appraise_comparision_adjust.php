<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseComparisionAdjust extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_comparison_factor', 'adjust_percent')) {
            Schema::table('appraise_comparison_factor', function (Blueprint $table) {
                $table->float('adjust_percent')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('appraise_comparison_factor', 'adjust_percent')) {
            Schema::table('appraise_comparison_factor', function (Blueprint $table) {
                $table->integer('adjust_percent')->change();
            });
        }
    }
}
