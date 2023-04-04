<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStepColumnToAppraisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraises', 'step')) {
            Schema::table('appraises', function (Blueprint $table) {
				$table->integer('step')->nullable()->before('created_at');
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
        if (Schema::hasColumn('appraises', 'step')){
            Schema::table('appraises', function (Blueprint $table) {
                $table->dropColumn('step');
            });
        }
    }
}
