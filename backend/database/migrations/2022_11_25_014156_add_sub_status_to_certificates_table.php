<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubStatusToCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('certificates', 'sub_status')) {
            Schema::table('certificates', function (Blueprint $table) {
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
        if (Schema::hasColumn('certificates', 'sub_status')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('sub_status');
            });
        }
    }
}
