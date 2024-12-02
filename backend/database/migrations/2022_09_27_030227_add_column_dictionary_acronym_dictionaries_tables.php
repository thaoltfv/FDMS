<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDictionaryAcronymDictionariesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('dictionaries', 'dictionary_acronym')) {
            Schema::table('dictionaries', function (Blueprint $table) {
				$table->string('dictionary_acronym')->nullable()->before('created_at');
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
        if (Schema::hasColumn('dictionaries', 'dictionary_acronym')) {
            Schema::table('dictionaries', function (Blueprint $table) {
				$table->dropColumn('dictionary_acronym');
            });
        }
    }
}
