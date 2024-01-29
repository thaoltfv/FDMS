<?php

namespace App\Repositories;

use App;
use App\Contracts\PreCertificateRepository;
use App\Enum\CompareMaterData;
use App\Enum\ErrorMessage;
use App\Models\Certificate;
use App\Models\CertificateOtherDocuments;
use App\Models\PreCertificate;
use App\Models\Customer;
use App\Models\Appraise;
use App\Models\PreCertificateOtherDocuments;
use App\Models\PreCertificatePayments;
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
        return DB::transaction(function () use ($id,$typeDocument, $request) {
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
        return ;
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
            if (isset($dataTemp) && isset($dataTemp->timeFilter) ) {
                if ( isset($dataTemp->timeFilter->from)) {
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
            // 'users.image',
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
                            then 'Hoàn thành'
                        when 6
                            then 'Hủy'
                    end as status_text
                "),
            'total_preliminary_value',
            // Db::raw("COALESCE(document_count,0) as document_count"),
            'status_expired_at',
        ];
        $with = [
            'createdBy:id,name',
            'appraisePurpose:id,name',
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
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
        if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN')) {
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
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('pre_certificates.id', 'like', strval($filter));
                        $q = $q->orwhere('petitioner_name', 'ILIKE', '%' . $filter . '%');
                    });
            }
        }
        
        // dd($result);

            if (isset($timeFilterFrom) && isset($timeFilterTo)) {
                $startDate = date('Y-m-d', strtotime($timeFilterFrom));
                $endDate = date('Y-m-d', strtotime($timeFilterTo));
                $result = $result->whereBetween('created_at', [$startDate, $endDate])
                                ->whereBetween('updated_at', [$startDate, $endDate]);
            }   elseif (isset($timeFilterFrom)) {
                    $startDate = date('Y-m-d', strtotime($timeFilterFrom));
                    $result = $result->where('created_at', '>=', $startDate)
                                    ->where('updated_at', '>=', $startDate);
            } elseif (isset($timeFilterTo)) {
                    $endDate = date('Y-m-d', strtotime($timeFilterTo));
                    $result = $result->where('created_at', '<=', $endDate)
                                ->where('updated_at', '<=', $endDate);
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

        $result = $result->orderByDesc('pre_certificates.updated_at');
        // dd(DB::getQueryLog());
        $result = $result
            ->forPage($page, $perPage)
            ->paginate($perPage);
        
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
            if (isset($dataTemp) && isset($dataTemp->timeFilter) ) {
                if ( isset($dataTemp->timeFilter->from)) {
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
            // 'users.image',
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
                            then 'Hoàn thành'
                        when 6
                            then 'Hủy'
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
                            then u2.image
                        when 4
                            then u1.image
                        when 5
                            then u2.image
                        when 6
                            then u1.image
                    end as image
                "),
        ];
        $with = [
            'createdBy:id,name',   
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'cancelReason:id,description',
            'otherDocuments' => function ($query) {
                $query->whereNull('deleted_at');
            },
            'preType:id,description',
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
        // dd($role->name);
        if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN')) {
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
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('pre_certificates.id', 'like', strval($filter));
                        $q = $q->orwhere('petitioner_name', 'ILIKE', '%' . $filter . '%');
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
        $result= $result->get();
     
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
            if (isset( $objects['appraiser_sale_id'])) {
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
                if (!isset($oldPreCertificate['created_by']))
                {
                    $data['created_by'] = $user->id;
                }
                else if(isset($oldPreCertificate['created_by']) && isset($oldPreCertificate['created_by']->id))
                {
                    $data['created_by'] = $oldPreCertificate['created_by']->id;
                }
                else
                {
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

                
                PreCertificate::where('id', $preCertificateId)->update($data);
                $edited = PreCertificate::where('id', $preCertificateId)->first();
                $edited->fill($data);
                $edited->save();
                $changeLog = $edited->getChanges();
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
        $check = $this->checkAuthorizationPreCertificate($id);
        if (!empty($check))
            return $check;
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
            'created_at',
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
            'preType:id,description',
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
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
        }
        if ($result['status'] == 2) {
            $appraiser = Appraiser::query()
            ->where('id', '=', $result['appraiser_perform_id'])
            ->first();
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
        }
        if ($result['status'] == 3 || $result['status'] == 4) {
            $appraiser = Appraiser::query()
            ->where('id', '=', $result['appraiser_perform_id'])
            ->first();
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
        }
        if ($result['status'] == 6) {
            $appraiser = Appraiser::query()
            ->where('id', '=', $result['business_manager_id'])
            ->first();
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
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
                if($preCertificate->status != 5){
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
                    $preCertificatePayments = PreCertificatePayments::where('pre_certificate_id',$preCertificate->id)->get();

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
                // # đang tắt khối block xác thực
                // $check = $this->beforeUpdateStatus($id);
                // if (isset($check)) {
                //     return $check;
                // }
                $preCertificate = $this->model->query()->where('id', $id)->first();

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

                    $result = $this->model->query()
                        ->where('id', '=', $id)
                        ->update($updateArray);
                    
                    # Chuyển status từ số sang text
                    $edited = PreCertificate::where('id', $id)->first();
                    if ($current > $next) {
                        // $logDescription = $request['status_description'] . ' '.  $request['status_config']['description'];
                        $description = $currentConfig !== false ? $currentConfig['description'] : '';
                        $logDescription = 'từ chối ' .  $description;
                        if ($logDescription == "từ chối Hủy") {
                            $logDescription = "Khôi phục YCSB";
                        }
                    }
                    else {
                        $description = $nextConfig !== false ? $nextConfig['description'] : '';
                        $logDescription = 'cập nhật trạng thái '. $description;
                    }
                    $logName = 'update_status';
                    // activity-log Update status
                    $note = $request['status_note'] ?? '';
                    $reason_id = $request['status_reason_id'] ?? null;
                    $this->CreateActivityLog($edited, $edited, $logName, $logDescription, $note, $reason_id);

                    $this->notifyChangeStatus($id, $status);
                    if (!empty($assignTo)) {
                        $this->notifyReAssign($id, $status, $assignTo);
                    }
                }
                // $result = $this->getAppraisalTeam($id);
                $result = $this->getPreCertificate($id);

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
                        if(isset($item['id'])){
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
                        if (!($data->created_by == $user->id || $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có người tạo phiếu và nhân viên Sale mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
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
            if (!empty($required)) {
                $isCheckAppraiser =  $required['appraiser'];
                $isCheckTotalPreliminaryValue =  $required['total_preliminary_value'];

        
                if ($isCheckAppraiser) {
                    $appraiser['appraiser_sale_id'] =  request()->get('appraiser_sale_id');
                    $appraiser['business_manager_id'] =  request()->get('business_manager_id');
                    $appraiser['appraiser_perform_id'] =  request()->get('appraiser_perform_id');
                    if (empty($appraiser['appraiser_sale_id']) || empty($appraiser['business_manager_id']) || empty($appraiser['appraiser_perform_id'])) {
                        return ['message' => ErrorMessage::CERTIFICATE_APPRAISERTEAM, 'exception' => ''];
                    }
                }
            }
            //Check role and permision
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN'])) {
                switch ($data['status']) {
                    case 1:
                        if (!($data->created_by == $user->id || $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có người tạo phiếu và nhân viên kinh doanh mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 3:
                        if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 4:
                        if (!( $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có nhân viên kinh doanh mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 6:
                        if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
                        break;
                }
            }
        } else {
            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

   
    private function notifyChangeStatus(int $id, int $status)
    {
        if (PreCertificate::where('id', $id)->exists()) {
            $loginUser = CommonService::getUser();
            $users[] = $loginUser;
            // $users= $loginUser;
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
                    $statusText = 'Hoàn thành';
                    break;
                case 6:
                    $statusText = 'Đã hủy';
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

            CommonService::callNotification($users, $data);
        }
    }
    private function notifyReAssign(int $id, int $status, $assignTo)
    {
        if (PreCertificate::where('id', $id)->exists()) {
            $loginUser = CommonService::getUser();
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
                    $statusText = 'Hoàn thành';
                    break;
                case 6:
                    $statusText = 'Đã hủy';
                    break;
                default:
                    $statusText = 'Yêu cầu sơ bộ';
            }
            $preCertificate = PreCertificate::with($with)->where('id', $id)->get($select)->first();
            $eloquenUser = new EloquentUserRepository(new User());
            foreach ($assignTo as $assign) {
                $user = null;
                if (isset($preCertificate[$assign]) && isset($preCertificate[$assign]->user_id)) {
                    if ($preCertificate[$assign]->user_id != $loginUser->id) {
                        $user = $eloquenUser->getUser($preCertificate[$assign]->user_id);
                    }
                }

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
                    'message' => 'YCSB_' . $id .' '. $preCertificate[$assign]->user_id.' bạn được' . $loginUser->name . ' phân công làm ' . $typeAssign . '.',
                    'user' => $loginUser,
                    'id' => $id
                ];
                if ($user) {
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
        $appraiser = request()->get('appraiser_id');
        $appraiserSale = request()->get('appraiser_sale_id');
        $appraiserConfirm = request()->get('appraiser_confirm_id');
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
            'pre_certificates.id',
            'petitioner_name',
            'pre_date',
            'status',
            'pre_certificates.created_at',
            'appraise_purpose_id',
            'created_by',
            'appraiser_id',
            'appraiser_perform_id',
            'appraiser_sale_id',
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
                    then 'Hoàn thành'
                when 6
                    then 'Hủy'
                end as status_text"),
            'commission_fee',
        ];
        $with = [
            'createdBy:id,name',
            'appraiser:id,name,user_id',
            // 'appraiserManager:id,name,user_id',
            // 'appraiserConfirm:id,name,user_id',
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraisePurpose:id,name',
            // 'assetPrice' => function ($q) {
            //     $q->where('slug', '=', 'total_asset_price');
            // },
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
            $result = $result->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'");
        }
        if (isset($appraiser)) {
            $result = $result->where('appraiser_id', $appraiser);
        }
        if (isset($appraiserSale)) {
            $result = $result->where('appraiser_sale_id', $appraiserSale);
        }
        if (isset($appraiserConfirm)) {
            $result = $result->where('appraiser_comfirm_id', $appraiserConfirm);
        }
        if (isset($customer)) {
            $result = $result->where('customer_id', $customer);
        }
        $result = $result->get();

        return $result;
    }


    private function checkAuthorizationPreCertificate ($id)
    {
        $check = null;
        if ($this->model->query()->where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $role = $user->roles->last();
            $result = $this->model->query()->where('id', $id);
            $userId = $user->id;
            if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN')) {
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
                $check = ['message' => 'Bạn không có quyền ở YCSB '. $id , 'exception' => '', 'statusCode' => 403];
        } else {
            $check = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        }
        return $check;
    }
}
