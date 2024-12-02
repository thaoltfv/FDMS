<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\CertificateAssetLaw;
use App\Models\CertificateAssetLawLandDetail;
use App\Models\CompareAssetGeneral;

class CreateCertificateAssetLawLandDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_asset_law_land_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('certificate_asset_law')
                ->onDelete('cascade');
			$table->integer('doc_no')->nullable();
            $table->integer('land_no')->nullable();
			$table->integer('doc_no_old')->nullable();
            $table->integer('land_no_old')->nullable();
			$table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
		
		foreach(CertificateAssetLaw::withTrashed()->get() as $item) {
			CertificateAssetLawLandDetail::create([
				'appraise_law_id' => $item->id,
				'doc_no' => $item->doc_no,
				'land_no' => $item->land_no,
			]);
		}
		/* foreach(CompareAssetGeneral::get() as $item) {
			CompareAssetGeneral::where('id', $item->id)->update([
				'adjust_percent' => ($item->adjust_percent+100)
			]);
		} */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_asset_law_land_details');
    }
}
