<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnCheckCustomerCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'is_company')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('is_company')->default(0);
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
        if (Schema::hasColumn('certificates', 'is_company')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('is_company');
            });
        }
    }
}
