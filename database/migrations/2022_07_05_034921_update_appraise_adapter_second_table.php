<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\AppraiseAdapter;
use App\Services\CommonService;

class UpdateAppraiseAdapterSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_adapter', 'change_purpose_price')) {
			Schema::table('appraise_adapter', function (Blueprint $table) {
				$table->float('change_purpose_price')->nullable()->before('created_at');
			});
			$items = AppraiseAdapter::get();
			foreach($items as $item) {
				//var_dump("=================================================");
				//var_dump($item->appraise_id);
				$cpcdmdsd = 0;
				try {
					$cpcdmdsd = CommonService::getCPCDMDSD($item->appraise_id, $item->asset_general_id);
				} catch (\Exception $e) {
					//var_dump($e->getErrorMessage());
				}
				
				if($cpcdmdsd) {
					AppraiseAdapter::whereId($item->id)->update([
						'change_purpose_price' => $cpcdmdsd
					]);
					sleep(1);
				}
				//var_dump($cpcdmdsd);
			}
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
