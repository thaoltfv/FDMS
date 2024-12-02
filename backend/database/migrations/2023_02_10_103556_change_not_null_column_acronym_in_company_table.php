<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNotNullColumnAcronymInCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraiser_companies', 'acronym')) {
            Schema::table('appraiser_companies', function (Blueprint $table) {
                $table->string('acronym')->nullable()->change();
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
        if (Schema::hasColumn('appraiser_companies', 'acronym')) {
            Schema::table('appraiser_companies', function (Blueprint $table) {
                $table->string('acronym')->nullable(false)->change();
            });
        }
    }
}
