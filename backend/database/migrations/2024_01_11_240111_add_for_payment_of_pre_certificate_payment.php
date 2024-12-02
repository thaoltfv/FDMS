<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForPaymentOfPreCertificatePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('pre_certificate_payments', 'for_payment_of')) {
            Schema::table('pre_certificate_payments', function (Blueprint $table) {
                $table->text('for_payment_of')->nullable();
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
        if (!Schema::hasColumn('pre_certificate_payments', 'for_payment_of')) {
            Schema::table('pre_certificate_payments', function (Blueprint $table) {
                $table->dropColumn('for_payment_of');
            });
        }
    }
}
