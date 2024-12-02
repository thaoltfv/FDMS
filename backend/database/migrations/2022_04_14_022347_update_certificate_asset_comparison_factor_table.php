<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CertificateAssetComparisonFactor;

class UpdateCertificateAssetComparisonFactorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $datas = CertificateAssetComparisonFactor::where('type', 'quy_mo')->get();
		foreach($datas as $data) {
			$appraiseTitle = $data->appraise_title;
			$appraiseTitle = str_replace('.','',$appraiseTitle);
			$appraiseTitle = str_replace(',','.',$appraiseTitle);
			$assetTitle = $data->asset_title;
			$assetTitle = str_replace('.','',$assetTitle);
			$assetTitle = str_replace(',','.',$assetTitle);
			CertificateAssetComparisonFactor::where('id', $data->id)->update([
				'appraise_title' => $appraiseTitle,
				'asset_title' => $assetTitle
			]);
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
