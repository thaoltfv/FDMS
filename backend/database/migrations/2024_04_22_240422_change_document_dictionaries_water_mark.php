<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeDocumentDictionariesWaterMark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('document_dictionaries')
            ->where('slug', 'print_watermask')
            ->update(['value' => 'no']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('document_dictionaries')
            ->where('slug', 'print_watermask')
            ->update(['value' => 'yes']);
    }
}
