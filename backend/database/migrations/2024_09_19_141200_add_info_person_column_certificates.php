<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addInfoPersonColumnCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'info_person_send')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->text('info_person_send')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'info_person_receive')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->text('info_person_receive')->nullable();
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
        if (!Schema::hasColumn('certificates', 'info_person_send')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('info_person_send');
            });
        }
        if (!Schema::hasColumn('certificates', 'info_person_receive')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('info_person_receive');
            });
        }
    }
}
