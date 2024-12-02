<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLawDateIntoAppraiseLawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_law', 'law_date')) {
            Schema::table('appraise_law', function (Blueprint $table) {
				$table->dateTime('law_date')->nullable()->before('created_at');
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
        if (Schema::hasColumn('appraise_law', 'law_date')) {
            Schema::table('appraise_law', function (Blueprint $table) {
				$table->dropColumn('law_date');
            });
        }
    }
}
