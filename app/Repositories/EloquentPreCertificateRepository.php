<?php

namespace App\Repositories;

use App;
use App\Contracts\PreCertificateRepository;
use App\Enum\CompareMaterData;
use App\Enum\ErrorMessage;
use App\Models\Certificate;
use App\Models\CertificateOtherDocuments;
use App\Models\PreCertificateExportDocuments;
use App\Models\PreCertificate;
use App\Models\Customer;
use App\Models\Appraise;
use App\Models\PreCertificateOtherDocuments;
use App\Models\PreCertificatePayments;

use App\Models\PreCertificateHasPriceEstimate;
use App\Models\PriceEstimate;
use App\Models\PreCertificatePriceEstimate;
use App\Models\PreCertificatePriceEstimateApartmentProperty;
use App\Models\PreCertificatePriceEstimateFinal;
use App\Models\PreCertificatePriceEstimateApartmentFinal;
use App\Models\PreCertificatePriceEstimateFinalTangibleAsset;
use App\Models\PreCertificatePriceEstimateFinalLand;
use App\Models\PreCertificatePriceEstimateProperty;
use App\Models\PreCertificatePriceEstimatePropertyDetail;
use App\Models\PreCertificatePriceEstimatePropertyTurningTime;
use App\Models\PreCertificatePriceEstimateVersion;
use App\Models\PreCertificatePriceEstimateHasAsset;


use App\Notifications\BroadcastNotification;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Ramsey\Uuid\Uuid;
use Storage;
use Exception;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\EloquentUserRepository;
use App\Models\User;
use App\Enum\ValueDefault;
use App\Models\Appraiser;
use App\Models\CertificateDictionary;
use App\Models\Dictionary;
use App\Notifications\ActivityLog;
use App\Services\CommonService;
use Carbon\Carbon;
use Illuminate\Support\Arr;


use function PHPUnit\Framework\isEmpty;

class  EloquentPreCertificateRepository extends EloquentRepository implements PreCertificateRepository
{

    use ActivityLog;

    private string $defaultSort = 'id';

    private string $allowedSorts = 'id';

    /**
     * @return bool
     */


    /**
     * @return bool
     */
    public function otherDocumentUpload($id, $typeDocument, $request)
    {
        return DB::transaction(function () use ($id, $typeDocument, $request) {
            try {
                $result = [];
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $path = env('STORAGE_OTHERS') . '/' . 'comparison_brief/' . $now->year . '/' . $now->month . '/';

                $files = $request->file('files');

                $user = CommonService::getUser();

                if (isset($files) && !empty($files)) {
                    foreach ($files as $file) {
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getClientOriginalExtension();
                        $fileSize = $file->getSize();
                        $name = $path . Uuid::uuid4()->toString() . '.' . $fileType;
                        Storage::put($name, file_get_contents($file));
                        $fileUrl = Storage::url($name);
                        $item = [
                            'pre_certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => 'appendix',
                            'created_by' => $user->id,
                            'type_document' => $typeDocument
                        ];

                        $item = new PreCertificateOtherDocuments($item);
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                        $result[] = $item;
                    }
                    $edited = PreCertificate::where('id', $id)->first();
                    $edited2 = PreCertificateOtherDocuments::where('pre_certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'tải phụ lục');
                    // chưa lấy ra được model user và id user
                }

                $result = PreCertificateOtherDocuments::where('pre_certificate_id', $id)
                    ->with('createdBy')
                    ->get();
                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return bool
     */
    public function otherDocumentRemove($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $preCertificateId = PreCertificateOtherDocuments::select('pre_certificate_id')->where('id', $id)->get();
                $item = PreCertificateOtherDocuments::where('id', $id)->delete();
                $edited = PreCertificate::where('id', $preCertificateId[0]->pre_certificate_id)->first();
                $edited2 = PreCertificateOtherDocuments::where('id', $id)->get();
                $item = PreCertificateOtherDocuments::where('id', $id)->delete();
                # activity-log delete file
                $this->CreateActivityLog($edited, $edited2, 'delete_file', 'xóa phụ lục');
                // chưa lấy ra được model user và id user
                return $item;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return bool
     */
    public function otherDocumentDownload($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $item = PreCertificateOtherDocuments::where('id', $id)->first();
                return $item;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }


    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query()
            ->select()
            ->orderByDesc($this->defaultSort)
            ->get();
    }

    /**
     * @param $id
     * @return Builder|Model|object
     */
    public function findById($id)
    {
        $version = request()->get('version');
        $result = null;
        if (!$result) {
            $result =  $this->model->query()
                ->where('id', '=', $id)
                ->with('businessManager')
                ->with('appraiserSale')
                ->with('appraiserPerform')
                ->with('createdBy')
                ->with('appraisePurpose')

                ->with('otherDocuments')
                ->with('otherDocuments.createdBy')
                ->first();
        }
        return $result;
    }

    /**
     * @param $id
     * @return Builder|Model|object
     */
    public function findByIdTest($id)
    {
        return;
    }

    /**
     * @param array $objects
     * @return int
     * @throws Throwable
     */
    public function createPreCertificate(array $objects)
    {
        return DB::transaction(function () use ($objects) {
            try {

                $objects["status"] = 1;

                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];

                $objects["updated_at"] = date("Y-m-d H:i:s");
                $preCertificate = new PreCertificate($objects);
                $preCertificateId = QueryBuilder::for(PreCertificate::class)
                    ->insertGetId($preCertificate->attributesToArray());
                return $preCertificateId;
            } catch (Exception $exception) {
                dd($exception);
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     * @throws Throwable
     */
    //public function updatePreCertificate($id, array $objects): int
    public function updatePreCertificate($id, array $objects)
    {
        return DB::transaction(function () use ($id, $objects) {
            try {
                $oldPreCertificate = PreCertificate::where('id', $id)->first();
                $oldAppraises = $oldPreCertificate->appraises;
                $oldCertificateAssetIds = [];
                foreach ($oldAppraises as $oldAppraise) {
                    $oldCertificateAssetIds[$oldAppraise->appraise_id] = $oldAppraise->id;
                }

                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];

                $objects["updated_at"] = date("Y-m-d H:i:s");
                $preCertificate = new PreCertificate($objects);
                $preCertificateId = $id;
                $preCertificate->newQuery()->updateOrInsert(['id' => $id], $preCertificate->attributesToArray());

                return $preCertificateId;
            } catch (Exception $exception) {
                dd($exception);
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @param $id
     * @return void
     */
    public function comparisonFactor($appraiseId, $datas, $edit = false)
    {
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteCertificate($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param $name
     * @return Builder|Model|object|null
     */
    public function findByName($name)
    {
        $query = 'name ilike ' . "'%" . $name . "%'";

        return $this->model->query()
            ->whereRaw($query)
            ->first();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function indexData($data)
    {
        $assetVersion = null;
        if (isset($data->version)) {
            foreach ($data->version as $version) {
                $assetVersion = ($assetVersion == null) ?  $version : ($assetVersion->id > $version->id ? $assetVersion : $version);
            }
        }
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $request = [
            'index' => env('ELASTIC_APPRAISE_VERSION_INDEX'),
            'type' => '_doc',
            'id' => trim($data->id) . '-' . ($assetVersion->version ?? 1),
            'body' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ];
        $client->index($request);
        return $data;
    }

    public function findPaging_v2()
    {
        $user = CommonService::getUser();

        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $sortField = request()->get('sortField');
        $sortOrder = request()->get('sortOrder');
        $filter = request()->get('search');

        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;

        $selectedStatus = null;
        $selectedOfficialTransferStatus = null;
        $timeFilterFrom = null;
        $timeFilterTo = null;
        $dataTemp = null;
        if (request()->has('data')) {
            $dataJson = request()->get('data');
            $dataTemp = json_decode($dataJson);
            $selectedStatus = $dataTemp->status;
            $selectedOfficialTransferStatus = $dataTemp->ots;
            if (isset($dataTemp) && isset($dataTemp->timeFilter)) {
                if (isset($dataTemp->timeFilter->from)) {
                    $timeFilterFrom = $dataTemp->timeFilter->from;
                }
                if (isset($dataTemp->timeFilter->to)) {
                    $timeFilterTo = $dataTemp->timeFilter->to;
                }
            }
        }
        $select = [
            'pre_certificates.id', 'status', 'pre_certificates.created_by', 'petitioner_name',
            'pre_certificates.updated_at', 'status_updated_at',
            'business_manager_id',
            'appraiser_sale_id',
            'appraiser_perform_id',
            'appraise_purpose_id',
            'pre_certificates.created_at',
            'customer_id',
            'total_service_fee',
            'pre_certificates.customer_group_id',

            DB::raw("concat('YCSB_', pre_certificates.id) AS slug"),
            DB::raw("case status
                        when 1
                            then 'Yêu cầu sơ bộ'
                        when 2
                            then 'Định giá sơ bộ'
                        when 3
                            then 'Duyệt giá sơ bộ'
                        when 4
                            then 'Thương thảo'
                        when 5
                            then 'Phát hành KQSB'
                        when 6
                            then 'Hoàn thành'
                        when 7
                            then 'Hủy'
                         when 8
                            then 'Phân hồ sơ'
                    end as status_text
                "),
            'total_preliminary_value',
            // Db::raw("COALESCE(document_count,0) as document_count"),
            'status_expired_at',
        ];
        $with = [
            'createdBy:id,name',
            'priceEstimates',
            'priceEstimates.landFinalEstimate',
            'appraisePurpose:id,name',
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'priceEstimates',
            'priceEstimates.landFinalEstimate',
            'customer:id,name,phone,address',
            'customerGroup:id,description,name_lv_1,name_lv_2,name_lv_3,name_lv_4',
            'payments',
        ];
        DB::enableQueryLog();
        // dd($this->model)->with($with)->select($select);
        $result = QueryBuilder::for($this->model)
            ->with($with)
            ->select($select);
        // dd($result->toSql());
        // dd($result->forPage($page, $perPage)->paginate($perPage));
        //// command tạm - sẽ xử lý phân quyền sau
        $role = $user->roles->last();
        // dd($role->name);
        if (request()->has('is_guest')) {
        } elseif (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'Accounting')) {
            $result = $result->where(function ($query) use ($user) {
                $query = $query->whereHas('createdBy', function ($q) use ($user) {
                    return $q->where('id', $user->id);
                });
                $query = $query->orwhereHas('appraiserSale', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserPerform', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
            });
        }
        // dd($result);
        if (isset($filter) && !empty($filter)) {
            $filterSubstr = substr($filter, 0, 1);
            $filterData = substr($filter, 1);
            switch ($filterSubstr) {
                case '@':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy', function ($has) use ($filterData) {
                            $has->where('name', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                case '$':
                    if (floatval($filterData)) {
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        $result = $result->where(function ($q) use ($fromValue, $toValue) {
                            $q->whereBetween('total_preliminary_value', [$fromValue, $toValue]);
                        });
                    }
                    break;
                case '%':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->where('petitioner_name', 'ILIKE', '%' . $filterData . '%');
                    });
                    break;
                case '^':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('priceEstimates', function ($has) use ($filterData) {
                            $has->where('full_address', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('pre_certificates.id', 'like', strval($filter));
                    });
            }
        }

        // dd($result);

        if (isset($timeFilterFrom) && isset($timeFilterTo)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->whereBetween('pre_certificates.created_at', [$startDate, $endDate])
                ->whereBetween('pre_certificates.updated_at', [$startDate, $endDate]);
        } elseif (isset($timeFilterFrom)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $result = $result->where('pre_certificates.created_at', '>=', $startDate)
                ->where('pre_certificates.updated_at', '>=', $startDate);
        } elseif (isset($timeFilterTo)) {
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->where('pre_certificates.created_at', '<=', $endDate)
                ->where('pre_certificates.updated_at', '<=', $endDate);
        }

        if (isset($selectedStatus) && !empty($selectedStatus)) {
            $result = $result->whereIn('status', $selectedStatus);
        }
        if (isset($selectedOfficialTransferStatus)) {
            if ($selectedOfficialTransferStatus === 1) {
                $result = $result->whereNotNull('certificate_id');
            } elseif ($selectedOfficialTransferStatus === 0) {
                $result = $result->whereNull('certificate_id');
            }
        }
        if (isset($sortField) && !isEmpty($sortField)) {
            if ($sortField == 'petitioner_name')
                if ($sortOrder == 'descend')
                    $result =  $result->orderBy('petitioner_name', 'DESC');
                else
                    $result =  $result->orderBy('petitioner_name', 'ASC');
        }
        if (request()->has('is_guest')) {
            if (isset($user->name_lv_1)) {
                // $result = $result->where('customer_group_id', '=', $user->customer_group_id);
                $result = $result->where(function ($q) use ($user) {
                    $q = $q->whereHas('customerGroup', function ($has) use ($user) {
                        if ($user->name_lv_1 && $user->name_lv_1 != '') {
                            $has->where('name_lv_1', 'ILIKE', '%' . $user->name_lv_1 . '%');
                        }
                        if ($user->name_lv_2 && $user->name_lv_2 != '') {
                            $has->where('name_lv_2', 'ILIKE', '%' . $user->name_lv_2 . '%');
                        }
                        if ($user->name_lv_3 && $user->name_lv_3 != '') {
                            $has->where('name_lv_3', 'ILIKE', '%' . $user->name_lv_3 . '%');
                        }
                        if ($user->name_lv_4 && $user->name_lv_4 != '') {
                            $has->where('name_lv_4', 'ILIKE', '%' . $user->name_lv_4 . '%');
                        }
                    });
                });
                $result = $result->orderByDesc('pre_certificates.updated_at');
                // dd(DB::getQueryLog());
                $result = $result
                    ->forPage($page, $perPage)
                    ->paginate($perPage);
            } else {
                $result = [];
            }
        } else {
            $result = $result->orderByDesc('pre_certificates.updated_at');
            // dd(DB::getQueryLog());
            $result = $result
                ->forPage($page, $perPage)
                ->paginate($perPage);
        }


        // foreach ($result as $stt => $item) {
        //     $result[$stt]->append('detail_list_id');
        //     // $result[$stt]->append('certificate_asset_price');
        // }
        return $result;
    }

    public function getPreCertificateWorkFlow()
    {
        $user = CommonService::getUser();

        $filter = request()->get('search');
        $query = request()->get('query');
        $page = request()->get('page');
        $limit = request()->get('limit');

        $selectedStatus = null;
        $selectedOfficialTransferStatus = null;
        $timeFilterFrom = null;
        $timeFilterTo = null;
        $dataTemp = null;
        if (request()->has('data')) {
            $dataJson = request()->get('data');
            $dataTemp = json_decode($dataJson);
            $selectedStatus = $dataTemp->status;
            $selectedOfficialTransferStatus = $dataTemp->ots;
            if (isset($dataTemp) && isset($dataTemp->timeFilter)) {
                if (isset($dataTemp->timeFilter->from)) {
                    $timeFilterFrom = $dataTemp->timeFilter->from;
                }
                if (isset($dataTemp->timeFilter->to)) {
                    $timeFilterTo = $dataTemp->timeFilter->to;
                }
            }
        }
        if (!empty($query)) {
            $query = json_decode($query);
        } else {
            $query = [];
        }
        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;

        $select = [
            'pre_certificates.id', 'status', 'pre_certificates.created_by', 'petitioner_name',
            'pre_certificates.updated_at', 'status_updated_at',
            'business_manager_id',
            'appraiser_sale_id',
            'appraiser_perform_id',
            'certificate_id',
            'pre_certificates.customer_group_id',
            'customer_id',
            DB::raw("concat('YCSB_', pre_certificates.id) AS slug"),
            DB::raw("case status
                       when 1
                            then 'Yêu cầu sơ bộ'
                        when 2
                            then 'Định giá sơ bộ'
                        when 3
                            then 'Duyệt giá sơ bộ'
                        when 4
                            then 'Thương thảo'
                        when 5
                            then 'Phát hành KQSB'
                        when 6
                            then 'Hoàn thành'
                        when 7
                            then 'Hủy'
                         when 8
                            then 'Phân hồ sơ'
                    end as status_text
                "),
            'total_preliminary_value',
            Db::raw("COALESCE(document_count,0) as document_count"),
            'status_expired_at',
            DB::raw("case status
                        when 1
                            then u1.image
                        when 2
                            then u2.image
                        when 3
                            then u3.image
                        when 4
                            then u1.image
                        when 5
                            then u1.image
                        when 6
                            then u1.image
                        when 7
                            then u3.image
                         when 8
                            then u3.image
                    end as image
                "),
            DB::raw("case status
                when 1
                    then u1.name
                when 2
                    then u2.name
                when 3
                    then u3.name
                when 4
                    then u1.name
                when 5
                    then u1.name
                when 6
                    then u1.name
                when 7
                    then u3.name
                 when 8
                    then u3.name
            end as name_nv
        "),
        ];
        $with = [
            'createdBy:id,name',
            'priceEstimates',
            'priceEstimates.landFinalEstimate',
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'cancelReason:id,description',
            'customerGroup:id,description',
            'otherDocuments' => function ($query) {
                $query->whereNull('deleted_at');
            },
            'preType:id,description',
            'customer:id,name,phone,address',
        ];
        DB::enableQueryLog();
        $result = $this->model->with($with)
            ->leftjoin('users', function ($join) {
                $join->on('pre_certificates.created_by', '=', 'users.id')
                    ->select(['id', 'image'])
                    ->limit(1);
            })
            ->leftjoin(
                DB::raw('(select pre_certificate_id , count(pre_certificate_id) as document_count
                                    from pre_certificate_other_documents
                                    where deleted_at is null
                                    group by pre_certificate_id) as "tbCount"'),
                function ($join) {
                    $join->on('pre_certificates.id', '=', 'tbCount.pre_certificate_id')
                        ->select(['pre_certificate_id', 'document_count']);
                }
            )
            ->leftjoin('appraisers as sale', function ($join) {
                $join->on('sale.id', '=', 'pre_certificates.appraiser_sale_id')
                    ->join('users as u1', function ($j) {
                        $j->on('sale.user_id', '=', 'u1.id');
                    })
                    ->select('u1.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as perform', function ($join) {
                $join->on('perform.id', '=', 'pre_certificates.appraiser_perform_id')
                    ->join('users as u2', function ($j) {
                        $j->on('perform.user_id', '=', 'u2.id');
                    })
                    ->select('u2.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as buinesss', function ($join) {
                $join->on('buinesss.id', '=', 'pre_certificates.business_manager_id')
                    ->join('users as u3', function ($j) {
                        $j->on('buinesss.user_id', '=', 'u3.id');
                    })
                    ->select('u3.image')
                    ->limit(1);
            })
            ->select($select);

        //// command tạm - sẽ xử lý phân quyền sau
        $role = $user->roles->last();
        // $permissionViewAccount = false;
        // if ($user->hasPermissionTo('VIEW_ACCOUNTING')) {
        //     $permissionViewAccount = true;
        // }
        // dd($role->name);
        if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN' && $role->name !== 'Accounting')) {
            $result = $result->where(function ($query) use ($user) {
                $query = $query->whereHas('createdBy', function ($q) use ($user) {
                    return $q->where('id', $user->id);
                });
                $query = $query->orwhereHas('appraiserSale', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserPerform', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
            });
        }


        if (isset($filter) && !empty($filter)) {
            $filterSubstr = substr($filter, 0, 1);
            $filterData = substr($filter, 1);
            switch ($filterSubstr) {
                case '@':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy', function ($has) use ($filterData) {
                            $has->where('name', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                case '$':
                    if (floatval($filterData)) {
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        $result = $result->where(function ($q) use ($fromValue, $toValue) {
                            $q->whereBetween('total_preliminary_value', [$fromValue, $toValue]);
                        });
                    }
                    break;
                case '%':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->where('petitioner_name', 'ILIKE', '%' . $filterData . '%');
                    });
                    break;
                case '^':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('priceEstimates', function ($has) use ($filterData) {
                            $has->where('full_address', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('pre_certificates.id', 'like', strval($filter));
                    });
            }
        }
        if (isset($timeFilterFrom) && isset($timeFilterTo)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->whereBetween('pre_certificates.created_at', [$startDate, $endDate])
                ->whereBetween('pre_certificates.updated_at', [$startDate, $endDate]);
        } elseif (isset($timeFilterFrom)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $result = $result->where('pre_certificates.created_at', '>=', $startDate)
                ->where('pre_certificates.updated_at', '>=', $startDate);
        } elseif (isset($timeFilterTo)) {
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->where('pre_certificates.created_at', '<=', $endDate)
                ->where('pre_certificates.updated_at', '<=', $endDate);
        }

        if (isset($selectedStatus) && !empty($selectedStatus)) {
            $result = $result->whereIn('status', $selectedStatus);
        }
        if (isset($selectedOfficialTransferStatus)) {
            if ($selectedOfficialTransferStatus === 1) {
                $result = $result->whereNotNull('certificate_id');
            } elseif ($selectedOfficialTransferStatus === 0) {
                $result = $result->whereNull('certificate_id');
            }
        }
        $result = $result->orderByDesc('pre_certificates.updated_at');
        $result = $result->get();

        return $result;
    }


    //Step 1 - Post Data
    public function postGeneralInfomation(array $objects, int $id = null)
    {
        DB::beginTransaction();
        try {
            // $check = $this->checkDuplicateData($objects, $id);
            // if (isset($check)) {
            //     return $check;
            // }
            $user = CommonService::getUser();
            $customerId = null;
            if (!empty($objects['customer']['name'])) {
                $customer = Customer::whereName($objects['customer']['name'])
                    ->whereAddress($objects['customer']['address'])
                    ->wherePhone($objects['customer']['phone'])
                    ->first();
                if (isset($customer)) {
                    $customerId = $customer->id;
                } else {
                    $customerId = Customer::insertGetId([
                        'name' => $objects['customer']['name'],
                        'address' => $objects['customer']['address'],
                        'phone' => $objects['customer']['phone'],
                        'status' => ValueDefault::ACTIVE_STATUS,
                        'created_by' => $user->id,
                        'created_date' => date('d/m/Y'),
                    ]);
                }
            }

            $branch_id = null;
            if (isset($objects['appraiser_sale_id'])) {
                $branch_id = Appraiser::query()->where('id', $objects['appraiser_sale_id'])->first()->branch_id;
            } else {
                $branch_id = Appraiser::query()->where('user_id', $user->id)->first()->branch_id;
            }

            $data = $objects;
            // dd('note', $data);
            $data['branch_id'] = $branch_id;
            $data['customer_id'] = $customerId;
            if (isset($id)) {
                $check = $this->beforeSave($id);
                if (isset($check)) {
                    return $check;
                }
                $oldPreCertificate = PreCertificate::where('id', '=', $id)->first();
                if (!isset($oldPreCertificate)) {
                    $data = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS . $id, 'exception' =>  ''];
                    return $data;
                }
                $preCertificateId = $id;
                $status = $oldPreCertificate->status;
                if (!isset($oldPreCertificate['created_by'])) {
                    $data['created_by'] = $user->id;
                } else if (isset($oldPreCertificate['created_by']) && isset($oldPreCertificate['created_by']->id)) {
                    $data['created_by'] = $oldPreCertificate['created_by']->id;
                } else {
                    $data['created_by'] = $oldPreCertificate->getRawOriginal('created_by');
                }

                $assignTo = [];
                if ($oldPreCertificate && $oldPreCertificate->appraiser_sale_id && $oldPreCertificate->appraiser_sale_id != $data['appraiser_sale_id']) {
                    $assignTo[] = 'appraiserSale';
                }
                if ($oldPreCertificate && $oldPreCertificate->appraiser_perform_id && $oldPreCertificate->appraiser_perform_id != $data['appraiser_perform_id']) {
                    $assignTo[] = 'appraiserPerform';
                }
                if ($oldPreCertificate && $oldPreCertificate->business_manager_id && $oldPreCertificate->business_manager_id != $data['business_manager_id']) {
                    $assignTo[] = 'appraiserBusinessManager';
                }

                $certificateArr = new PreCertificate($data);
                PreCertificate::where('id', $preCertificateId)->update($certificateArr->attributesToArray());
                $edited = PreCertificate::where('id', $preCertificateId)->first();
                $changeLog = $edited->fill($data);
                $edited->save();
                $this->CreateActivityLog($edited, $changeLog, 'update_data', 'cập nhật dữ liệu');

                if (!empty($assignTo)) {
                    $this->notifyReAssign($preCertificateId, $status, $assignTo);
                }
            } else {
                $data['created_by'] = $user->id;
                // $curDate = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                $PROCESSING_TIME = CertificateDictionary::where(['type' => 'PROCESSING_TIME', 'acronym' => 'MOI'])->get('description')->first();
                $minutes = intval($PROCESSING_TIME->description);
                $status_expired_at = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->addMinutes($minutes)->format('Y-m-d H:i');
                $data['status_expired_at'] = $status_expired_at;
                $certificateArr = new PreCertificate($data);
                // dd($certificateArr);
                $preCertificateCreate = PreCertificate::query()->create($certificateArr->attributesToArray());
                $preCertificateId = $preCertificateCreate->id;
                // $this->saveMethod($preCertificateId);
                # Activity Log "create if id = null"
                $edited = PreCertificate::where('id', $preCertificateId)->first();
                $this->CreateActivityLog($edited, $edited, 'create', 'tạo mới');

                $assignTo = [];
                if ($edited && $edited->appraiser_sale_id) {
                    $assignTo[] = 'appraiserSale';
                }
                if ($edited && $edited->appraiser_perform_id) {
                    $assignTo[] = 'appraiserPerform';
                }
                if ($edited && $edited->business_manager_id) {
                    $assignTo[] = 'appraiserBusinessManager';
                }

                if (!empty($assignTo)) {
                    $this->notifyReAssign($preCertificateId, $preCertificateCreate->status, $assignTo);
                }
                // $this->updateDocumentDescription($preCertificateId);
            }
            DB::commit();
            return [
                'id' => $preCertificateId,
                'status' => $data['status'],
            ];
        } catch (Exception $ex) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            Log::error($ex);
            return $data;
            DB::rollBack();
        }
    }


    public function getPreCertificate(int $id)
    {
        $result = [];
        // $check = $this->checkAuthorizationPreCertificate($id);
        // if (!empty($check))
        //     return $check;
        $select = [
            'id',
            'certificate_id',
            'petitioner_name',
            'petitioner_phone',
            'petitioner_identity_card',
            'petitioner_address',
            'customer_id',
            'appraise_purpose_id',
            'note',
            'appraiser_sale_id',
            'business_manager_id',
            'appraiser_perform_id',
            'total_preliminary_value',
            'cancel_reason',
            'status_updated_at',
            'pre_certificates.created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'status',
            'deleted_at',
            'status_expired_at',
            'commission_fee',
            'pre_date',
            'pre_asset_name',
            'total_service_fee',
            'pre_type_id',
            'customer_group_id',
        ];
        $with = [
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'appraisePurpose:id,name',
            'customer:id,name,phone,address',
            'otherDocuments' => function ($query) {
                $query->whereNull('deleted_at');
            },
            'createdBy:id,name',
            'payments',
            'cancelReason:id,description',
            'customerGroup:id,description',
            'preType:id,description',
            'exportDocuments',

            'priceEstimates',
            'priceEstimates.createdBy:id,name',
            'priceEstimates.assetType',
            'priceEstimates.lastVersion',
            'priceEstimates.apartmentProperties',
            'priceEstimates.apartmentProperties.floor',
            'priceEstimates.landFinalEstimate',
            'priceEstimates.landFinalEstimate.lands',
            'priceEstimates.landFinalEstimate.tangibleAssets',
            'priceEstimates.landFinalEstimate.apartmentFinals',
            'priceEstimates.properties',
            'priceEstimates.properties.propertyDetail',
            'priceEstimates.properties.propertyTurningTime',
            'priceEstimates.assetGeneralRelation',
        ];
        $result = $this->model->query()
            ->with($with)
            ->where('id', $id)
            ->select($select)
            ->first();
        if ($result['status'] == 1) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_sale_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }
        if ($result['status'] == 2 || $result['status'] == 4) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_perform_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }
        if ($result['status'] == 3 || $result['status'] == 6 || $result['status'] == 5) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['business_manager_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }
        return $result;
    }


    public function updateToOffical($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $preCertificate = $this->getPreCertificate($id);
                if ($preCertificate->certificate_id) {
                    return [
                        'error' => true,
                        'message' => 'Hồ sơ này đã được chuyển chính thức, vui lòng kiểm tra lại'
                    ];
                }
                if ($preCertificate->status != 6) {
                    return [
                        'error' => true,
                        'message' => 'Hồ sơ này không đạt đủ yêu cầu để chuyển chính thức, vui lòng kiểm tra lại'
                    ];
                }
                $preCertificateKey = [
                    'certificate_id',
                    'petitioner_name',
                    'petitioner_phone',
                    'petitioner_identity_card',
                    'petitioner_address',
                    'customer_id',
                    'appraise_purpose_id',
                    'note',
                    'appraiser_sale_id',
                    'business_manager_id',
                    'appraiser_perform_id',
                    'total_preliminary_value',
                    'cancel_reason',
                    'status_updated_at',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by',
                    'status',
                    'deleted_at',
                    'commission_fee',
                    'pre_type_id',
                    'customer_group_id',
                ];
                $certificateKey = [
                    'petitioner_name',
                    'petitioner_phone',
                    'petitioner_identity_card',
                    'petitioner_address',
                    'appraiser_id',
                    'appraiser_confirm_id',
                    'appraiser_manager_id',
                    'appraiser_control_id',
                    'appraise_purpose_id',
                    'document_num',
                    'document_date',
                    'appraise_date',
                    'service_fee',
                    'appraiser_sale_id',
                    'business_manager_id',
                    'appraiser_perform_id',
                    'certificate_date',
                    'certificate_num',
                    'customer_id',
                    'status',
                    'sub_status',
                    'commission_fee',
                    'note',
                    'created_by',
                    'document_type',
                    'total_preliminary_value',
                    'pre_type_id',
                    'customer_group_id',
                ];

                $user = CommonService::getUser();
                $certificate = new Certificate();
                foreach ($preCertificateKey as $key) {
                    if (in_array($key, $certificateKey)) {
                        $certificate->$key = $preCertificate->$key;
                    }
                }
                $certificate->status = 1;
                $certificate->sub_status = 1;
                $certificate->service_fee = $preCertificate->total_service_fee;
                $certificate->created_by = $user->id;
                $certificate->pre_certificate_id = $id;
                $certificate->updated_at = date("Y-m-d H:i:s");
                $certificate->document_description = 'Các hồ sơ, tài liệu về tài sản do khách hàng cung cấp là đầy đủ và tin cậy';

                $PROCESSING_TIME = CertificateDictionary::where(['type' => 'PROCESSING_TIME', 'acronym' => 'MOI'])->get('description')->first();
                $minutes = intval($PROCESSING_TIME->description);
                $status_expired_at = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->addMinutes($minutes)->format('Y-m-d H:i');
                $certificate->status_expired_at = $status_expired_at;

                $certificateId = QueryBuilder::for(Certificate::class)
                    ->insertGetId($certificate->attributesToArray());

                if ($certificateId) {

                    $preCertificateModel = PreCertificate::find($preCertificate->id);
                    if ($preCertificateModel) {
                        $preCertificateModel->certificate_id = $certificateId;
                        $preCertificateModel->save();
                    }
                    $preCertificatePayments = PreCertificatePayments::where('pre_certificate_id', $preCertificate->id)->get();

                    foreach ($preCertificatePayments as $payment) {
                        $payment->certificate_id = $certificateId;
                        $payment->save();
                    }

                    $documents = PreCertificateOtherDocuments::where('pre_certificate_id', $preCertificate->id)
                        ->whereNull('deleted_at')
                        ->get();
                    if ($documents->count() > 0) {
                        foreach ($documents as $document) {
                            if ($document->type_document == 'Appendix') {
                                $item = [
                                    'certificate_id' => $certificateId,
                                    'name' => $document->name,
                                    'link' => $document->link,
                                    'type' => $document->type,
                                    'size' => $document->size,
                                    'description' => 'appendix',
                                    'created_by' => $user->id,
                                ];
                                $item = new CertificateOtherDocuments($item);
                                QueryBuilder::for($item)->insert($item->attributesToArray());
                            }
                        }
                    }
                    $documentsExports = PreCertificateExportDocuments::where('pre_certificate_id', $preCertificate->id)
                        ->whereNull('deleted_at')
                        ->get();
                    if ($documentsExports->count() > 0) {
                        foreach ($documentsExports as $documentsExport) {
                            $item = [
                                'certificate_id' => $certificateId,
                                'name' => $documentsExport->name,
                                'link' => $documentsExport->link,
                                'type' => $documentsExport->type,
                                'size' => $documentsExport->size,
                                'description' => $documentsExport->description,
                                'type_document' => $documentsExport->type_document,
                                'created_by' => $user->id,
                            ];
                            $item = new PreCertificateExportDocuments($item);
                            QueryBuilder::for($item)->insert($item->attributesToArray());
                        }
                    }

                    $edited = Certificate::where('id', $certificateId)->first();
                    $this->CreateActivityLog($edited, $edited, 'create', 'Chuyển chính thức từ YCSB_' . $id);
                }
                // $logDescription = 'chuyển chính thức ' . $preCertificate->id;
                // $this->CreateActivityLog($certificate, $certificate, 'chuyen_chinh_thuc', $logDescription, $request['note']);
                return [
                    'error' => false,
                    'data' => $certificateId,
                ];
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }
    public function updateStatus_v2($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $result = [];
                $preCertificate = $this->model->query()->where('id', $id)->first();
                if ($preCertificate->status ==  $request['status']) {
                    $check = $this->beforeUpdateStatusRedistribute($id);
                    if (isset($check)) {
                        return $check;
                    }
                } else {
                    // # đang tắt khối block xác thực
                    $check = $this->beforeUpdateStatus($id);
                    if (isset($check)) {
                        return $check;
                    }
                }



                if (isset($preCertificate->certificate_id)) {
                    return  ['message' => ErrorMessage::PRE_CERTIFICATE_HAVE_CERTIFICATE, 'exception' => ''];
                }
                $currentStatus = $preCertificate->status;
                $current = intval($currentStatus);
                $currentConfig = current(array_filter($request['status_config'], function ($val) use ($currentStatus) {
                    return $val['status'] == $currentStatus;
                }));
                $status = $request['status'];
                $next = intval($status);
                $nextConfig = current(array_filter($request['status_config'], function ($val) use ($status) {
                    return $val['status'] == $status;
                }));
                $status_expired_at = isset($request['status_expired_at']) ? \Carbon\Carbon::createFromFormat('d-m-Y H:i', $request['status_expired_at'])->format('Y-m-d H:i') : null;
                $total_preliminary_value = isset($request['total_preliminary_value']) ? $request['total_preliminary_value'] : null;
                $cancel_reason = isset($request['cancel_reason']) ? $request['cancel_reason'] : null;

                if (isset($status)) {
                    $updateArray = [
                        'status' => $status,
                        'status_updated_at' => date('Y-m-d H:i:s'),
                        'status_expired_at' => $status_expired_at,
                    ];

                    $assignTo = [];
                    if (isset($cancel_reason)) {
                        $updateArray['cancel_reason'] = $cancel_reason;
                    } else if (isset($total_preliminary_value)) {
                        $updateArray['total_preliminary_value'] = $total_preliminary_value;
                    } else if ($status == 1 && $preCertificate->cancel_reason != null) {
                        $updateArray['cancel_reason'] = null;
                    }
                    if (isset($request['appraiser_sale_id'])) {
                        $updateArray['appraiser_sale_id'] = $request['appraiser_sale_id'];
                        $assignTo[] = 'appraiserSale';
                    }
                    if (isset($request['appraiser_perform_id'])) {
                        $updateArray['appraiser_perform_id'] = $request['appraiser_perform_id'];
                        $assignTo[] = 'appraiserPerform';
                    }
                    if (isset($request['business_manager_id'])) {
                        $updateArray['business_manager_id'] = $request['business_manager_id'];
                        $assignTo[] = 'appraiserBusinessManager';
                    }
                    $result = $this->model->query()
                        ->where('id', '=', $id)
                        ->update($updateArray);
                    if (($current == 2 && $next == 8) || $next == 7) {
                        //xóa tài sản sơ bộ khỏi HSSB nếu từ định giá sơ bộ về phân hồ sơ, hoặc hủy
                        $this->deletePriceEstimateWithRelations(
                            $id
                        );
                    }

                    # Chuyển status từ số sang text
                    $edited = PreCertificate::where('id', $id)->first();
                    if ($next != 8  && $current > $next) {
                        // $logDescription = $request['status_description'] . ' '.  $request['status_config']['description'];
                        $description = $currentConfig !== false ? $currentConfig['description'] : '';
                        $logDescription = 'từ chối ' .  $description;
                        if ($logDescription == "từ chối Hủy") {
                            $logDescription = "Khôi phục YCSB";
                        }
                        if ($logDescription == "từ chối Phân hồ sơ") {
                            $description = $nextConfig !== false ? $nextConfig['description'] : '';
                            $logDescription = 'cập nhật trạng thái ' . $description;
                        }
                    } elseif ($current == $next) {
                        $logDescription = 'phân lại hồ sơ ';
                    } else {
                        $description = $nextConfig !== false ? $nextConfig['description'] : '';
                        $logDescription = 'cập nhật trạng thái ' . $description;
                    }
                    $logName = 'update_status';
                    // activity-log Update status
                    $note = $request['status_note'] ?? '';
                    $reason_id = $request['status_reason_id'] ?? null;
                    $this->CreateActivityLog($edited, $edited, $logName, $logDescription, $note, $reason_id);
                }
                // $result = $this->getAppraisalTeam($id);
                $result = $this->getPreCertificate($id);
                if (isset($status)) {
                    $this->notifyChangeStatus($id, $status, $result);
                }
                if (!empty($assignTo)) {
                    $this->notifyReAssign($id, $status, $assignTo, $result);
                }
                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    public function updatePayments($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $user = CommonService::getUser();
                foreach ($request as $item) {
                    try {
                        if (isset($item['id']) && isset($item['is_deleted'])) {
                            $payment = PreCertificatePayments::find($item['id']);
                            if ($payment) {
                                $payment->delete();
                            } else {
                                return [
                                    'error' => true,
                                    'message' => "Không thể xóa được, vui lòng kiểm tra lại"
                                ];
                            }
                        } else if (isset($item['id'])) {
                            $item['updated_by'] = $user->id;
                            PreCertificatePayments::where('id', $item['id'])->update($item);
                        } else {
                            $item['created_by'] = $user->id;
                            PreCertificatePayments::create($item);
                        }
                    } catch (\Exception $e) {
                        $message = 'Thêm mới thất bại';
                        if (isset($item['id'])) {
                            $message = 'Cập nhật thất bại';
                        }
                        return [
                            'error' => true,
                            'message' => $message . $e->getMessage()
                        ];
                    }
                }
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    private function beforeSave(int $id)
    {
        $result = null;

        if (PreCertificate::where('id', $id)->exists()) {
            $user = CommonService::getUser();
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN'])) {
                $data = PreCertificate::where('id', $id)->get()->first();
                switch ($data['status']) {
                    case 1:
                        if (!($data->appraiserSale->user_id == $user->id)) //&& !($data->appraiserBusinessManager->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có nhân viên kinh doanh mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thực hiện mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    case 8:
                        if (!($data->appraiserBusinessManager->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có quản lý nghiệp vụ mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
                        break;
                }
            }
        } else {
            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

    public function getProcessingTime()
    {
        $result = [];
        $select = [
            'id',
            'type',
            'description',
            'acronym',
            // 'status',
            DB::raw('cast(description as integer) as value'),
            // DB::raw('cast(description as integer)/60 as hour'),
            // DB::raw('cast(description as integer)%60 as minute'),
        ];
        $where = [
            'type' => 'PROCESSING_TIME',
            'status' => 1,
        ];
        $result = CertificateDictionary::where($where)->select($select)->get();
        return $result;
    }

    private function beforeUpdateStatus(int $id)
    {
        $result = null;

        if (PreCertificate::where('id', $id)->exists()) {
            $required = request()->get('required');
            $user = CommonService::getUser();
            $data = PreCertificate::where('id', $id)->get()->first();
            $appraiser = [];
            // if (!empty($required)) {
            //     $isCheckAppraiser =  $required['appraiser'];
            //     // $isCheckTotalPreliminaryValue =  $required['total_preliminary_value'];


            //     if ($isCheckAppraiser) {
            //         $appraiser['appraiser_sale_id'] =  request()->get('appraiser_sale_id');
            //         $appraiser['business_manager_id'] =  request()->get('business_manager_id');
            //         $appraiser['appraiser_perform_id'] =  request()->get('appraiser_perform_id');
            //         if (empty($appraiser['appraiser_sale_id']) || empty($appraiser['business_manager_id']) || empty($appraiser['appraiser_perform_id'])) {
            //             return ['message' => ErrorMessage::CERTIFICATE_APPRAISERTEAM, 'exception' => ''];
            //         }
            //     }
            // }
            //Check role and permision
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN', 'ADMIN'])) {
                switch ($data['status']) {
                    case 1:
                        if (!($data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có nhân viên kinh doanh mới có quyền này.', 'exception' => ''];
                        break;
                    case 4:
                    case 5:
                        if (!($data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có nhân viên kinh doanh mới có quyền này.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền này.', 'exception' => ''];
                        break;
                    case 3:
                    case 6:
                        if (!($data->appraiserBusinessManager && $data->appraiserBusinessManager->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có quản lý nghiệp vụ mới có quyền này.', 'exception' => ''];
                        break;
                    case 7:
                    case 8:
                        if (!($data->appraiserBusinessManager && $data->appraiserBusinessManager->user_id == $user->id))
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có quản lý nghiệp vụ mới có quyền này.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
                        break;
                }
            }
        } else {
            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

    private function beforeUpdateStatusRedistribute(int $id)
    {
        $result = null;

        if (PreCertificate::where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $data = PreCertificate::where('id', $id)->get()->first();
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN', 'ADMIN'])) {
                if (!($data->appraiserBusinessManager->user_id == $user->id)) {
                    $result = ['message' => 'Chỉ có quản lý nghiệp vụ mới có quyền phân lại hồ sơ này.', 'exception' => ''];
                }
            }
        } else {
            $result = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }


    private function notifyChangeStatus(int $id, int $status, $preCertificate = null)
    {
        $loginUser = CommonService::getUser();
        $users[] = $loginUser;
        if (!$preCertificate) {
            $with = [
                'appraiserSale:id,user_id,name',
                'appraiserPerform:id,user_id,name',
                'appraiserBusinessManager:id,user_id,name',
                'createdBy:id,name',
            ];
            $select = [
                'id',
                'created_by',
                'appraiser_sale_id',
                'appraiser_perform_id',
                'business_manager_id',
            ];
            $preCertificate = PreCertificate::with($with)->where('id', $id)->get($select)->first();
        }
        $eloquenUser = new EloquentUserRepository(new User());

        if (isset($preCertificate->appraiserSale->user_id))
            if ($preCertificate->appraiserSale->user_id != $loginUser->id) {
                $users[] =  $eloquenUser->getUser($preCertificate->appraiserSale->user_id);
            }
        if (isset($preCertificate->appraiserPerform->user_id))
            if ($preCertificate->appraiserPerform->user_id != $loginUser->id) {
                $users[] =  $eloquenUser->getUser($preCertificate->appraiserPerform->user_id);
            }
        if (isset($preCertificate->appraiserBusinessManager->user_id))
            if ($preCertificate->appraiserBusinessManager->user_id != $loginUser->id) {
                $users[] =  $eloquenUser->getUser($preCertificate->appraiserBusinessManager->user_id);
            }
        switch ($status) {
            case 2:
                $statusText = 'Định giá sơ bộ';
                break;
            case 3:
                $statusText = 'Duyệt giá sơ bộ';
                break;
            case 4:
                $statusText = 'Thương thảo';
                break;
            case 5:
                $statusText = 'Phát hành KQSB';
                break;
            case 6:
                $statusText = 'Hoàn thành';
                break;
            case 7:
                $statusText = 'Đã hủy';
                break;
            case 8:
                $statusText = 'Phân hồ sơ';
                break;
            default:
                $statusText = 'Yêu cầu sơ bộ';
        }

        $data = [
            'subject' => '[YCSB_' . $id . '] Chuyển sang trạng thái ' . $statusText,
            'message' => 'YCSB_' . $id . ' đã được ' . $loginUser->name . ' chuyển sang trạng thái ' . $statusText . '.',
            'user' => $loginUser,
            'id' => $id
        ];
        $users = array_map(function ($user) {
            return serialize($user);
        }, $users);

        $users = array_unique($users);

        $users = array_map(function ($user) {
            return unserialize($user);
        }, $users);

        CommonService::callNotification($users, $data);
    }
    private function notifyReAssign(int $id, int $status, $assignTo, $preCertificate = null)
    { // Allow the script to execute in the background
        $loginUser = CommonService::getUser();
        switch ($status) {
            case 2:
                $statusText = 'Định giá sơ bộ';
                break;
            case 3:
                $statusText = 'Duyệt giá sơ bộ';
                break;
            case 4:
                $statusText = 'Thương thảo';
                break;
            case 5:
                $statusText = 'Phát hành KQSB';
                break;
            case 6:
                $statusText = 'Hoàn thành';
                break;
            case 7:
                $statusText = 'Đã hủy';
                break;
            case 8:
                $statusText = 'Phân hồ sơ';
                break;
            default:
                $statusText = 'Yêu cầu sơ bộ';
        }
        if (!$preCertificate) {
            $with = [
                'appraiserSale:id,user_id,name',
                'appraiserPerform:id,user_id,name',
                'appraiserBusinessManager:id,user_id,name',
                'createdBy:id,name',
            ];
            $select = [
                'id',
                'created_by',
                'status',
                'appraiser_sale_id',
                'appraiser_perform_id',
                'business_manager_id',
            ];

            $preCertificate = PreCertificate::with($with)->where('id', $id)->get($select)->first();
        }
        $eloquenUser = new EloquentUserRepository(new User());
        $processedUserIds = []; // Array to store processed user_ids
        foreach ($assignTo as $assign) {
            $user = null;
            if (isset($preCertificate[$assign]) && isset($preCertificate[$assign]->user_id)) {
                if ($preCertificate[$assign]->user_id != $loginUser->id && !in_array($preCertificate[$assign]->user_id, $processedUserIds)) {
                    $user = $eloquenUser->getUser($preCertificate[$assign]->user_id);
                    $processedUserIds[] = $preCertificate[$assign]->user_id; // Add user_id to processedUserIds array

                    switch ($assign) {
                        case 'appraiserSale':
                            $typeAssign = 'Nhân viên kinh doanh';
                            break;
                        case 'appraiserPerform':
                            $typeAssign = 'Chuyên viên thẩm định';
                            break;
                        case 'appraiserBusinessManager':
                            $typeAssign = 'Quản lý nghiệp vụ';
                            break;
                    }

                    $data = [
                        'subject' => '[YCSB_' . $id . '] trạng thái ' . $statusText,
                        'message' => $preCertificate[$assign]->name . ' bạn được ' . $loginUser->name . ' phân công làm ' . $typeAssign . 'cho YCSB_' . $id . '.',
                        'user' => $loginUser,
                        'id' => $id
                    ];
                    CommonService::callNotification([$user], $data);
                }
            }
        }
    }
    private function checkDuplicateData(array $object, int $preCertificateId = null)
    {
        $result = null;
        // PreCertificate brief id must be checked with year.
        // It might raise bug if created success with same number but after that update same year.
        // Temp return fale.
        return $result;
        $paramList = CompareMaterData::CERTIFICATE_BRIEF_CHECK_DUPLICATE;
        if ($paramList != null && count($paramList) > 0) {
            $paramKeys = array_keys($paramList);
            foreach ($paramKeys as $key) {
                if (isset($object[$key])) {
                    if (PreCertificate::where($key, $object[$key])->where('id', '<>', $preCertificateId ?? -1)->exists()) {
                        $result = ['message' => $paramList[$key] . ' đã tồn tại. Vui lòng nhập ' . $paramList[$key] . ' khác', 'exception' => ''];
                    }
                }
            }
        }
        return $result;
    }
    #endregion

    public function exportPreCertificate()
    {
        $status = request()->get('status');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $users = request()->get('created_by');
        $businessManager = request()->get('business_manager_id');
        $appraiserSale = request()->get('appraiser_sale_id');
        $appraiserPerform = request()->get('appraiser_perform_id');
        $customer = request()->get('customer_id');
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
            $diff = $toDate->diff($fromDate);
            if ($diff->days > 186) {
                return ['message' => 'Chỉ được tìm kiếm tối đa 6 tháng.', 'exception' => ''];
            }
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        if (!empty($status)) {
            $status = explode(',', $status);
        }

        if (!empty($users)) {
            $users = explode(',', $users);
        }
        $select = [
            'id',
            'certificate_id',
            'petitioner_name',
            'petitioner_phone',
            'petitioner_identity_card',
            'petitioner_address',
            'customer_id',
            'appraise_purpose_id',
            'note',
            'appraiser_sale_id',
            'business_manager_id',
            'appraiser_perform_id',
            'total_preliminary_value',
            'cancel_reason',
            'status_updated_at',
            'pre_certificates.created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'status',
            'deleted_at',
            'status_expired_at',
            'commission_fee',
            'pre_date',
            'pre_asset_name',
            'total_service_fee',
            'pre_type_id',
            'customer_group_id',
            DB::raw(" CASE status
                WHEN 1 THEN 'Yêu cầu sơ bộ'
                WHEN 2 THEN 'Định giá sơ bộ'
                WHEN 3 THEN 'Duyệt giá sơ bộ'
                WHEN 4 THEN 'Thương thảo'
                WHEN 5 THEN 'Phát hành KQSB'
                WHEN 6 THEN 'Hoàn thành'
                WHEN 7 THEN 'Hủy'
                WHEN 8 THEN 'Phân hồ sơ'
            END AS status_text
                "),
        ];
        $with = [
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'appraisePurpose:id,name',
            'customer:id,name,phone,address',
            'createdBy:id,name',
            'payments',
            'cancelReason:id,description',
            'preType:id,description',
            'customerGroup:id,description',
            'priceEstimates',
            'priceEstimates.landFinalEstimate'
        ];
        $result = PreCertificate::with($with)
            ->select($select);

        if (isset($status)) {
            $result = $result->whereIn('status', $status);
        }
        if (isset($users)) {
            $result = $result->whereIn('created_by', $users);
        }
        if (isset($fromDate) && isset($toDate)) {
            // $result=$result->whereRaw("to_char('created_at' ,'YYYY-MM-dd') between '". $fromDate->format('Y-m-d') ."' and '". $toDate->format('Y-m-d')."'");
            $result = $result->whereRaw("to_char(pre_certificates.created_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'");
        }
        if (isset($businessManager)) {
            $result = $result->where('business_manager_id', $businessManager);
        }
        if (isset($appraiserSale)) {
            $result = $result->where('appraiser_sale_id', $appraiserSale);
        }
        if (isset($appraiserPerform)) {
            $result = $result->where('appraiser_perform_id', $appraiserPerform);
        }
        if (isset($customer)) {
            $result = $result->where('customer_id', $customer);
        }
        $result = $result->get();
        return $result;
    }

    public function exportPayment()
    {
        $status = request()->get('status');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
            $diff = $toDate->diff($fromDate);
            if ($diff->days > 186) {
                return ['message' => 'Chỉ được tìm kiếm tối đa 6 tháng.', 'exception' => ''];
            }
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        if (!empty($status)) {
            $status = explode(',', $status);
        }

        $select = ['*'];
        $with = [
            'preCertificate',
            'preCertificate.appraiserSale',
            'preCertificate.appraiserPerform',
            'preCertificate.payments',  'certificate', 'certificate.payments'
        ];
        $result = PreCertificatePayments::with($with)->select($select);
        $result = $result->whereNotNull('pre_certificate_id');

        if (isset($status)) {
            $result = $result->whereHas('preCertificate', function ($query) use ($status) {
                $query->whereIn('status', $status);
            });
        }

        if (isset($fromDate) && isset($toDate)) {
            $result = $result->whereBetween('pay_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
        }

        $result = $result->orderBy('pay_date', 'desc')->get();
        return $result;
    }

    private function checkAuthorizationPreCertificate($id)
    {
        $check = null;
        if ($this->model->query()->where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $role = $user->roles->last();
            $result = $this->model->query()->where('id', $id);
            $userId = $user->id;
            if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN' && $role->name !== 'Accounting')) {
                $result = $result->where(function ($query) use ($userId) {
                    $query = $query->whereHas('createdBy', function ($q) use ($userId) {
                        return $q->where('id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserSale', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserPerform', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                });
            }
            $result = $result->first();
            if (empty($result))
                $check = ['message' => 'Bạn không có quyền ở YCSB ' . $id, 'exception' => '', 'statusCode' => 403];
        } else {
            $check = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        }
        return $check;
    }

    /**
     * @return bool
     */
    public function exportDocumentUpload($id, $is_pc, $request)
    {
        return DB::transaction(function () use ($id, $is_pc, $request) {
            try {
                $result = [];
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $path = env('STORAGE_OTHERS') . '/' . 'export_document/' . $now->year . '/' . $now->month . '/';

                $files = $request->file('files');
                $typeDocument = $request->input('type') ?? '';

                if (!$typeDocument) {
                    return ['message' => 'Vui lòng nhập tên tài liệu', 'exception' => ''];
                }
                $user = CommonService::getUser();

                if (isset($files) && !empty($files)) {
                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }
                    foreach ($files as $file) {
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getClientOriginalExtension();
                        $fileSize = $file->getSize();
                        $name = $path . Uuid::uuid4()->toString() . '.' . $fileType;
                        Storage::put($name, file_get_contents($file));
                        $fileUrl = Storage::url($name);
                        $item = [
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => '',
                            'created_by' => $user->id,
                            'type_document' => $typeDocument
                        ];



                        if ($is_pc) {
                            $item['pre_certificate_id'] = $id;
                            $oldItem = PreCertificateExportDocuments::firstWhere([
                                'pre_certificate_id' => $id,
                                'type_document' => $typeDocument,
                            ]);
                        } else {
                            $item['certificate_id'] = $id;
                            $oldItem = PreCertificateExportDocuments::firstWhere([
                                'certificate_id' => $id,
                                'type_document' => $typeDocument,
                            ]);
                        }
                        if ($oldItem) {
                            $oldItem->delete();
                        }
                        $item = new PreCertificateExportDocuments($item);
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                        $result[] = $item;
                    }
                    if ($is_pc) {
                        $edited = PreCertificate::where('id', $id)->first();
                        $edited2 = PreCertificateExportDocuments::where('pre_certificate_id', $id)->first();
                    } else {
                        $edited = Certificate::where('id', $id)->first();
                        $edited2 = PreCertificateExportDocuments::where('certificate_id', $id)->first();
                    }
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'Tải tài liệu sơ bộ ' . $name);
                    // chưa lấy ra được model user và id user
                }

                if ($is_pc) {
                    $result = PreCertificateExportDocuments::where('pre_certificate_id', $id)
                        ->with('createdBy')
                        ->get();
                } else {
                    $result = PreCertificateExportDocuments::where('certificate_id', $id)
                        ->with('createdBy')
                        ->get();
                }
                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return bool
     */
    public function exportDocumentRemovePC($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $delete_what
                    = $request->input('delete_what') ?? '';
                $preCertificateId = PreCertificateExportDocuments::select('pre_certificate_id')->where('id', $id)->get();
                $item = PreCertificateExportDocuments::where('id', $id)->delete();
                $edited = PreCertificate::where('id', $preCertificateId[0]->pre_certificate_id)->first();
                $edited2 = PreCertificateExportDocuments::where('id', $id)->get();
                # activity-log delete file
                $this->CreateActivityLog(
                    $edited,
                    $edited2,
                    'delete_file',
                    'xóa tài liệu sơ bộ ' .
                        $delete_what . ' được tải lên'
                );
                // chưa lấy ra được model user và id user
                return $item;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return bool
     */
    public function exportDocumentRemoveCertificate($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $delete_what = $request->input('delete_what') ?? '';
                $certificateId = PreCertificateExportDocuments::select('certificate_id')->where('id', $id)->get();
                $item = PreCertificateExportDocuments::where('id', $id)->delete();
                $edited = Certificate::where('id', $certificateId[0]->certificate_id)->first();
                $edited2 = PreCertificateExportDocuments::where('id', $id)->get();
                # activity-log delete file
                $this->CreateActivityLog($edited, $edited2, 'delete_file', 'Xóa tài liệu sơ bộ ' . $delete_what . ' được tải lên');
                // chưa lấy ra được model user và id user
                return $item;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }
    /**
     * @return bool
     */
    public function exportDocumentDownload($id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $item = PreCertificateExportDocuments::where('id', $id)->first();
                return $item;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    private function checkAuthorization($id)
    {
        $check = null;
        if (PriceEstimate::query()->where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $role = $user->roles->last();
            $result = PriceEstimate::query()->where('id', $id);
            $userId = $user->id;
            // if (($role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN')) {
            //     $result = $result->where('created_by', $userId);
            // }
            $result = $result->first();
            if (empty($result))
                $check = ['message' => 'Bạn không có quyền ở TSSB ' . $id, 'exception' => '', 'statusCode' => 403];
        } else {
            $check = ['message' => ErrorMessage::PE_CHECK_EXIT . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        }
        return $check;
    }

    public function getPriceEstimateDataFullConnectPreCertificate($priceEstimateId)
    {
        // $check = $this->checkAuthorization($priceEstimateId);
        // if (!empty($check))
        //     return $check;
        $select = ['*'];
        $with = [
            'createdBy:id,name',
            'lastVersion',
            'apartmentProperties',
            'apartmentProperties.floor',
            'landFinalEstimate',
            'landFinalEstimate.lands',
            'landFinalEstimate.tangibleAssets',
            'landFinalEstimate.apartmentFinals',
            'properties',
            'properties.propertyDetail',
            'properties.propertyTurningTime',
            'assetGeneralRelation',
        ];
        $result = PriceEstimate::with($with)
            ->select($select)
            ->where('id', $priceEstimateId);

        $result = $result->first();

        return $result;
    }

    public function updatePreCertificateV3(array $objects, int $preCertificateId)
    {
        $result =  [];

        DB::beginTransaction();
        try {
            // # khóa khối block xác thực
            $check = $this->beforeSave($preCertificateId);
            if (isset($check)) {
                return $check;
            }
            if (PreCertificate::where('id', $preCertificateId)->where('status', 2)->exists()) {
                $priceEstimates = isset($objects['price_estimates']) ? $objects['price_estimates'] : [];

                if (count($priceEstimates) == 0) {
                    $this->deletePriceEstimateWithRelations($preCertificateId);
                } else {
                    $this->deletePriceEstimateWithRelations($preCertificateId);
                    foreach ($priceEstimates as $priceEstimateId) {
                        // Fetch data using the getPriceEstimateDataFull method
                        $priceEstimate = $this->getPriceEstimateDataFullConnectPreCertificate($priceEstimateId);
                        // Check if $priceEstimate is not empty
                        if (!empty($priceEstimate)) {
                            // Check if general_asset exists in the result
                            // if (isset($priceEstimate['assetGeneralRelation'])) {
                            //     // Loop over each asset in general_asset
                            //     $generalAsset = $priceEstimate['assetGeneralRelation'];
                            //     if (count($generalAsset) > 0) {
                            //         if (isset($priceEstimate['project_id'])) {
                            //             $this->updateDetailPriceEstimateApartment($preCertificateId, $priceEstimateId, $priceEstimate);
                            //         } else {
                            //             $this->updateDetailPriceEstimateAppraise($preCertificateId, $priceEstimateId, $priceEstimate);
                            //         }
                            //     } else {
                            //         $result = ['message' => ErrorMessage::PRICE_ESTIMATE_CHECK_ASSET, 'exception' => ''];
                            //         return $result;
                            //     }
                            // }
                            if (isset($priceEstimate['project_id'])) {
                                $this->updateDetailPriceEstimateApartment($preCertificateId, $priceEstimateId, $priceEstimate);
                            } else {
                                $this->updateDetailPriceEstimateAppraise($preCertificateId, $priceEstimateId, $priceEstimate);
                            }
                        } else {
                            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS, 'exception' => ''];
                            return $result;
                        }
                    }
                }


                $edited = PreCertificate::where('id', $preCertificateId)->first();
                // activity-log cập nhật thông tin chi tiết
                $this->CreateActivityLog($edited, $edited, 'update_data', 'cập nhật thông tin kết quả thẩm định');
            } else {
                $result = ['message' => ErrorMessage::PRE_CERTIFICATE_CHOOSE_PRICE_ESTIMATE, 'exception' => ''];
                return $result;
            }
            DB::commit();
            $result = $this->getPreCertificate($preCertificateId);
            // $result = $this->updateDocumentType($preCertificateId);
        } catch (exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $result = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $ex->getMessage()];
        }
        return $result;
    }

    private function updateDetailPriceEstimateApartment($preCertificateId, $priceEstimateId, $priceEstimate)
    {

        // $this->deleteApartmentWithRelations($preCertificateId, $priceEstimateId);
        $user = CommonService::getUser();
        $preCertificatePriceEstimate = new PreCertificatePriceEstimate($priceEstimate->toArray());
        $preCertificatePriceEstimate->price_estimate_id = $priceEstimateId;
        $preCertificatePriceEstimate->pre_certificate_id = $preCertificateId;
        $preCertificatePriceEstimate->created_by = $user->id;
        $preCertificatePriceEstimate->save();
        $preCertificatePriceEstimateId = $preCertificatePriceEstimate->id;
        $this->insertApartmentData($preCertificatePriceEstimateId, $priceEstimate);
        $this->updatePriceEstimatePreCertificateId($priceEstimateId, $preCertificateId);
    }
    private function updatePriceEstimatePreCertificateId($priceEstimateId, $preCertificateId = null)
    {
        $preCertificate = PreCertificate::find($preCertificateId);
        $status = $preCertificate->status;
        $dataUpdate = [
            'pre_certificate_id' => $preCertificateId,
            'status' => $status,
        ];

        PriceEstimate::query()->where('id', $priceEstimateId)->update($dataUpdate);
    }
    private function updateDetailPriceEstimateAppraise($preCertificateId,  $priceEstimateId, $priceEstimate)
    {
        // $this->deleteAppraiseWithRelations($preCertificateId, $priceEstimateId);
        $user = CommonService::getUser();
        $preCertificatePriceEstimate = new PreCertificatePriceEstimate($priceEstimate->toArray());
        $preCertificatePriceEstimate->price_estimate_id = $priceEstimateId;
        $preCertificatePriceEstimate->pre_certificate_id = $preCertificateId;
        $preCertificatePriceEstimate->created_by = $user->id;
        $preCertificatePriceEstimate->save();
        $preCertificatePriceEstimateId = $preCertificatePriceEstimate->id;
        $this->insertAppraiseData($preCertificatePriceEstimateId, $priceEstimate);
        // $appraiseRepo->updatePriceEstimateStatus($priceEstimateId, 3);
        $this->updatePriceEstimatePreCertificateId($priceEstimateId, $preCertificateId);
    }



    private function insertApartmentData(int $preCertificatePriceEstimateId, $priceEstimate)
    {
        if (isset($priceEstimate['apartmentProperties'])) {
            foreach ($priceEstimate['apartmentProperties'] as $apartmentProperty) {
                $apartmentProperties = new PreCertificatePriceEstimateApartmentProperty($apartmentProperty->toArray());
                $apartmentProperties->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
                $apartmentProperties->save();
            }
        }
        if (isset($priceEstimate['landFinalEstimate'])) {
            foreach ($priceEstimate['landFinalEstimate'] as $landFinalEstimate) {
                $finalEstimate = new PreCertificatePriceEstimateFinal($landFinalEstimate->toArray());
                $finalEstimate->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
                $finalEstimate->save();
                $finalEstimateId = $finalEstimate->id;

                if (isset($landFinalEstimate['apartmentFinals'])) {
                    foreach ($landFinalEstimate['apartmentFinals'] as $apartmentFinals) {
                        $apartmentFinal = new PreCertificatePriceEstimateApartmentFinal($apartmentFinals->toArray());
                        $apartmentFinal->pre_certificate_price_estimate_final_id = $finalEstimateId;
                        $apartmentFinal->save();
                    }
                }
            }
        }

        if (isset($priceEstimate['assetGeneralRelation'])) {
            foreach ($priceEstimate['assetGeneralRelation'] as $asset) {
                $dataAsset = new PreCertificatePriceEstimateHasAsset($asset->toArray());
                $dataAsset->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
                $dataAsset->save();
            }
        }
        if (isset($priceEstimate->lastVersion)) {
            $lastVersionData = is_object($priceEstimate->lastVersion) && method_exists($priceEstimate->lastVersion, 'toArray')
                ? $priceEstimate->lastVersion->toArray()
                : (array) $priceEstimate->lastVersion;

            $lastVersion = new PreCertificatePriceEstimateVersion($lastVersionData);
            $lastVersion->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
            $lastVersion->save();
        }
    }

    private function insertAppraiseData(int $preCertificatePriceEstimateId, $priceEstimate)
    {
        if (isset($priceEstimate['properties'])) {
            foreach ($priceEstimate['properties'] as $appraiseProperty) {
                $appraiseProperty = new PreCertificatePriceEstimateProperty($appraiseProperty->toArray());
                $appraiseProperty->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
                $appraiseProperty->save();
                $appraisePropertyId = $appraiseProperty->id;

                if (isset($appraiseProperty['propertyDetail'])) {
                    foreach ($appraiseProperty['propertyDetail'] as $propertyDetail) {
                        $propertyDetailData = new PreCertificatePriceEstimatePropertyDetail($propertyDetail->toArray());
                        $propertyDetailData->pre_certificate_price_estimate_final_id = $appraisePropertyId;
                        $propertyDetailData->save();
                    }
                }

                if (isset($appraiseProperty['propertyTurningTime'])) {
                    foreach ($appraiseProperty['propertyTurningTime'] as $propertyTurningTime) {
                        $propertyTurningTimeData = new PreCertificatePriceEstimatePropertyTurningTime($propertyTurningTime->toArray());
                        $propertyTurningTimeData->pre_certificate_price_estimate_final_id = $appraisePropertyId;
                        $propertyTurningTimeData->save();
                    }
                }
            }
        }
        if (isset($priceEstimate['landFinalEstimate'])) {
            foreach ($priceEstimate['landFinalEstimate'] as $landFinalEstimate) {
                $finalEstimate = new PreCertificatePriceEstimateFinal($landFinalEstimate->toArray());
                $finalEstimate->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
                $finalEstimate->save();
                $finalEstimateId = $finalEstimate->id;

                if (isset($landFinalEstimate['lands'])) {
                    foreach ($landFinalEstimate['lands'] as $land) {
                        $landData = new PreCertificatePriceEstimateFinalLand($land->toArray());
                        $landData->pre_certificate_price_estimate_final_id = $finalEstimateId;
                        $landData->save();
                    }
                }

                if (isset($landFinalEstimate['tangibleAssets'])) {
                    foreach ($landFinalEstimate['tangibleAssets'] as $tangibleAsset) {
                        $tangibleAssetData = new PreCertificatePriceEstimateFinalTangibleAsset($tangibleAsset->toArray());
                        $tangibleAssetData->pre_certificate_price_estimate_final_id = $finalEstimateId;
                        $tangibleAssetData->save();
                    }
                }
            }
        }

        if (isset($priceEstimate['assetGeneralRelation'])) {
            foreach ($priceEstimate['assetGeneralRelation'] as $asset) {
                $dataAsset = new PreCertificatePriceEstimateHasAsset($asset->toArray());
                $dataAsset->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
                $dataAsset->save();
            }
        }

        if (isset($priceEstimate->lastVersion)) {
            $lastVersionData = is_object($priceEstimate->lastVersion) && method_exists($priceEstimate->lastVersion, 'toArray')
                ? $priceEstimate->lastVersion->toArray()
                : (array) $priceEstimate->lastVersion;

            $lastVersion = new PreCertificatePriceEstimateVersion($lastVersionData);
            $lastVersion->pre_certificate_price_estimate_id = $preCertificatePriceEstimateId;
            $lastVersion->save();
        }
    }

    public function deleteApartmentWithRelations(int $preCertificateId, int $priceEstimateId)
    {
        // Start a database transaction
        try {
            $preCertificatePriceEstimateId = PreCertificatePriceEstimate::query()
                ->where('pre_certificate_id', $preCertificateId)
                ->where('price_estimate_id', $priceEstimateId)
                ->value('id'); // Use value instead of pluck

            $finalEstimateIds = PreCertificatePriceEstimateFinal::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)
                ->pluck('id');

            // Delete the PreCertificatePriceEstimateApartmentFinal records that belong to the PreCertificatePriceEstimateFinal records
            PreCertificatePriceEstimateApartmentFinal::whereIn('pre_certificate_price_estimate_final_id', $finalEstimateIds)->forceDelete();

            PreCertificatePriceEstimateFinal::whereIn('id', $finalEstimateIds)->forceDelete();

            // Delete PreCertificatePriceEstimateApartmentProperty records
            PreCertificatePriceEstimateApartmentProperty::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();

            PreCertificatePriceEstimateVersion::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();

            PreCertificatePriceEstimateHasAsset::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();
            // Delete the PriceEstimate record
            PreCertificatePriceEstimate::where('id', $preCertificatePriceEstimateId)->forceDelete();

            PriceEstimate::where('pre_certificate_id', $preCertificateId)
                ->where('id', $priceEstimateId)
                ->update(['pre_certificate_id' => null]);
            // Commit the transaction
        } catch (\Exception $e) {
            // An error occurred; cancel the transaction...
            throw $e;
        }
    }

    public function deleteAppraiseWithRelations(int $preCertificateId, int $priceEstimateId)
    {
        // Start a database transaction

        try {
            $preCertificatePriceEstimateId = PreCertificatePriceEstimate::query()
                ->where('pre_certificate_id', $preCertificateId)
                ->where('price_estimate_id', $priceEstimateId)
                ->value('id'); // Use value instead of pluck

            $finalEstimateIds = PreCertificatePriceEstimateFinal::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)
                ->pluck('id');

            // Delete the PreCertificatePriceEstimateApartmentFinal records that belong to the PreCertificatePriceEstimateFinal records
            PreCertificatePriceEstimateFinalLand::whereIn('pre_certificate_price_estimate_final_id', $finalEstimateIds)->forceDelete();

            PreCertificatePriceEstimateFinalTangibleAsset::whereIn('pre_certificate_price_estimate_final_id', $finalEstimateIds)->forceDelete();

            PreCertificatePriceEstimateFinal::whereIn('id', $finalEstimateIds)->forceDelete();

            $propertyIds = PreCertificatePriceEstimateProperty::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)
                ->pluck('id');

            // Delete the related PreCertificatePriceEstimatePropertyDetail records
            PreCertificatePriceEstimatePropertyDetail::whereIn('pre_certificate_price_estimate_property_id', $propertyIds)->forceDelete();

            // Delete the related PreCertificatePriceEstimatePropertyTurningTime records
            PreCertificatePriceEstimatePropertyTurningTime::whereIn('pre_certificate_price_estimate_property_id', $propertyIds)->forceDelete();

            // Delete the PreCertificatePriceEstimateProperty records
            PreCertificatePriceEstimateProperty::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();

            PreCertificatePriceEstimateVersion::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();

            PreCertificatePriceEstimateHasAsset::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();
            // Delete the PriceEstimate record
            PreCertificatePriceEstimate::where('id', $preCertificatePriceEstimateId)->forceDelete();

            PriceEstimate::where('pre_certificate_id', $preCertificateId)
                ->where('id', $priceEstimateId)
                ->update(['pre_certificate_id' => null]);
            // Commit the transaction
        } catch (\Exception $e) {
            // An error occurred; cancel the transaction...
            // and rethrow the exception
            throw $e;
        }
    }

    public function deletePriceEstimateWithRelations(int $preCertificateId)
    {
        try {
            $preCertificatePriceEstimateIds = PreCertificatePriceEstimate::query()
                ->where('pre_certificate_id', $preCertificateId)
                ->pluck('id');

            foreach ($preCertificatePriceEstimateIds as $preCertificatePriceEstimateId) {
                $finalEstimateIds = PreCertificatePriceEstimateFinal::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)
                    ->pluck('id');

                PreCertificatePriceEstimateFinalLand::whereIn('pre_certificate_price_estimate_final_id', $finalEstimateIds)->forceDelete();
                PreCertificatePriceEstimateFinalTangibleAsset::whereIn('pre_certificate_price_estimate_final_id', $finalEstimateIds)->forceDelete();
                PreCertificatePriceEstimateApartmentFinal::whereIn('pre_certificate_price_estimate_final_id', $finalEstimateIds)->forceDelete();
                PreCertificatePriceEstimateApartmentProperty::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();
                PreCertificatePriceEstimateFinal::whereIn('id', $finalEstimateIds)->forceDelete();

                $propertyIds = PreCertificatePriceEstimateProperty::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)
                    ->pluck('id');

                PreCertificatePriceEstimatePropertyDetail::whereIn('pre_certificate_price_estimate_property_id', $propertyIds)->forceDelete();
                PreCertificatePriceEstimatePropertyTurningTime::whereIn('pre_certificate_price_estimate_property_id', $propertyIds)->forceDelete();
                PreCertificatePriceEstimateProperty::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();
                PreCertificatePriceEstimateVersion::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();
                PreCertificatePriceEstimateHasAsset::where('pre_certificate_price_estimate_id', $preCertificatePriceEstimateId)->forceDelete();
            }

            PreCertificatePriceEstimate::whereIn('id', $preCertificatePriceEstimateIds)->forceDelete();

            PriceEstimate::where('pre_certificate_id', $preCertificateId)
                ->update(['pre_certificate_id' => null]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
