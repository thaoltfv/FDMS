<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSlugAppraiseOtherInformationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_other_information', 'acronym')) {
            Schema::table('appraise_other_information', function (Blueprint $table) {
                $table->json('acronym')->nullable()->after('type');
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
        if (Schema::hasColumn('appraise_other_information', 'acronym')) {
            Schema::table('appraise_other_information', function (Blueprint $table) {
                $table->dropColumn('acronym');
            });
        }
    }
}
