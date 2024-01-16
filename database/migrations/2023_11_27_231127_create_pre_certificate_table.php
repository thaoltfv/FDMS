<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (!Schema::hasTable('pre_certificates')) {
            Schema::create('pre_certificates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('certificate_id')->nullable();
                $table->foreign('certificate_id')
                    ->references('id')
                    ->on('certificates')
                    ->onDelete('cascade');
                $table->text('petitioner_name')->nullable();
                $table->text('petitioner_phone')->nullable();
                $table->text('petitioner_address')->nullable();
                $table->text('petitioner_identity_card')->nullable();
                $table->integer('customer_id')->nullable();
                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers')
                    ->onDelete('cascade');
                $table->integer('appraise_purpose_id');
                $table->foreign('appraise_purpose_id')
                    ->references('id')
                    ->on('appraise_other_information')
                    ->onDelete('cascade');
                $table->text('note')->unsigned()->nullable();



                $table->integer('appraiser_sale_id')->nullable();
                $table->foreign('appraiser_sale_id')
                    ->references('id')
                    ->on('appraisers')
                    ->onDelete('cascade');

                $table->integer('business_manager_id')->nullable();
                $table->foreign('business_manager_id')
                    ->references('id')
                    ->on('appraisers')
                    ->onDelete('cascade');

                $table->integer('appraiser_perform_id')->nullable();
                $table->foreign('appraiser_perform_id')
                    ->references('id')
                    ->on('appraisers')
                    ->onDelete('cascade');
                $table->bigInteger('total_preliminary_value')->unsigned()->nullable()->before('created_at');
                $table->text('cancel_reason')->nullable();
                $table->timestamp('status_updated_at')->useCurrent();
                $table->integer('status');
                $table->timestamp('created_at')->useCurrent();
                $table->uuid('created_by')->nullable();
                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->timestamp('updated_at')->useCurrent();
                $table->uuid('updated_by')->nullable();
                $table->foreign('updated_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->softDeletes();
                $table->dateTime('status_expired_at')->nullable()->before('created_at');

                $table->text('address')->nullable();
                $table->integer('branch_id')->unsigned()->nullable();

                $table->float('commission_fee')->unsigned()->default(0)->before('created_at');
                $table->date('pre_date')->nullable()->before('created_at');
                $table->text('pre_asset_name')->unsigned()->nullable()->before('created_at');
                $table->bigInteger('total_service_fee')->unsigned()->default(0)->before('created_at');
                $table->integer('pre_type_id')->unsigned()->nullable()->before('created_at');
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
        Schema::dropIfExists('pre_certificates');
    }
}
