<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCertificateAssetVersionSequence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   try {
            DB::beginTransaction();
            DB::statement("DROP SEQUENCE IF EXISTS appraise_versions_id_seq CASCADE");
            DB::statement("CREATE SEQUENCE appraise_versions_id_seq owned by appraise_versions.id");
            DB::statement("CREATE SEQUENCE certificate_asset_versions_id_seq owned by certificate_asset_versions.id");
            DB::statement("SELECT SETVAL('appraise_versions_id_seq', (SELECT MAX(id) + 1 FROM appraise_versions))");
            DB::statement("SELECT SETVAL('certificate_asset_versions_id_seq', (SELECT MAX(id) + 1 FROM certificate_asset_versions))");
            DB::statement("ALTER TABLE appraise_versions ALTER COLUMN id set default nextval('appraise_versions_id_seq')");
            DB::statement("ALTER TABLE certificate_asset_versions ALTER COLUMN id set default nextval('certificate_asset_versions_id_seq')");
            DB::statement("update certificate_asset_versions set version = 1");
            DB::statement("update appraise_versions set version = 1");
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
