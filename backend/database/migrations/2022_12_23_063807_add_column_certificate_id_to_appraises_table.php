<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCertificateIdToAppraisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('real_estates', 'certificate_id')) {
            Schema::table('real_estates', function (Blueprint $table) {
                $table->integer('certificate_id')->unsigned()->nullable();
            });
        }
        if (!Schema::hasColumn('appraises', 'certificate_id')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->integer('certificate_id')->unsigned()->nullable();
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
        if (Schema::hasColumn('real_estates', 'certificate_id')) {
            Schema::table('real_estates', function (Blueprint $table) {
                $table->dropColumn('certificate_id');
            });
        }
        if (Schema::hasColumn('appraises', 'certificate_id')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->dropColumn('certificate_id');
            });
        }
    }
}
