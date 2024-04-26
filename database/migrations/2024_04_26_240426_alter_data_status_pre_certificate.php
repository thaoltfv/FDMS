<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterDataStatusPreCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pre_certificate')
            ->where('status', 4)
            ->update(['status' => 5]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('pre_certificate')
            ->where('status', 5)
            ->update(['status' => 4]);
    }
}
