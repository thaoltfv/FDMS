<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreCertificateExportDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pre_certificate_export_documents')) {
            Schema::create('pre_certificate_export_documents', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pre_certificate_id');
                $table->foreign('pre_certificate_id')
                    ->references('id')
                    ->on('pre_certificates')
                    ->onDelete('cascade');
                $table->integer('certificate_id')->nullable();
                $table->foreign('certificate_id')
                    ->references('id')
                    ->on('certificates')
                    ->onDelete('cascade');
                $table->text('name')->nullable();
                $table->text('link')->nullable();
                $table->text('type')->nullable();
                $table->text('size')->nullable();
                $table->text('description')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->uuid('created_by')->nullable();
                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->timestamp('updated_at')->useCurrent();
                $table->text('type_document');
                $table->softDeletes();

                $table->unique(['certificate_id', 'type_document']);
                $table->unique(['pre_certificate_id', 'type_document']);
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
        Schema::dropIfExists('pre_certificate_export_documents');
    }
}
