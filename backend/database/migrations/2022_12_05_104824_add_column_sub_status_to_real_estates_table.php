<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSubStatusToRealEstatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('real_estates', 'sub_status')) {
            Schema::table('real_estates', function (Blueprint $table) {
                $table->integer('sub_status')->default(1);
            });
        }
        if (! Schema::hasColumn('appraises', 'sub_status')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->integer('sub_status')->default(1);
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
        if (Schema::hasColumn('real_estates', 'sub_status')) {
            Schema::table('real_estates', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
        if (Schema::hasColumn('appraises', 'sub_status')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
    }
}
