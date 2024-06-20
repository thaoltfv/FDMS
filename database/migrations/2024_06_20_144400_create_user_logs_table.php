<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_logs')) {
            Schema::create('user_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('user_id')->nullable();
                $table->timestamp('last_login_at');
                $table->string('last_login_ip')->nullable();
                $table->string('browser_info')->nullable();
                $table->string('email')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
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
        Schema::dropIfExists('user_logs');
    }
}
