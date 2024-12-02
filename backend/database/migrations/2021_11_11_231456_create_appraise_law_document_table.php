<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseLawDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraise_law_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('document_type')->nullable();
            $table->string('type')->nullable();
            $table->string('date')->nullable();
            $table->text('content')->nullable();
            $table->string('provinces')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('is_defaults')->default(true);
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
        Schema::dropIfExists('appraise_law_documents');
    }
}
