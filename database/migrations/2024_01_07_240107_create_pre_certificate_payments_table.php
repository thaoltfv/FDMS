<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreCertificatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('pre_certificate_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pre_certificate_id');
            $table->foreign('pre_certificate_id')
                ->references('id')
                ->on('pre_certificates')
                ->onDelete('cascade');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates') 
                ->onDelete('cascade'); 
			$table->date('pre_date')->nullable();
		    $table->integer('amount')->unsigned()->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_certificate_payments');
    }
}
