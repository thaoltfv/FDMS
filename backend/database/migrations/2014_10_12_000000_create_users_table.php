<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->default('');
            $table->string('address')->default('');
            $table->string('acronym');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('address')->default('');
            $table->string('name')->default('');
            $table->string('phone')->default('');
            $table->date('birthday')->nullable();
            $table->string('image')->default('');
            $table->integer('branch_id')->nullable();
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->onDelete('cascade');
            $table->string('appraisers_number')->default('');
            $table->string('mailing_address')->default('');
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('branches');
    }
}
