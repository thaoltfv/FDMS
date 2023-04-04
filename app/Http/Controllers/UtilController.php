<?php

namespace App\Http\Controllers;

use App\Enum\RoleDefault;
use App\Models\ApartmentAsset;
use App\Models\ApartmentAssetProperty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use DB;
use App\Models\CompareAssetGeneral;
use App\Models\CompareProperty;
use App\Repositories\EloquentCompareAssetGeneralRepository;

use App\Models\Appraise;
use App\Models\CertificateAsset;
use App\Models\Certificate;
use App\Models\Street;
use App\Models\AppraiseAdapter;

use App\Models\Dictionary;
use App\Models\AppraiseLawDetail;
use App\Models\CertificateAssetLawDetail;

use App\Models\AppraisalConstructionCompany;
use App\Models\AppraiseComparisonFactor;
use App\Models\AppraisePropertyDetail;
use App\Models\Appraiser;
use App\Models\AppraiseTangibleAsset;
use App\Models\Branch;
use App\Models\CertificateApartmentProperty;
use App\Models\CertificateAssetComparisonFactor;
use App\Models\ConstructionCompany;
use App\Models\CertificateAssetConstructionCompany;
use App\Models\RealEstate;
use App\Models\User;
use App\Services\CommonService;
use Symfony\Component\VarDumper\VarDumper;

class UtilController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function serverTime(): JsonResponse
	{
		$now = now();
		$milliseconds = substr((string)$now->micro, 0, 3);
        $appENV = env('APP_ENV', 'Unknow');
		$response = $now->format('Y-m-d\TH:i:s') . '.' . $milliseconds . 'Z';

		return $this->respondWithCustomData(['message' => $response, 'env' => $appENV], Response::HTTP_OK);
	}

	/**
	 * @return JsonResponse
	 */
	public function runFix()
	{

		$type = request()->get('type');

		if (!empty($type) && ($type == 1)) {

			$compareAssetGenerals = DB::select(DB::raw("SELECT compare_asset_generals.id, compare_asset_generals.street_id, streets.name, streets.province_id, streets.district_id FROM compare_asset_generals LEFT JOIN streets ON compare_asset_generals.street_id = streets.id WHERE streets.deleted_at is not null AND migrate_status = 'TSS'"));
			$i = 1;
			$j = 0;
			foreach ($compareAssetGenerals as $item) {

				$street = Street::query()
				->whereName($item->name)
				->where('district_id',$item->district_id)
				->where('province_id',$item->province_id)
				->whereNull('deleted_at')
				->first();

				$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
				if ($street) {
					dump($i . ': ' . $item->id . " - " . $item->name);
					$j += 1;
					dump('Old id: ' . $item->street_id);
					dump('New id: ' . $street->id);
					$compareAssetGeneralRepository->updateStatusCompareAssetGeneral('['.$item->id.']', ["street_id" => $street->id]);

				} else {
					$compareAssetGeneralRepository->updateStatusCompareAssetGeneral('['.$item->id.']', ["status" => 2]);
					dump($item->id);
				}
				$i += 1;
			}
			dd($j);
		}

		if (!empty($type) && ($type == 2)) {
			$certificates = Certificate::with('appraises.createdBy')
				->with('appraises')
				->get();
			echo '<pre>';
			foreach ($certificates as $certificate) {
				foreach ($certificate->appraises as $appraise) {
					$appraise = Appraise::where('id', $appraise->appraise_id)->first();
					if ($appraise->created_by !== $certificate->created_by) {
						var_dump("certificate->id");
						var_dump($certificate->id);
						var_dump($appraise->id);
					}
				}
			}
		}

		if (!empty($type) && ($type == 3)) {
			$certificates = Certificate::with('appraises.createdBy')
				->with('appraises')
				->get();
			echo '<pre>';
			foreach ($certificates as $certificate) {
				//var_dump($certificate->id);
				foreach ($certificate->appraises as $appraise) {
					$appraiseTmp = Appraise::where('id', $appraise->appraise_id)->first();
					if ($appraiseTmp->created_by !== $certificate->created_by) {
						//var_dump($appraise->appraise_id);
						var_dump("certificate->id");
						var_dump($certificate->id);
						var_dump($appraise->appraise_id);
						Appraise::where('id', $appraise->appraise_id)->update([
							'created_by' => $certificate->created_by
						]);
						CertificateAsset::where('id', $appraise->id)->update([
							'created_by' => $certificate->created_by
						]);
						//sleep(1);
					}
				}
				//var_dump("--------------------------------------------");
			}
		}

		if (!empty($type) && ($type == 4)) {
			//update asset_general_land_sum_area
			$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
			$compareAssetGenerals = CompareAssetGeneral::with('properties.propertyDetail')->whereMigrateStatus('TSS')->get();
			$count = 0;
			foreach ($compareAssetGenerals as $compareAssetGeneral) {
				$flag = false;
				foreach ($compareAssetGeneral->properties as $property) {
					$sum = 0;
					foreach($property->propertyDetail as $propertyDetail) {
						$sum += $propertyDetail->total_area;
					}
					if(round($property->asset_general_land_sum_area, 2) != round($sum, 2)) {
						dump($compareAssetGeneral->id . ": " . $sum . " : " . $property->asset_general_land_sum_area);
						$flag = true;
						CompareProperty::where('id', $property->id)->update(['asset_general_land_sum_area' => $sum]);
					}
				}
				if ($flag) {
					$count += 1;
					$rows = $compareAssetGeneralRepository->findById($compareAssetGeneral->id);
					$compareAssetGeneralRepository->indexData($rows);
				}
			}
			dd($count);
		}

		if (!empty($type) && ($type == 5)) {
			//change acronym of land type
			$values = [
				'CÂY HÀNG NĂM' => 'BHK',
				'NUÔI TRỒNG THỦY SẢN' => 'NTS',
				'LÚA' => 'LUA',
				'ĐẤT THƯƠNG MẠI DỊCH VỤ TẠI ĐÔ THỊ' => 'TMD',
				'ĐẤT THƯƠNG MẠI DỊCH VỤ TẠI NÔNG THÔN' => 'TMD',
				'ĐẤT SẢN XUẤT KINH DOANH TẠI NÔNG THÔN' => 'SKC',
				'ĐẤT SẢN XUẤT KINH DOANH TẠI ĐÔ THỊ' => 'SKC',
				'ĐẤT KHU CÔNG NGHIỆP' => 'SKK',
				'ĐẤT CỤM CÔNG NGHIỆP' => 'SKN',
			];
			echo '<pre>';
			foreach($values as $key=>$value) {
				$item = Dictionary::where('type', 'LOAI_DAT_CHI_TIET')->where('description', $key)->first();
				if(isset($item)) {
					var_dump($key . ' : có');
					var_dump('Acronym: ' . $item->acronym);
					Dictionary::where('id', $item->id)->update([
						'acronym' => $value
					]);
				}
			}
		}

		if (!empty($type) && ($type == 6)) {
			// check tstd with tsss have migrate status TSC
			echo '<pre>';
			$appraises = Appraise::with('assetGeneral')->get();
			foreach($appraises as $appraise) {
				var_dump("=================================================");
				var_dump($appraise->id);
				foreach($appraise->assetGeneral as $assetGeneral) {
					if($assetGeneral->migrate_status=='TSC') {
						var_dump($assetGeneral->id);
					}
				}
			}
			dd($appraises);
		}

		if (!empty($type) && ($type == 7)) {
			// remove tsss have migrate status TSC
			echo '<pre>';
            $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
            $compareAssetGenerals = CompareAssetGeneral::whereMigrateStatus('TSC')
                ->whereStatus(1)
                ->doesntHave('appraiseHasAsset')
                ->get();

            var_dump(count($compareAssetGenerals));
            foreach($compareAssetGenerals as $item) {
                var_dump($item->id);
                $item->fill(['status' => 2])->save();
                $compareAssetGeneralRepository->indexData($item);
                usleep(200);
            }
		}

		if (!empty($type) && ($type == 8)) {
			// update tsss ELS
			echo '<pre>';
			$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
			$compareAssetGenerals = CompareAssetGeneral::select('id')->whereStatus(1)->get();
			//$compareAssetGenerals = CompareAssetGeneral::select('id')->whereMigrateStatus('TSS')->whereStatus(1)
			//$compareAssetGenerals = CompareAssetGeneral::whereIn('id', [6669, 6668, 6673])->get();
			foreach($compareAssetGenerals as $item) {
				var_dump("=================================================");
				var_dump($item->id);
				//if(isset($item->public_date	)) {
					$rows = $compareAssetGeneralRepository->findById($item->id);
					$compareAssetGeneralRepository->indexData($rows);
					usleep(200);
				//}

			}
		}

		if (!empty($type) && ($type == 9)) {
			// change appraise status 0 to 5
			echo '<pre>';
			$appraises = Appraise::whereStatus(0)->get();
			foreach($appraises as $appraise) {
				var_dump("=================================================");
				var_dump($appraise->id);
				Appraise::where('id', $appraise->id)->update([
					'status' => 5
				]);
			}
		}

		if (!empty($type) && ($type == 10)) {
			// fix construction_company data - get from appraisal_construction_company
			echo '<pre>';
			$items = ConstructionCompany::withTrashed()->get();
			foreach($items as $item) {
				var_dump("========================================");
				var_dump($item->id);
				if(isset($item->construction_company_id)) {
					$masterData = AppraisalConstructionCompany::whereId($item->construction_company_id)->first();
					ConstructionCompany::where('id', $item->id)->withTrashed()->update([
						'name' => $masterData->name,
						'address' => $masterData->address,
						'phone_number' => $masterData->phone_number,
						'manager_name' => $masterData->manager_name,
						'unit_price_m2' => $masterData->unit_price_m2,
						'is_defaults' => $masterData->is_defaults,
					]);
				} else {
					echo 'construction_company_id: '.$item->construction_company_id;
				}

			}
		}

		if (!empty($type) && ($type == 11)) {
			// fix construction_company data - get form certificate asset
			echo '<pre>';
			$items = CertificateAssetConstructionCompany::get();
			foreach($items as $item) {
				var_dump("=========================================");
				var_dump($item->id);
				if(isset($item->appraise->appraise_id)) {
					var_dump('appraise_id '.$item->appraise_id);
					var_dump('appraise->appraise_id '.$item->appraise->appraise_id);
					var_dump('company_id '.$item->company_id);
					ConstructionCompany::where('appraise_id', $item->appraise->appraise_id)->where('construction_company_id', $item->company_id)->update([
						'name' => $item->name,
						'address' => $item->address,
						'phone_number' => $item->phone_number,
						'manager_name' => $item->manager_name,
						'unit_price_m2' => $item->unit_price_m2,
						'is_defaults' => $item->is_defaults,
					]);
				} else {
					echo 'Not found.';
				}
			}
		}

		if (!empty($type) && ($type == 12)) {
			// convert expiry_date to right format
			echo '<pre>';
			$items = AppraiseLawDetail::get();
			foreach($items as $item) {
				var_dump("=================================================");
				var_dump($item->id);
				if (is_numeric(strtotime($item->expiry_date))) {
					if(date_create_from_format('Y-m-d', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('Y-m-d', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						AppraiseLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else if(date_create_from_format('d-m-Y', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('d-m-Y', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						AppraiseLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else {
						var_dump('Aldready convert');
					}
				} else {
					var_dump('Không '.$item->expiry_date);
				}
			}
		}

		if (!empty($type) && ($type == 13)) {
			// convert expiry_date to right format
			echo '<pre>';
			$items = CertificateAssetLawDetail::get();
			foreach($items as $item) {
				var_dump("=================================================");
				var_dump($item->id);
				if (is_numeric(strtotime($item->expiry_date))) {
					if(date_create_from_format('Y-m-d', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('Y-m-d', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						CertificateAssetLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else if(date_create_from_format('d-m-Y', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('d-m-Y', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						AppraiseLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else {
						var_dump('Aldready convert');
					}
				} else {
					var_dump('Không '.$item->expiry_date);
				}
			}
		}

		if (!empty($type) && ($type == 12)) {
			// convert expiry_date to right format
			echo '<pre>';
			$items = AppraiseLawDetail::get();
			foreach($items as $item) {
				var_dump("=================================================");
				var_dump($item->id);
				if (is_numeric(strtotime($item->expiry_date))) {
					if(date_create_from_format('Y-m-d', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('Y-m-d', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						AppraiseLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else if(date_create_from_format('d-m-Y', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('d-m-Y', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						AppraiseLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else {
						var_dump('Aldready convert');
					}
				} else {
					var_dump('Không '.$item->expiry_date);
				}
			}
		}

		if (!empty($type) && ($type == 13)) {
			// convert expiry_date to right format
			echo '<pre>';
			$items = CertificateAssetLawDetail::get();
			foreach($items as $item) {
				var_dump("=================================================");
				var_dump($item->id);
				if (is_numeric(strtotime($item->expiry_date))) {
					if(date_create_from_format('Y-m-d', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('Y-m-d', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						CertificateAssetLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else if(date_create_from_format('d-m-Y', $item->expiry_date)) {
						$newDateFromat = date_create_from_format('d-m-Y', $item->expiry_date)->format('d/m/Y');
						var_dump($newDateFromat);
						AppraiseLawDetail::whereId($item->id)->update([
							'expiry_date' => $newDateFromat
						]);
					} else {
						var_dump('Aldready convert');
					}
				} else {
					var_dump('Không '.$item->expiry_date);
				}
			}
		}

		if (!empty($type) && ($type == 14)) {
			// update appraise_adapter - change_purpose_price
			echo '<pre>';
			$items = AppraiseAdapter::get();
			foreach($items as $item) {
				var_dump("=================================================");
				var_dump($item->appraise_id);
				$cpcdmdsd = 0;
				try {
					$cpcdmdsd = CommonService::getCPCDMDSD($item->appraise_id, $item->asset_general_id);
				} catch (\Exception $e) {
					var_dump($e->getErrorMessage());
				}

				/* if($cpcdmdsd) {
					AppraiseAdapter::whereId($item->id)->update([
						'change_purpose_price' => $cpcdmdsd
					]);
					sleep(1);
				} */
				var_dump($cpcdmdsd);
			}
		}

        if (!empty($type) && ($type == 15)) {

            // update appraise_property_details - main_area

            echo '<pre>';

            $items = AppraisePropertyDetail::whereNull('main_area')->get();

            var_dump(count($items) . ' item cần xử lý');

            foreach($items as $item) {

                var_dump("=================================================");

                $area = null;
                $planing_area = $item->planning_area ? floatval($item->planning_area) : 0;
                if($item->total_area ) {

                    $area = floatval($item->total_area) - $planing_area;

                }

                if($item->id && $area) {
                    DB::statement('UPDATE appraise_property_details SET main_area = ' . $area . ' WHERE id = ?', [$item->id]);
                    var_dump($item->id . ': ok =>' . $area);

                }else
                    var_dump($item->id . ': Null =>' . $area);


                usleep(10);
            }

        }
        if (!empty($type) && ($type == 15.1)) {

            // update appraise_property_details - main_area

            echo '<pre>';

            $items = AppraisePropertyDetail::whereNull('main_area')->orWhere('main_area',0)->get();

            var_dump(count($items) . ' item cần xử lý');

            foreach($items as $item) {

                var_dump("=================================================");

                $area = null;
                $planing_area = $item->planning_area ? floatval($item->planning_area) : 0;
                if($item->total_area ) {

                    $area = floatval($item->total_area) - $planing_area;

                }

                if($item->id && $area) {
                    DB::statement('UPDATE appraise_property_details SET main_area = ' . $area . ' WHERE id = ?', [$item->id]);
                    var_dump($item->id . ': ok =>' . $area);

                }else
                    var_dump($item->id . ': Null =>' . $area);


                usleep(10);
            }

        }
        if (!empty($type) && ($type == 16)) {
            echo '<pre>';
            $datas = AppraiseTangibleAsset::with('buildingType')->whereNull('tangible_name')->get();
            if(isset($datas)){
                foreach($datas as $data){
                    if(isset($data->buildingType->description)){
                        var_dump($data->id . ': description =>' . $data->buildingType->description);
                        AppraiseTangibleAsset::where('id',$data->id)->update([
                            'tangible_name' => $data->buildingType->description
                        ]);
                        var_dump('done');
                    }
                    usleep(10);
                }
            }
            var_dump('Cập nhật thành công');
        }

        if (!empty($type) && ($type == 17)) {
            echo '<pre>';
            $datas = Appraiser::whereNull('user_id')->get();
            if(isset($datas)){
                foreach($datas as $data){
                    var_dump($data->id . ': name =>' . $data->name);
                    Appraiser::where('id',$data->id)->delete();
                    var_dump('done');
                    usleep(10);
                }
            }

            var_dump('Cập nhật thành công');
        }
        if (!empty($type) && ($type == 18)) {
            echo '<pre>';
            $datas = Appraiser::with('user')
                ->whereHas('user', function ($has){
                    $has->whereNotNull('deleted_at');
                })
                ->get();

            if(isset($datas)){
                foreach($datas as $data){
                    var_dump($data->id . ': name =>' . $data->name);
                    Appraiser::where('id',$data->id)->delete();
                    var_dump('done');
                    usleep(10);
                }
            }

            var_dump('Cập nhật thành công');
        }

        if (!empty($type) && ($type == 19)) {
            echo '<pre>';
            $datas = Certificate::query()->whereNull('branch_id')->whereNotNull('appraiser_sale_id')->get();
            // dd($datas);
            var_dump('có ' . count($datas) . ' dữ liệu cần cập nhật');

            if(isset($datas)){
                foreach($datas as $data){
                    $branchId = Appraiser::query()->where('id', $data->appraiser_sale_id)->first()->branch_id;
                    Certificate::query()->where('id', $data->id)->update(['branch_id' => $branchId]);
                    var_dump('done');
                    usleep(10);
                    }
                    var_dump('Cập nhật thành công');
                }
        }
        if (!empty($type) && ($type == 20)) {
            echo '<pre>';
            $errString = '"[\"BDS\"]"';
            $datas = Certificate::query()->whereRaw("document_type::jsonb ='". $errString."'")->get(['id']);
            // dd($datas);
            var_dump('có ' . count($datas) . ' dữ liệu cần cập nhật');

            if(isset($datas)){
                foreach($datas as $data){
                    Certificate::query()->where('id', $data->id)->update(['document_type' => array('BDS')]);
                    var_dump( $data->id .' - done');
                    usleep(10);
                    }
                    var_dump('Cập nhật thành công');
                }
        }
        if (!empty($type) && ($type == 21)) {
            echo '<pre>';
            // $datas = ::query()->whereRaw("document_type::jsonb ='". $errString."'")->get(['id']);
            // dd($datas);
            $datas = RealEstate::query()->with('assetUpdate')->whereNull('created_at')->orWhereNull('status')->get();
            var_dump('có ' . count($datas) . ' dữ liệu cần cập nhật');

            if(isset($datas)){
                foreach($datas as $data){
                    $createAt = $data->asset->created_at??now();
                    $updatedAt = $data->asset->updated_at??now();
                    $status = $data->asset->status??1;
                    var_dump( $createAt   .' - ' . $updatedAt . '-'. $status);
                    RealEstate::query()->where('id', $data->id)->update(['updated_at' => $updatedAt, 'created_at' => $createAt,'status' => $status]);
                    var_dump( $data->id .' - done');
                    usleep(10);
                    }
                    var_dump('Cập nhật thành công');
                }
        }
		if (!empty($type) && ($type == 22)) {
            echo '<pre>';
			var_dump('Cập nhật cho TSTĐ đã được chọn vào hoàn thành');
			if (Appraise::query()->whereIn('status', [3, 4])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->exists()) {
				Appraise::query()->whereIn('status', [3, 4])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->update(['step' => 7]);
			}
			var_dump('Cập nhật xong');
			var_dump('Cập nhật cho TSTĐ status 1 , 2 và có TSSS');
			if (Appraise::query()->whereIn('status', [1, 2])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->whereHas('appraiseHasAssets')->exists()) {
				Appraise::query()->whereIn('status', [1, 2])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->whereHas('appraiseHasAssets')->update(['step' => 6]);
			}
			var_dump('Cập nhật xong');

			var_dump('Cập nhật cho TSTĐ status 1 , 2 và chưa TSSS và có pháp lý');

			if (Appraise::query()->whereIn('status', [1, 2])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->whereDoesntHave('appraiseHasAssets')->whereHas('appraiseLaw')->exists()) {
				Appraise::query()->whereIn('status', [1, 2])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->whereDoesntHave('appraiseHasAssets')->whereHas('appraiseLaw')->update(['step' => 5]);
			}
			var_dump('Cập nhật xong');
			var_dump('Cập nhật cho TSTĐ status 1 , 2 và chưa TSSS và chưa có pháp lý');
			if (Appraise::query()->whereIn('status', [1, 2])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->whereDoesntHave('appraiseHasAssets')->whereDoesntHave('appraiseLaw')->exists()) {
				Appraise::query()->whereIn('status', [1, 2])->where(function($q){$q->whereNull('step')->orWhere('step', 1);})->whereDoesntHave('appraiseHasAssets')->whereDoesntHave('appraiseLaw')->update(['step' => 3]);
			}
			var_dump('Cập nhật xong');

            // $dataCertificateAppraise = Appraise::query()->whereIn('status', [3, 4])->get();
            // var_dump('có ' . count($datas) . ' dữ liệu cần cập nhật');

            // if(isset($datas)){
            //     foreach($datas as $data){
            //         $createAt = $data->asset->created_at??now();
            //         $updatedAt = $data->asset->updated_at??now();
            //         $status = $data->asset->status??1;
            //         var_dump( $createAt   .' - ' . $updatedAt . '-'. $status);
            //         RealEstate::query()->where('id', $data->id)->update(['updated_at' => $updatedAt, 'created_at' => $createAt,'status' => $status]);
            //         var_dump( $data->id .' - done');
            //         usleep(10);
            //         }
            //         var_dump('Cập nhật thành công');
            //     }
        }
		if (!empty($type) && ($type == 23)) {
            echo '<pre>';
			var_dump('Bắt đầu cập nhật');
			if (AppraiseAdapter::query()->whereNull('change_violate_price')->exists()) {

				AppraiseAdapter::query()->whereNull('change_violate_price')->update([
					'change_violate_price' => 0
				]);
			}
			if (AppraiseAdapter::query()->whereNull('change_purpose_price')->exists()) {
				AppraiseAdapter::query()->whereNull('change_purpose_price')->update([
					'change_purpose_price' => 0
				]);
			}
			var_dump('Cập nhật xong');
        }
		if (!empty($type) && ($type == 24)) {
            echo '<pre>';
			var_dump('Bắt đầu cập nhật cho tài sản');
			if (ApartmentAssetProperty::query()->whereNull('full_name')->exists()) {
				$datas = ApartmentAssetProperty::query()->with(['block:id,name', 'floor:id,name', 'apartmentAsset:id,project_id', 'apartmentAsset.project:id,name'])->whereNull('full_name')->get();
				var_dump('có' . count($datas) . ' dữ liệu cần cập nhật');
				foreach ($datas as $item) {
					$fullname = '';
					$fullname = $fullname . 'Căn hộ '. $item->apartment_name . ' tầng '. $item->floor->name . ' khu ' . $item->block->name . ' chung cư ' . $item->apartment->project->name;
					ApartmentAssetProperty::query()->where('id', $item->id)->update(['full_name' => $fullname]);
				}
			} else
				var_dump('Không có dữ liệu cần cập nhật');
			var_dump('Bắt đầu cập nhật cho tài sản chứng thư');
			if (CertificateApartmentProperty::query()->whereNull('full_name')->exists()) {
				$datas = CertificateApartmentProperty::query()->with(['apartmentAsset'])->whereHas('apartmentAsset')->whereNull('full_name')->get();
				var_dump('có ' . count($datas) . ' dữ liệu cần cập nhật');
				foreach ($datas as $item) {
					$apartmentProperty = ApartmentAssetProperty::query()->where('apartment_asset_id', $item->apartment->apartment_asset_id)->first(['apartment_name', 'full_name']);
					if (!empty($apartmentProperty))
						CertificateApartmentProperty::query()->where('id', $item->id)->update(['apartment_name' => $apartmentProperty->apartment_name, 'full_name' => $apartmentProperty->full_name]);
				}
			} else
				var_dump('Không có dữ liệu cần cập nhật');
			var_dump('Cập nhật xong');
        }
		if (!empty($type) && ($type == 25)) {
            echo '<pre>';
			var_dump('Xóa yếu tố khác trùng');
			$groupBy = [
				'appraise_id',
				'position',
				'asset_general_id',
			];
			$select = [
				DB::raw("min(id) as id")
			];
			$datas = AppraiseComparisonFactor::query()->where('status', 1)->where('type', 'yeu_to_khac')->select(array_merge($groupBy,$select))->groupBy($groupBy)->havingRaw("count(*) > 1")->get();
			if (!empty($datas)) {
				foreach ($datas as $item) {
					$where = [
						'appraise_id' => $item->appraise_id,
						'asset_general_id' => $item->asset_general_id,
						'position' => $item->position
					];
					AppraiseComparisonFactor::query()->where($where)->where('id' , '<>', $item->id)->delete();
				}
			}

			$datas = CertificateAssetComparisonFactor::query()->where('status', 1)->where('type', 'yeu_to_khac')->select(array_merge($groupBy,$select))->groupBy($groupBy)->havingRaw("count(*) > 1")->get();
			if (!empty($datas)) {
				foreach ($datas as $item) {
					$where = [
						'appraise_id' => $item->appraise_id,
						'asset_general_id' => $item->asset_general_id,
						'position' => $item->position
					];
					CertificateAssetComparisonFactor::query()->where($where)->where('id' , '<>', $item->id)->delete();
				}
			}
			var_dump('Cập nhật xong');
        }
        if (!empty($type) && ($type == 26)) {
            echo '<pre>';
			var_dump('Change all user to User role');
            $users = User::where('email', '!=', 'admin@fastvalue.vn')->get();
            foreach ($users as $user) {
                $user->assignRole(RoleDefault::USER['role']);
            }
			var_dump('Cập nhật xong');
        }
	}
}
