<?php

namespace App\Console\Commands;

use App\Models\Appraise;
use App\Models\AppraiseAdapter;
use App\Models\AppraiseComparisonFactor;
use App\Models\AppraisePropertyDetail;
use App\Models\Appraiser;
use App\Models\AppraiseTangibleAsset;
use App\Models\Certificate;
use App\Models\CertificateAssetComparisonFactor;
use App\Models\User;
use Artisan;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_production:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update DONAVA database from v1 to v2';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('db:seed', ['--class' => 'UpdateProductionDB']);
        printf('Insert new database v2' . "\n");

        printf('Running fix 15: Update appraise_property_details - main_area' . "\n");
        $this->runFix(15);
        printf('Running fix 15 - done' . "\n");

        printf('Running fix 16: Add name for tangible in certificate asset' . "\n");
        $this->runFix(16);
        printf('Running fix 16 - done' . "\n");

        printf('Running fix 17: Link user and appraiser' . "\n");
        $this->runFix(17);
        printf('Running fix 17 - done' . "\n");

        printf('Running fix 18: Link user and appraiser' . "\n");
        $this->runFix(18);
        printf('Running fix 18 - done' . "\n");

        printf('Running fix 22: Update certification\'s perform user' . "\n");
        $this->runFix(22);
        printf('Running fix 22 - done' . "\n");

        printf('Running fix 23: Update appraise adapter price' . "\n");
        $this->runFix(23);
        printf('Running fix 23 - done' . "\n");

        printf('Running fix 25: delete duplicate comparision factor type yeu_to_khac' . "\n");
        $this->runFix(25);
        printf('Running fix 25 - done' . "\n");

        printf('Command update tangible' . "\n");
        $this->call('migration_update_tangible_asset_id:cron');
        printf('Command update tangible - done ' . "\n");

        printf('Command insert Real Estates');
        $this->call('migration-insert-real-estates:cron');
        printf('Insert data Real Estates - done' . "\n");

        printf('Command insert Certificate Real Estates , update Certificate DocumentType' . "\n");
        $this->call('migration-insert-certificate-real-estates:cron');
        printf('Insert data Certificate Real Estates - done' . "\n");

        printf('Command Import Apartments'."\n");
        $this->call('import_apartment_data:cron');
        printf('Import Apartments - done'."\n");

        printf('Update sale and perform appraiser'."\n");
        $this->updateSaleAndPerformAppraiser();
        printf('Update sale and perform appraiser - Done'."\n");

        printf('Update round and round total for appraises'."\n");
        $this->call('update_appraise_price_by_land_type:cron');
        printf('Update sale and perform appraiser - Done'."\n");

        // printf('Command Update Status'."\n");
        // $this->call('update_certificate_status:cron');
        // printf('Update Status - done'."\n");

        printf('Command Update certificate id for appraise'."\n");
        $this->call('update-certificate-id-to-appraises:cron');
        printf('Update certificate id - done'."\n");
    }
    private function runFix($type)
    {
        if (!empty($type) && ($type == 15)) {

            // update appraise_property_details - main_area

            $items = AppraisePropertyDetail::whereNull('main_area')->get();

            printf(count($items) . ' item cần xử lý' . "\n");

            $this->output->progressStart(count($items));
            foreach ($items as $item) {

                $area = null;
                $planing_area = $item->planning_area ? floatval($item->planning_area) : 0;
                if ($item->total_area) {

                    $area = floatval($item->total_area) - $planing_area;
                }

                if ($item->id && $area) {
                    DB::statement('UPDATE appraise_property_details SET main_area = ' . $area . ' WHERE id = ?', [$item->id]);

                }
                $this->output->progressAdvance();
                usleep(10);
            }
            $this->output->progressFinish();
        }
        if (!empty($type) && ($type == 16)) {
            $datas = AppraiseTangibleAsset::with('buildingType')->whereNull('tangible_name')->get();
            if (isset($datas)) {
                printf(count($datas) . ' item cần xử lý' . "\n");
                $this->output->progressStart(count($datas));
                foreach ($datas as $data) {
                    if (isset($data->buildingType->description)) {
                        AppraiseTangibleAsset::where('id', $data->id)->update([
                            'tangible_name' => $data->buildingType->description
                        ]);
                    }
                    $this->output->progressAdvance();
                    usleep(10);
                }
            }
            $this->output->progressFinish();
            printf('Cập nhật thành công!' . "\n");
        }

        if (!empty($type) && ($type == 17)) {
            $datas = Appraiser::whereNull('user_id')->get();
            if (isset($datas)) {
                foreach ($datas as $data) {
                    Appraiser::where('id', $data->id)->delete();
                    usleep(10);
                }
            }

            printf('Cập nhật thành công!' . "\n");
        }

        if (!empty($type) && ($type == 18)) {
            $datas = Appraiser::with('user')
                ->whereHas('user', function ($has) {
                    $has->whereNotNull('deleted_at');
                })
                ->get();

            if (isset($datas)) {
                foreach ($datas as $data) {
                    Appraiser::where('id', $data->id)->delete();
                    usleep(10);
                }
            }

            printf('Cập nhật thành công!' . "\n");
        }

        if (!empty($type) && ($type == 19)) {
            $datas = Certificate::query()->whereNull('branch_id')->whereNotNull('appraiser_sale_id')->get();
            printf('có ' . count($datas) . ' dữ liệu cần cập nhật');

            if (isset($datas)) {
                foreach ($datas as $data) {
                    $branchId = Appraiser::query()->where('id', $data->appraiser_sale_id)->first()->branch_id;
                    Certificate::query()->where('id', $data->id)->update(['branch_id' => $branchId, 'updated_at' => DB::raw('updated_at')]);
                    printf('done');
                    usleep(10);
                }
                printf('Cập nhật thành công!' . "\n");
            }
        }
        if (!empty($type) && ($type == 22)) {
            try {
                DB::beginTransaction();
                printf('Cập nhật cho TSTĐ đã được chọn vào hoàn thành' . "\n");
                if (Appraise::query()->whereIn('status', [3, 4])->where(function ($q) {
                    $q->whereNull('step')->orWhere('step', 1);
                })->exists()) {
                    Appraise::query()->whereIn('status', [3, 4])->where(function ($q) {
                        $q->whereNull('step')->orWhere('step', 1);
                    })->update(['step' => 7, 'updated_at' => DB::raw('updated_at')]);
                }
                printf('Cập nhật xong' . "\n");
                printf('Cập nhật cho TSTĐ status 1 , 2 và có TSSS' . "\n");
                if (Appraise::query()->whereIn('status', [1, 2])->where(function ($q) {
                    $q->whereNull('step')->orWhere('step', 1);
                })->whereHas('appraiseHasAssets')->exists()) {
                    Appraise::query()->whereIn('status', [1, 2])->where(function ($q) {
                        $q->whereNull('step')->orWhere('step', 1);
                    })->whereHas('appraiseHasAssets')->update(['step' => 6, 'updated_at' => DB::raw('updated_at')]);
                }
                printf('Cập nhật xong' . "\n");
                printf('Cập nhật cho TSTĐ status 1 , 2 và chưa TSSS và có pháp lý' . "\n");
                if (Appraise::query()->whereIn('status', [1, 2])->where(function ($q) {
                    $q->whereNull('step')->orWhere('step', 1);
                })->whereDoesntHave('appraiseHasAssets')->whereHas('appraiseLaw')->exists()) {
                    Appraise::query()->whereIn('status', [1, 2])->where(function ($q) {
                        $q->whereNull('step')->orWhere('step', 1);
                    })->whereDoesntHave('appraiseHasAssets')->whereHas('appraiseLaw')->update(['step' => 5, 'updated_at' => DB::raw('updated_at')]);
                }
                printf('Cập nhật xong' . "\n");
                printf('Cập nhật cho TSTĐ status 1 , 2 và chưa TSSS và chưa có pháp lý' . "\n");
                if (Appraise::query()->whereIn('status', [1, 2])->where(function ($q) {
                    $q->whereNull('step')->orWhere('step', 1);
                })->whereDoesntHave('appraiseHasAssets')->whereDoesntHave('appraiseLaw')->exists()) {
                    Appraise::query()->whereIn('status', [1, 2])->where(function ($q) {
                        $q->whereNull('step')->orWhere('step', 1);
                    })->whereDoesntHave('appraiseHasAssets')->whereDoesntHave('appraiseLaw')->update(['step' => 3, 'updated_at' => DB::raw('updated_at')]);
                }
                printf('Cập nhật xong' . "\n");
                DB::commit();
            } catch (Exception $e) {
                printf($e->getMessage());
                DB::rollBack();
            }
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
    }

    private function updateSaleAndPerformAppraiser()
    {
        try {
            DB::beginTransaction();
            $count = Certificate::query()->whereNull('appraiser_perform_id')->get('id')->count();
            printf("Có $count HSTĐ cần cập nhật \n");
            if ($count > 0) {
                $this->output->progressStart($count);
                $userList = User::query()->whereHas('appraiser')->get();
                foreach ($userList as $user) {
                    $appraiserId = $user->appraiser->id;
                    if (isset($appraiserId)) {
                        if (Certificate::query()->whereNull('appraiser_perform_id')->where('created_by', $user->id)->exists())
                            Certificate::query()->whereNull('appraiser_perform_id')->where('created_by', $user->id)->update([
                                'appraiser_perform_id' => $appraiserId,
                                'updated_at' => DB::raw('updated_at')
                            ]);
                        if (Certificate::query()->whereNull('appraiser_sale_id')->where('created_by', $user->id)->exists())
                            Certificate::query()->whereNull('appraiser_sale_id')->where('created_by', $user->id)->update([
                                'appraiser_sale_id' => $appraiserId,
                                'branch_id' => $user->branch_id,
                                'updated_at' => DB::raw('updated_at')
                            ]);
                    }
                    $this->output->progressAdvance();
                }
            }
            DB::commit();
            $this->output->progressFinish();
        } catch (Exception $e) {
            printf($e->getMessage());
            DB::rollBack();
        }
    }
}
