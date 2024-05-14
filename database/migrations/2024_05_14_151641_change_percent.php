<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVitri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_adapter', 'percent')) {
            Schema::table('appraise_adapter', function (Blueprint $table) {
                $table->float('percent')->change();
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
        // if (!Schema::hasColumn('appraise_adapter', 'percent')) {
        //     Schema::table('appraise_adapter', function (Blueprint $table) {
        //         $table->dropColumn('percent');
        //     });
        // }
    }
}
