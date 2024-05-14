<?php

namespace App\Repositories;

use App\Contracts\RealEstateRepository;
use App\Enum\ErrorMessage;
use App\Enum\ValueDefault;
use App\Services\CommonService;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Log;
use stdClass;

class EloquentRealEstateRepository extends EloquentRepository implements RealEstateRepository
{
    public function findPaging()
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $status = request()->get('status');

        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;
        $user = CommonService::getUser();
        $select = [
            'id',
            'asset_type_id',
            'appraise_asset',
            'total_area',
            'created_by',
            'coordinates',
            'front_side',
            'status',
            'round_total',
            'created_at',
            'updated_at',
            'total_price',
        ];
        $result = $this->model->query()->with(['createdBy', 'assetType', 'asset:id,real_estate_id', 'appraises', 'apartment'])->select($select);
        $role = $user->roles->last();
        if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN')) {
            $result = $result->where('created_by', $user->id);
        }
        if (isset($search)) {
            $filterSubstr = substr($search, 0, 1);
            $filterData = substr($search, 1);
            switch ($filterSubstr) {
                case '!':
                    if (floatval($filterData) >= 0) {
                        $result = $result->where('total_area', floatval($filterData));
                    }
                    break;
                case '@':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy', function ($has) use ($filterData) {
                            $has->where('name', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                case '&':
                    $data = explode('/', $filterData);
                    $doc_no = $data[0];
                    $land_no = isset($data[1]) ? $data[1] : -1;
                    if (intval($doc_no) >= 0) {
                        $result = $result->where(function ($q) use ($doc_no, $land_no) {
                            $q = $q->whereHas('appraises.appraiseLaw.landDetails', function ($has) use ($doc_no, $land_no) {
                                $has->where('doc_no', '=', $doc_no);
                                if (intval($land_no) >= 0)
                                    $has = $has->Where('land_no', '=', $land_no);
                            });
                        });
                    }
                    break;
                case '$':
                    if (floatval($filterData) >= 0) {
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        $result = $result->whereBetween('total_price', [$fromValue, $toValue]);
                    }
                    break;
                case '^':
                    $result = $result->where(function ($q) use ($search) {
                        $q = $q->whereHas('appraises', function ($query) use ($search) {
                            $query->where('full_address', 'ILIKE', '%' . $search . '%');
                        })->orWhereHas('apartment', function ($query) use ($search) {
                            $query->where('full_address', 'ILIKE', '%' . $search . '%');
                        });
                    });
                    break;
                default:
                    $result = $result->where(function ($q) use ($search) {
                        $q = $q->where('id', 'like', strval($search));
                        $q = $q->orwhere('appraise_asset', 'ILIKE', '%' . $search . '%');
                        $q = $q->orwhereHas('assetType', function ($has) use ($search) {
                            $has->where('description', 'ILIKE', '%' . $search . '%');
                        });
                        $q = $q->orwhereHas('createdBy', function ($has) use ($search) {
                            $has->where('name', 'ILIKE', '%' . $search . '%');
                        });
                    });
            }
        }
        if (isset($status)) {
            $result = $result->whereIn('status', $status);
        }
        $result = $result->orderByDesc('updated_at');
        $result = $result->orderByDesc('id');
        $result = $result->forPage($page, $perPage)
            ->paginate($perPage);
        return $result;
    }
    public function getReportData($id)
    {
        $select = [
            '*'
        ];
        $with = [
            'appraises',
            'apartment',
            'createdBy:id,name'
        ];
        $realEstate = $this->model->query()->where('id', $id)->with($with)->first($select);
        if (!empty($realEstate)) {
            $result = new stdClass();
            // $result = new Collection();
            $result->realEstate = $realEstate;
            $date = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
            $result->document_date = $date;
            $result->createdBy  = $realEstate->createdBy;
            $result->document_type = ['DT'];
            $result->id = $realEstate->id;
            if (!empty($realEstate->apartment))
                $result->document_type = ['CC'];
            elseif (!empty($realEstate->appraises->tangibleAssets) && count($realEstate->appraises->tangibleAssets) > 0)
                $result->document_type = ['DCN'];
            return $result;
        } else {
            return ['message' => ErrorMessage::APPRAISE_NOTEXISTS . $id, 'exception' => ''];
        }
    }
    public function getDataForShinhan($id)
    {
        $select = [
            '*'
        ];
        $with = [
            'appraises',
            'apartment',
            'createdBy:id,name,phone',
            'certificate:id,certificate_num,appraise_date'
        ];
        $realEstate = $this->model->query()->where('id', $id)->with($with)->first($select);
        if (!empty($realEstate)) {
            $result = new stdClass();
            // $result = new Collection();
            $result->realEstate = $realEstate;
            $date = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
            $result->document_date = $date;
            $result->createdBy  = $realEstate->createdBy;
            $result->document_type = ['DT'];
            $result->id = $realEstate->id;
            if (!empty($realEstate->apartment))
                $result->document_type = ['CC'];
            elseif (!empty($realEstate->appraises->tangibleAssets) && count($realEstate->appraises->tangibleAssets) > 0)
                $result->document_type = ['DCN'];
            return $result;
        } else {
            return ['message' => ErrorMessage::APPRAISE_NOTEXISTS . $id, 'exception' => ''];
        }
    }

    public function updateRealEstateAditionalData(array $realEstate, $id)
    {
        try {
            $dataUpdate = [
                'planning_info' => $realEstate['planning_info'] ?: '',
                'planning_source' => $realEstate['planning_source'] ?: '',
                'contact_person' => $realEstate['contact_person'] ?: '',
                'contact_phone' => $realEstate['contact_phone'] ?: '',
            ];
            if ($this->model->find($id)->update($dataUpdate)) {
                return $realEstate;
            } else {
                return ['message' => ErrorMessage::SYSTEM_ERROR];
            }
        } catch (Exception $e) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $e->getMessage()];
            return $data;
        }
    }

    public function updateStatus(array $request, $id)
    {
        $dataUpdate = [];
        if (isset($request['status'])) {
            $dataUpdate = [
                'status' => $request['status'] ?: '',
            ];
        } else {
            return ['message' => "Vui lòng nhập thông tin trạng thái"];
        }
        try {
            $realEstate = $this->model->find($id);
            if ($realEstate && $realEstate->appraises) {
                if ($realEstate->update($dataUpdate) && $realEstate->appraises->update($dataUpdate)) {
                    $this->CreateActivityLog($realEstate->appraises, $dataUpdate, 'update_status', 'cập nhật trạng thái ' . mb_strtolower($realEstate->appraises->status_text));
                    return $dataUpdate;
                } else {
                    return ['message' => ErrorMessage::SYSTEM_ERROR];
                }
            }
            if ($realEstate && $realEstate->apartment) {
                if ($realEstate->update($dataUpdate) && $realEstate->apartment->update($dataUpdate)) {
                    $this->CreateActivityLog($realEstate->apartment, $dataUpdate, 'update_status', 'cập nhật trạng thái ' . mb_strtolower($realEstate->apartment->status_text));
                    return $dataUpdate;
                } else {
                    return ['message' => ErrorMessage::SYSTEM_ERROR];
                }
            }
        } catch (Exception $e) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $e->getMessage()];
            return $data;
        }
    }

    public function CreateActivityLog($model, $request, $activity, $log, $note = '')
    {
        // if (is_object($model)) {
        // $loga = $this->CustomizeLogMessage($model, $activity, $log);

        // dd ($loga);
        activity($activity)
            ->by(CommonService::getUser())
            ->on($model)
            ->withProperties([
                'data' => [$request],
                'note' => $note
            ])
            ->log($log);
        // }
    }

    public function exportCertificateAssets()
    {
        $assetTypeId = request()->get('asset_type_id');
        $createdBy = request()->get('created_by');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $where = [];
        $select = [
            'id',
            'appraise_asset',
            'asset_type_id',
            'front_side',
            'total_area',
            'total_price',
            'created_at',
            'created_by',
            'asset_type_id',
            'coordinates',
        ];
        $with = [
            'assetFull',
            'assetType:id,description',
            'createdBy:id,name',
        ];
        $result = $this->model->query()->with($with)->where($where)->select($select);
        if (!empty($assetTypeId)) {
            $result->whereHas('assetType', function ($has) use ($assetTypeId) {
                $has->where('id', $assetTypeId);
            });
        }
        if (!empty($createdBy)) {
            $list_user = explode(',', $createdBy);
            $result = $result->whereIn('created_by', $list_user);
        }
        if (!empty($fromDate) && $fromDate != 'Invalid date') {
            $result->whereRaw("created_at >= to_date('$fromDate', 'dd/MM/yyyy') ");
        }
        if (!empty($toDate) && $toDate != 'Invalid date') {
            $result->whereRaw("created_at <= to_date('$toDate', 'dd/MM/yyyy') + '1 day'::interval");
        }
        // dd($result->limit(5)->get()->append('total_construction_base')->toArray());
        return $result->get()->append('total_construction_base');
    }
}
