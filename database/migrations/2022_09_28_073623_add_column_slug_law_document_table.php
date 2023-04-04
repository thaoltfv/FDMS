<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSlugLawDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_law_documents', 'slug')) {
            Schema::table('appraise_law_documents', function (Blueprint $table) {
				$table->json('slug')->nullable()->before('created_at');
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
        if (Schema::hasColumn('appraise_law_documents', 'slug')) {
            Schema::table('appraise_law_documents', function (Blueprint $table) {
				$table->dropColumn('slug');
            });
        }
    }
}
