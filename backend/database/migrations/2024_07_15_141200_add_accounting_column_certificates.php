<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addAccountingColumnCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'accounting_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('accounting_id')->nullable();
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
        if (!Schema::hasColumn('certificates', 'accounting_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('accounting_id');
            });
        }
    }
}
