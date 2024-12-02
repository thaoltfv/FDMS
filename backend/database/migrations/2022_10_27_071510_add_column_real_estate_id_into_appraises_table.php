<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRealEstateIdIntoAppraisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraises', 'real_estate_id')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->integer('real_estate_id')->unsigned()->nullable();
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
        if (Schema::hasColumn('appraises', 'real_estate_id')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->dropColumn('real_estate_id');
            });
        }
    }
}
