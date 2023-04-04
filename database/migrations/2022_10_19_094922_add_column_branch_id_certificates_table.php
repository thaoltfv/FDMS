<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBranchIdCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'branch_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('branch_id')->unsigned()->nullable();
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
        if (Schema::hasColumn('certificates', 'branch_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
}
