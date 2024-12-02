<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnPreCertifcateConfigsOldConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('pre_certificate_configs', 'old_config')) {
            Schema::table('pre_certificate_configs', function (Blueprint $table) {
                $table->jsonb('old_config')->nullable();
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
        if (Schema::hasColumn('pre_certificate_configs', 'old_config')) {
            Schema::table('pre_certificate_configs', function (Blueprint $table) {
                $table->dropColumn('old_config');
            });
        }
    }
}
