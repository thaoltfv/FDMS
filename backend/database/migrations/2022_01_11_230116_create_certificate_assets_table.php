<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		\DB::statement('CREATE TABLE certificate_assets ( LIKE appraises INCLUDING ALL )');
		\DB::statement('INSERT INTO certificate_assets SELECT * FROM appraises');
		Schema::table('certificate_assets', function (Blueprint $table) {
			$table->integer('appraise_id')->nullable();
        });
		\DB::statement('update certificate_assets set appraise_id=id;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_assets');
    }
}
