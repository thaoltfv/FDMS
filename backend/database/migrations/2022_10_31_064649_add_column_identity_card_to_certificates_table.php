<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdentityCardToCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'petitioner_identity_card')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->string('petitioner_identity_card')->nullable();
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
        if (!Schema::hasColumn('certificates', 'petitioner_identity_card')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('petitioner_identity_card');
            });
        }
    }
}
