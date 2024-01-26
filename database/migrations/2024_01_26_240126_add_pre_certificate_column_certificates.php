<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addPreCertificateColumnCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'total_preliminary_value')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->bigInteger('total_preliminary_value')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'pre_type_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('pre_type_id')->nullable();
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
        if (!Schema::hasColumn('certificates', 'total_preliminary_value')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('total_preliminary_value');
            });
        }
        if (!Schema::hasColumn('certificates', 'pre_type_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('pre_type_id');
            });
        }
    }
}
