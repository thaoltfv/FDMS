<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateDictionariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('certificate_dictionaries')) {
            Schema::create('certificate_dictionaries', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type');
                $table->string('acronym');
                $table->string('description');
                $table->string('useful_year')->nullable();
                $table->integer('status')->default(1);
                $table->string('created_by')->default('admin');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
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
        Schema::dropIfExists('certificate_dictionaries');
    }
}
