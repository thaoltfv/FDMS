<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addOtherAssetsColumnCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'other_assets')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->jsonb('other_assets')->nullable();
            });
        }
        if (!Schema::hasColumn('pre_certificates', 'other_assets')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->jsonb('other_assets')->nullable();
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
        if (!Schema::hasColumn('certificates', 'other_assets')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('other_assets');
            });
        }
        if (!Schema::hasColumn('pre_certificates', 'other_assets')) {
            Schema::table('pre_certificates', function (Blueprint $table) {
                $table->dropColumn('other_assets');
            });
        }
    }
}
