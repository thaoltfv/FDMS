<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetitionerBirthdayCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'pre_certificate_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('pre_certificate_id')->nullable();
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
        if (!Schema::hasColumn('certificates', 'pre_certificate_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('pre_certificate_id');
            });
        }
    }
}
