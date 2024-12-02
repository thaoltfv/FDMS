<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status');
            $table->integer('ticket_num');
            $table->string('document_num');
            $table->date('document_date');
            $table->string('certificate_num')->nullable();
            $table->date('certificate_date')->nullable();
            $table->string('petitioner_name')->nullable();
            $table->string('petitioner_phone')->nullable();
            $table->string('petitioner_address')->nullable();
            $table->string('address')->nullable();

            $table->integer('appraiser_id');
            $table->foreign('appraiser_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraiser_manager_id')->nullable();
            $table->foreign('appraiser_manager_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraiser_confirm_id')->nullable();
            $table->foreign('appraiser_confirm_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraise_purpose_id');
            $table->foreign('appraise_purpose_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->text('document_description');

            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_principle', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_principle_id');
            $table->foreign('certificate_principle_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_basis_property', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_basis_property_id');
            $table->foreign('certificate_basis_property_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_approach', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_approach_id');
            $table->foreign('certificate_approach_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_method_used', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_method_used_id');
            $table->foreign('certificate_method_used_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_legal_documents_on_valuation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_law_id');
            $table->foreign('certificate_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_legal_documents_on_land', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_law_id');
            $table->foreign('certificate_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('certificate_legal_documents_on_construction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_law_id');
            $table->foreign('certificate_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_legal_documents_on_local', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('certificate_law_id');
            $table->foreign('certificate_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_construction_company', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');

            $table->integer('construction_company_id')->nullable();
            $table->foreign('construction_company_id')
                ->references('id')
                ->on('appraisal_construction_companies')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_has_appraises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->integer('certificate_id');
            $table->string('version');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('certificate_comparison_factor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificate_id');
            $table->foreign('certificate_id')
                ->references('id')
                ->on('certificates')
                ->onDelete('cascade');
            $table->string('comparison_factor');
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
        Schema::dropIfExists('certificate_comparison_factor');
        Schema::dropIfExists('certificate_has_appraises');
        Schema::dropIfExists('certificate_principle');
        Schema::dropIfExists('certificate_basis_property');
        Schema::dropIfExists('certificate_approach');
        Schema::dropIfExists('certificate_method_used');
        Schema::dropIfExists('certificate_construction_company');
        Schema::dropIfExists('certificate_legal_documents_on_land');
        Schema::dropIfExists('certificate_legal_documents_on_valuation');
        Schema::dropIfExists('certificate_legal_documents_on_construction');
        Schema::dropIfExists('certificate_legal_documents_on_local');
        Schema::dropIfExists('certificates');
    }
}
