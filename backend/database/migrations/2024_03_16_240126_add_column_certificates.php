<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'business_manager_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('business_manager_id')->nullable();
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
        if (Schema::hasColumn('certificates', 'business_manager_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('business_manager_id');
            });
        }
    }
}
