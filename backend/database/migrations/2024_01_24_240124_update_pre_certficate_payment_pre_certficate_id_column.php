<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePreCertficatePaymentPreCertficateIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_certificate_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('pre_certificate_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pre_certificate_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('pre_certificate_id')->change();
        });
    }
}