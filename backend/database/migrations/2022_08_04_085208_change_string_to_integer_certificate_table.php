<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStringToIntegerCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('certificates', 'appraiser_sale_id')) {
            DB::statement('ALTER TABLE certificates ALTER COLUMN
            appraiser_sale_id TYPE integer USING (appraiser_sale_id)::integer');
		}
		if (Schema::hasColumn('certificates', 'appraiser_perform_id')) {
			DB::statement('ALTER TABLE certificates ALTER COLUMN
            appraiser_perform_id TYPE integer USING (appraiser_perform_id)::integer');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
