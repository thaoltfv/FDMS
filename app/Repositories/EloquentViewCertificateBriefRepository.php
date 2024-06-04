<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use App\Contracts\ViewCertificateBrieftRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PreCertificate;
use App\Models\Certificate;
use App\Services\CommonService;

class EloquentViewCertificateBriefRepository extends EloquentRepository implements ViewCertificateBrieftRepository
{
    public function countBrieftStatus()
    {
        // return $this->countBrieftBacklog();
        // return $this->countBriefInProcessing();
        $this->model->refresh();

        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        $result = $this->model
            ->select(['status_text', 'status', DB::Raw("count(id)")])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->groupby(['status_text', 'status'])
            ->orderBy('status')
            ->get()->toArray();

        $result = array('label' => Arr::pluck($result, 'status_text'), 'data' => Arr::pluck($result, 'count'), 'status' => Arr::pluck($result, 'status'));

        return $result;
    }

    public function countBrieftStatusExpired()
    {
        $this->model->refresh();

        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        $result = $this->model
            ->select([
                DB::Raw("count(id)"),
                DB::Raw("case
                            when status_expired_at is null
                                then 'expired'
                            else
                                case when status_expired_at < now()
                                        then 'expired'
                                    else
                                        'none'
                                end
                            end as expired
                        ")
            ])
            ->whereIn('status', [1, 2, 3])
            ->whereRaw("to_char(coalesce(status_updated_at,updated_at) , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->groupby(['expired'])
            ->orderByDesc('expired')
            ->get()->toArray();

        $data = Arr::pluck($result, 'count');
        $total = array_sum($data);
        $cal = [];
        foreach ($data as $item) {
            $cal[] = round($item * 100 / $total, 2);
        }
        $result = array('label' => Arr::pluck($result, 'expired'), 'data' => $cal, 'number' => $data);
        return $result;
    }

    public function countBrieftStatusByAppraiser()
    {
        $this->model->refresh();

        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        $saleData = $this->model
            ->select([
                DB::raw("count(id) , appraiser_sale_id as id , appraiser_sale_name as name, 'sale' as type, 'none' as expire"),
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->whereIn('status', [1, 2, 3, 4])
            ->groupBy(['appraiser_sale_id', 'appraiser_sale_name']);

        $saleDataExpired = $this->model
            ->select([
                DB::raw("count(id) , appraiser_sale_id as id , appraiser_sale_name as name, 'sale' as type, 'expired' as expire"),
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->whereRaw("(status_expired_at < now() or status_expired_at is null)")
            ->whereIn('status', [1])
            ->groupBy(['appraiser_sale_id', 'appraiser_sale_name']);
        // dd($saleData);
        $performData = $this->model
            ->select([
                DB::raw("count(id) , appraiser_perform_id as id , appraiser_perform_name as name, 'perform' as type, 'none' as expire"),
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->whereIn('status', [2, 3, 4])
            ->groupBy(['appraiser_perform_id', 'appraiser_perform_name']);

        $performDataExpired = $this->model
            ->select([
                DB::raw("count(id) , appraiser_perform_id as id , appraiser_perform_name as name, 'perform' as type, 'expired' as expire"),
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->whereRaw("(status_expired_at < now() or status_expired_at is null)")
            ->whereIn('status', [2])
            ->groupBy(['appraiser_perform_id', 'appraiser_perform_name']);
        $appraiserData = $this->model
            ->select([
                DB::raw("count(id) , appraiser_id as id , appraiser_name as name, 'appraiser' as type, 'none' as expire"),
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->whereIn('status', [3, 4])
            ->groupBy(['appraiser_id', 'appraiser_name']);

        $appraiserDataExpire = $this->model
            ->select([
                DB::raw("count(id) , appraiser_id as id , appraiser_name as name, 'appraiser' as type, 'expired' as expire"),
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->whereRaw("(status_expired_at < now() or status_expired_at is null)")
            ->whereIn('status', [3])
            ->groupBy(['appraiser_id', 'appraiser_name'])
            ->unionAll($appraiserData)
            ->unionAll($performData)
            ->unionAll($performDataExpired)
            ->unionAll($saleData)
            ->unionAll($saleDataExpired)
            ->get()->toArray();

        // dd($appraiserDataExpire);
        $result = $appraiserData;

        return $result;
    }

    public function countBrieftStatusByMonth()
    {
        // return $this->countBriefFinishByQuarters();
        $this->model->refresh();

        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $status = request()->get('status');

        if (isset($status))
            // $status = json_decode($status);
            $status = explode(',', $status);
        else
            $status = [1, 4];

        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        $date = $fromDate;
        $monthList = [];
        $stt = 0;

        while ($date <= $toDate) {
            $monthList[$stt]['month_year'] = $date->month . '_' . $date->year;
            $monthList[$stt]['label'] = 'Tháng ' . $date->month . ' năm ' . $date->year;
            $date = $date->addMonth(1);
            $stt++;
        }
        $fromDate = request()->get('fromDate');
        $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);

        $monthPluck = Arr::pluck($monthList, 'month_year');
        $label = Arr::pluck($monthList, 'label');

        $stt = 0;
        $dataRaw = $this->model
            ->select([
                DB::raw("count(id)"),
                'status',
                DB::raw("Concat(date_part('month', status_updated_at) ,'_',date_part('year', status_updated_at)) as month_year"),
            ])
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'")
            ->whereIn('status', $status)
            ->groupBy(['status_text', 'status', 'month_year'])
            ->orderBy('month_year')
            ->orderBy('status')
            ->get()->toArray();

        $data = [];
        $stt = 0;
        foreach ($monthPluck as $month_year) {
            foreach ($status as $item) {
                $filter = array_filter($dataRaw, function ($value) use ($month_year, $item) {
                    return $value['month_year'] == $month_year and $value['status'] == $item;
                });
                if (empty($filter)) {
                    $addData = ['count' => 0, 'status' => $item, 'month_year' => $month_year];
                    $data[$item][$stt] = $addData;
                } else {
                    foreach ($filter as $fil) {
                        $data[$item][$stt] =  $fil;
                    }
                }
                $stt++;
            }
        }
        $result = [];
        $stt = 0;
        foreach ($status as $item) {
            $count = Arr::pluck($data[$item], 'count');
            $status_text = $this->getStatusText($item);
            $result[$stt]['label'] = $status_text;
            $result[$stt]['count'] = $count;
            $result[$stt]['status'] = $item;
            $stt++;
        }
        $result = array_merge(['label' => $label], ['data' => $result]);
        return $result;
    }

    public function countBrieftBacklog()
    {
        $date = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-01');
        // dd($date);
        $this->model->refresh();
        $backlogSelect = [
            DB::raw("'Backlog' as type,'Tồn cũ' as description, count(id) as count")
        ];
        $newlogSelect = [
            DB::raw("'New' as type,'Mới trong tháng' as description, count(id) as count")
        ];
        $backlog = $this->model
            ->query()
            ->select($backlogSelect)
            ->where('created_at', '<', $date)
            ->whereIn('status', [1, 2, 3, 6]);

        $data = $this->model
            ->query()
            ->select($newlogSelect)
            ->where('created_at', '>=', $date)
            ->whereIn('status', [1, 2, 3, 6])
            ->unionAll($backlog)
            ->get()->toArray();
        // ->get()->toArray();
        $result = array('label' => Arr::pluck($data, 'description'), 'data' => Arr::pluck($data, 'count'), 'status' => Arr::pluck($data, 'type'));
        return $result;
    }

    public function countBriefInProcessing()
    {
        $this->model->refresh();

        $select = [
            DB::raw("status,status_text, count(id) as count")
        ];
        $data = $this->model
            ->query()
            ->select($select)
            ->whereIn('status', [1, 2, 3, 6])
            ->groupBy(['status', 'status_text'])
            ->get()->toArray();
        $result = array('label' => Arr::pluck($data, 'status_text'), 'data' => Arr::pluck($data, 'count'), 'status' => Arr::pluck($data, 'status'));
        return $result;
    }
    // Chart cho nhóm đối tác
    public function countBriefInProcessingPreCertificate()
    {
        $this->model->refresh();
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $user = CommonService::getUser();
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }
        $result =
            PreCertificate::query()->select([
                DB::raw("case status
                when 1
                    then 'Tiếp nhận yêu cầu'
                when 2
                    then 'Đang thực hiện'
                when 3
                    then 'Đang thực hiện'
                when 4
                    then 'Đang thực hiện'
                when 5
                    then 'Đang thực hiện'
                when 6
                    then 'Hoàn thành'
                when 7
                    then 'Hủy'
                when 8
                    then 'Đang thực hiện'
                end as status_text"),
                DB::raw("case status
                when 1
                    then 1
                when 2
                    then 2
                when 3
                    then 2
                when 4
                    then 2
                when 5
                    then 2
                when 6
                    then 6
                when 7
                    then 7
                when 8
                    then 2
                end as status_group"),
                DB::Raw("count(id)")
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->groupby(['status_text', 'status_group'])
            ->whereHas('customerGroup', function ($q) use ($user) {
                if ($user->name_lv_1 && $user->name_lv_1 != '') {
                    $q->where('name_lv_1', 'ILIKE', '%' . $user->name_lv_1 . '%');
                }
                if ($user->name_lv_2 && $user->name_lv_2 != '') {
                    $q->where('name_lv_2', 'ILIKE', '%' . $user->name_lv_2 . '%');
                }
                if ($user->name_lv_3 && $user->name_lv_3 != '') {
                    $q->where('name_lv_3', 'ILIKE', '%' . $user->name_lv_3 . '%');
                }
                if ($user->name_lv_4 && $user->name_lv_4 != '') {
                    $q->where('name_lv_4', 'ILIKE', '%' . $user->name_lv_4 . '%');
                }
                return $q;
                // if ($user->customer_group_id) {
                //     return $q->where('id', $user->customer_group_id);
                // }
            })
            ->orderBy('status_group')
            ->get()->toArray();
        $result = array('label' => Arr::pluck($result, 'status_text'), 'data' => Arr::pluck($result, 'count'), 'status' => Arr::pluck($result, 'status_group'));
        return $result;
    }

    public function countBriefInProcessingCertificate()
    {
        $this->model->refresh();
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $user = CommonService::getUser();
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }
        $result =
            Certificate::query()->select([
                DB::raw("case status
                            when 1
                            then 'Tiếp nhận hồ sơ'
                        when 2
                            then 'Đang thực hiện'
                        when 3
                            then 'Đang thực hiện'
                        when 4
                            then 'Hoàn thành'
                        when 5
                            then 'Huỷ'
                        when 7
                            then 'Đang thực hiện'
                        when 8
                            then 'Đang thực hiện'
                        when 9
                            then 'Đang thực hiện'
                        when 10
                            then 'Đang thực hiện'
                    end as status_text
                    "),
                DB::raw("case status
                    when 1
                        then 1
                    when 2
                        then 2
                    when 3
                        then 2
                    when 4
                        then 4
                    when 5
                        then 5
                    when 7
                        then 2
                    when 8
                        then 2
                    when 9
                        then 2
                    when 10
                        then 2
                end as status_group
            "),
                DB::Raw("count(id)")
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'")
            ->whereHas('customerGroup', function ($q) use ($user) {
                if ($user->name_lv_1 && $user->name_lv_1 != '') {
                    $q->where('name_lv_1', 'ILIKE', '%' . $user->name_lv_1 . '%');
                }
                if ($user->name_lv_2 && $user->name_lv_2 != '') {
                    $q->where('name_lv_2', 'ILIKE', '%' . $user->name_lv_2 . '%');
                }
                if ($user->name_lv_3 && $user->name_lv_3 != '') {
                    $q->where('name_lv_3', 'ILIKE', '%' . $user->name_lv_3 . '%');
                }
                if ($user->name_lv_4 && $user->name_lv_4 != '') {
                    $q->where('name_lv_4', 'ILIKE', '%' . $user->name_lv_4 . '%');
                }
                return $q;
                // if ($user->customer_group_id) {
                //     return $q->where('id', $user->customer_group_id);
                // }
            })->groupby(['status_text', 'status_group'])
            ->orderBy('status_group')
            ->get()->toArray();
        $result = array('label' => Arr::pluck($result, 'status_text'), 'data' => Arr::pluck($result, 'count'), 'status' => Arr::pluck($result, 'status_group'));
        return $result;
    }
    public function countBriefFinishByMonthCustomerGroup()
    {
        $this->model->refresh();
        $toYear = Carbon::now()->year;
        $fromYear = $toYear - 1;
        $user = CommonService::getUser();
        $fromDate = '1/1/' . $fromYear;
        $toDate = '31/12/' . $toYear;

        $status = [4];
        $year = [$fromYear, $toYear];
        $statusOutput = ['old', 'new'];

        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
        } else {
            return ['message' => 'Lỗi, không xác định được khoảng thời gian cần tìm', 'exception' => ''];
        }

        $date = $fromDate;
        $monthList = [];
        $stt = 0;
        while ($stt < 12) {
            $monthList[$stt]['month'] = $date->month;
            $monthList[$stt]['label'] = 'Tháng ' . $date->month;
            $date = $date->addMonth(1);
            $stt++;
        }

        $fromDate = '1/1/' . $fromYear;
        $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);

        $monthPluck = Arr::pluck($monthList, 'month');
        $label = Arr::pluck($monthList, 'label');

        $stt = 0;
        $dataRaw = Certificate::query()
            ->select([
                DB::raw("count(id)"),
                DB::raw("case status
                        when 1
                            then 'Tiếp nhận hồ sơ'
                        when 2
                            then 'Đang thực hiện'
                        when 3
                            then 'Đang thực hiện'
                        when 4
                            then 'Hoàn thành'
                        when 5
                            then 'Huỷ'
                        when 7
                            then 'Đang thực hiện'
                        when 8
                            then 'Đang thực hiện'
                        when 9
                            then 'Đang thực hiện'
                        when 10
                            then 'Đang thực hiện'
                    end as status_text
                "),
                DB::raw("case status
                        when 1
                            then 1
                        when 2
                            then 2
                        when 3
                            then 2
                        when 4
                            then 4
                        when 5
                            then 5
                        when 7
                            then 2
                        when 8
                            then 2
                        when 9
                            then 2
                        when 10
                            then 2
                    end as status_group
                "),
                DB::raw("date_part('month', status_updated_at) as month"),
                DB::raw("date_part('year', status_updated_at) as year"),
            ])
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'")
            ->whereIn('status', $status)->whereHas('customerGroup', function ($q) use ($user) {
                // if ($user->customer_group_id) {
                //     return $q->where('id', $user->customer_group_id);
                // }
                if ($user->name_lv_1 && $user->name_lv_1 != '') {
                    $q->where('name_lv_1', 'ILIKE', '%' . $user->name_lv_1 . '%');
                }
                if ($user->name_lv_2 && $user->name_lv_2 != '') {
                    $q->where('name_lv_2', 'ILIKE', '%' . $user->name_lv_2 . '%');
                }
                if ($user->name_lv_3 && $user->name_lv_3 != '') {
                    $q->where('name_lv_3', 'ILIKE', '%' . $user->name_lv_3 . '%');
                }
                if ($user->name_lv_4 && $user->name_lv_4 != '') {
                    $q->where('name_lv_4', 'ILIKE', '%' . $user->name_lv_4 . '%');
                }
                return $q;
            })
            ->groupBy(['status_text', 'status_group', 'month', 'year'])
            ->orderBy('month')
            ->orderBy('year')
            ->orderBy('status_group')
            ->get()->toArray();
        $data = [];
        $stt = 0;
        foreach ($monthPluck as $month) {
            foreach ($year as $item) {
                $filter = array_filter($dataRaw, function ($value) use ($month, $item, $status) {
                    return $value['month'] == $month and $value['year'] == $item and $value['status_group'] == $status[0];
                });
                if (empty($filter)) {
                    $addData = ['count' => 0, 'status' => $status[0], 'month' => $month, 'year' => $item];
                    $data[$item][$stt] = $addData;
                } else {
                    foreach ($filter as $fil) {
                        $data[$item][$stt] =  $fil;
                    }
                }
                $stt++;
            }
        }

        $result = [];
        $stt = 0;
        foreach ($year as $item) {
            $indexOfItem = array_search($item, $year);

            $count = Arr::pluck($data[$item], 'count');
            $result[$stt]['label'] = $item;
            $result[$stt]['count'] = $count;
            $result[$stt]['status'] = $statusOutput[$indexOfItem];

            $stt++;
        }
        $result = array_merge(['label' => $label], ['data' => $result]);

        return $result;
    }

    public function countBriefConversionRateCustomerGroup()
    {
        $this->model->refresh();
        $toYear = Carbon::now()->year;
        $fromYear = $toYear;
        $user = CommonService::getUser();
        $fromDate = '1/1/' . $fromYear;
        $toDate = '31/12/' . $toYear;
        $year = [$fromYear, $toYear];
        $statusOutput = ['ChuaChuyenDoi', 'DaChuyenDoi'];

        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
        } else {
            return ['message' => 'Lỗi, không xác định được khoảng thời gian cần tìm', 'exception' => ''];
        }

        $date = $fromDate;
        $monthList = [];
        $stt = 0;
        while ($stt < 12) {
            $monthList[$stt]['month'] = $date->month;
            $monthList[$stt]['label'] = 'Tháng ' . $date->month;
            $date = $date->addMonth(1);
            $stt++;
        }

        $fromDate = '1/1/' . $fromYear;
        $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);

        $monthPluck = Arr::pluck($monthList, 'month');
        $label = Arr::pluck($monthList, 'label');

        $stt = 0;
        $dataRaw = PreCertificate::query()
            ->select([
                DB::raw("count(id)"),
                DB::raw("CASE WHEN certificate_id IS NULL THEN 'ChuaChuyenDoi' ELSE 'DaChuyenDoi' END AS rate_text"),
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
                'status',
                DB::raw("date_part('month', status_updated_at) as month"),
                DB::raw("date_part('year', status_updated_at) as year"),
            ])
            ->where('status', 6)
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'")
            ->whereHas('customerGroup', function ($q) use ($user) {
                // if ($user->customer_group_id) {
                //     return $q->where('id', $user->customer_group_id);
                // }
                if ($user->name_lv_1 && $user->name_lv_1 != '') {
                    $q->where('name_lv_1', 'ILIKE', '%' . $user->name_lv_1 . '%');
                    Log::info($q->toArray());
                }
                if ($user->name_lv_2 && $user->name_lv_2 != '') {
                    $q->where('name_lv_2', 'ILIKE', '%' . $user->name_lv_2 . '%');
                }
                if ($user->name_lv_3 && $user->name_lv_3 != '') {
                    $q->where('name_lv_3', 'ILIKE', '%' . $user->name_lv_3 . '%');
                }
                if ($user->name_lv_4 && $user->name_lv_4 != '') {
                    $q->where('name_lv_4', 'ILIKE', '%' . $user->name_lv_4 . '%');
                }
                return $q;
            })
            ->groupBy(['status_text', 'rate_text', 'status', 'month', 'year'])
            ->orderBy('month')
            ->orderBy('year')
            ->orderBy('status')
            ->get()->toArray();
        $data = [];
        $stt = 0;
        foreach ($monthPluck as $month) {
            foreach ($statusOutput as $item) {
                $filter = array_filter($dataRaw, function ($value) use ($month, $item) {
                    return $value['month'] == $month and $value['rate_text']  === $item;
                });
                if (empty($filter)) {
                    $addData = ['count' => 0, 'status' => '', 'month' => $month, 'year' => ''];
                    $data[$item][$stt] = $addData;
                } else {
                    foreach ($filter as $fil) {
                        $data[$item][$stt] =  $fil;
                    }
                }
                $stt++;
            }
        }

        $result = [];
        $stt = 0;
        foreach ($statusOutput as $item) {
            $indexOfItem = array_search($item, $year);
            $count = Arr::pluck($data[$item], 'count');
            $result[$stt]['label'] = $item;
            $result[$stt]['count'] = $count;
            $result[$stt]['status'] = $statusOutput[$indexOfItem];

            $stt++;
        }
        $result = array_merge(['label' => $label], ['data' => $result]);
        return $result;
    }

    public function countBriefFinishByQuarters()
    {
        $year = Carbon::now('Asia/Ho_Chi_Minh')->format('Y');
        $lastYear = $year - 1;
        $nowQ1 = $this->model
            ->query()
            ->selectRaw("'Quý 1' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-01-01' . "' and '" . $year . '-01-31' . "'")
            ->where('status', 4);
        $nowQ2 = $this->model
            ->query()
            ->selectRaw("'Quý 2' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-03-01' . "' and '" . $year . '-06-30' . "'")
            ->where('status', 4);
        $nowQ3 = $this->model
            ->query()
            ->selectRaw("'Quý 3' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-07-01' . "' and '" . $year . '-09-30' . "'")
            ->where('status', 4);
        $nowQ4 = $this->model
            ->query()
            ->selectRaw("'Quý 4' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-10-01' . "' and '" . $year . '-12-31' . "'")
            ->where('status', 4);
        $lastQ1 = $this->model
            ->query()
            ->selectRaw("'Quý 1' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-01-01' . "' and '" . $lastYear . '-01-31' . "'")
            ->where('status', 4);
        $lastQ2 = $this->model
            ->query()
            ->selectRaw("'Quý 2' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-03-01' . "' and '" . $lastYear . '-06-30' . "'")
            ->where('status', 4);
        $lastQ3 = $this->model
            ->query()
            ->selectRaw("'Quý 3' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-07-01' . "' and '" . $lastYear . '-09-30' . "'")
            ->where('status', 4);
        $lastQ4 = $this->model
            ->query()
            ->selectRaw("'Quý 4' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-10-01' . "' and '" . $lastYear . '-12-31' . "'")
            ->where('status', 4);

        $data = $nowQ1
            ->union($nowQ2)
            ->unionAll($nowQ3)
            ->unionAll($nowQ4)
            ->unionAll($lastQ1)
            ->unionAll($lastQ2)
            ->unionAll($lastQ3)
            ->unionAll($lastQ4)
            ->get()->toArray();

        $label = ['Quý 1', 'Quý 2', 'Quý 3', 'Quý 4'];
        $dataReport = [];
        $stt = 0;
        while ($year >= $lastYear) {
            $filter = array_filter($data, function ($var) use ($year) {
                return ($var['year'] == $year);
            });
            $dataReport[$stt]['label'] = $year;
            $dataReport[$stt]['count'] = Arr::pluck($filter, 'count');
            $stt++;
            $year--;
        }
        $result = array_merge(['label' => $label, 'data' => $dataReport]);
        return $result;
    }

    public function countBriefCancelByQuarters()
    {
        $year = Carbon::now('Asia/Ho_Chi_Minh')->format('Y');
        $lastYear = $year - 1;
        $nowQ1 = $this->model
            ->query()
            ->selectRaw("'Quý 1' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-01-01' . "' and '" . $year . '-01-31' . "'")
            ->where('status', 5);
        $nowQ2 = $this->model
            ->query()
            ->selectRaw("'Quý 2' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-03-01' . "' and '" . $year . '-06-30' . "'")
            ->where('status', 5);
        $nowQ3 = $this->model
            ->query()
            ->selectRaw("'Quý 3' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-07-01' . "' and '" . $year . '-09-30' . "'")
            ->where('status', 5);
        $nowQ4 = $this->model
            ->query()
            ->selectRaw("'Quý 4' as type, count(id) as count, $year as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $year . '-10-01' . "' and '" . $year . '-12-31' . "'")
            ->where('status', 5);
        $lastQ1 = $this->model
            ->query()
            ->selectRaw("'Quý 1' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-01-01' . "' and '" . $lastYear . '-01-31' . "'")
            ->where('status', 5);
        $lastQ2 = $this->model
            ->query()
            ->selectRaw("'Quý 2' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-03-01' . "' and '" . $lastYear . '-06-30' . "'")
            ->where('status', 5);
        $lastQ3 = $this->model
            ->query()
            ->selectRaw("'Quý 3' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-07-01' . "' and '" . $lastYear . '-09-30' . "'")
            ->where('status', 5);
        $lastQ4 = $this->model
            ->query()
            ->selectRaw("'Quý 4' as type, count(id) as count, $lastYear as year")
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $lastYear . '-10-01' . "' and '" . $lastYear . '-12-31' . "'")
            ->where('status', 5);

        $data = $nowQ1
            ->union($nowQ2)
            ->unionAll($nowQ3)
            ->unionAll($nowQ4)
            ->unionAll($lastQ1)
            ->unionAll($lastQ2)
            ->unionAll($lastQ3)
            ->unionAll($lastQ4)
            ->get()->toArray();

        $label = ['Quý 1', 'Quý 2', 'Quý 3', 'Quý 4'];
        $dataReport = [];
        $stt = 0;
        while ($year >= $lastYear) {
            $filter = array_filter($data, function ($var) use ($year) {
                return ($var['year'] == $year);
            });
            $dataReport[$stt]['label'] = $year;
            $dataReport[$stt]['count'] = Arr::pluck($filter, 'count');
            $stt++;
            $year--;
        }
        $result = array_merge(['label' => $label, 'data' => $dataReport]);
        return $result;
    }

    public function totalBriefBranchRevenue()
    {
        $data = $this->model->query()
            ->selectRaw("branch_id, branch_name, sum(service_fee) as total")
            ->where('status', 4)
            ->groupBy(['branch_id', 'branch_name'])
            ->get();

        $result = array('label' => Arr::pluck($data, 'branch_name'), 'data' => Arr::pluck($data, 'total'), 'branch_id' => Arr::pluck($data, 'branch_id'));
        return $result;
    }

    public function totalBriefBranchDebt()
    {
        $data = $this->model->query()
            ->selectRaw("branch_id, branch_name, sum(service_fee) as total")
            ->whereIn('status', [2, 3])
            ->groupBy(['branch_id', 'branch_name'])
            ->get();

        $result = array('label' => Arr::pluck($data, 'branch_name'), 'data' => Arr::pluck($data, 'total'), 'branch_id' => Arr::pluck($data, 'branch_id'));
        return $result;
    }

    public function countBriefFinishByMonth()
    {
        $this->model->refresh();

        $toYear = Carbon::now()->year;
        $fromYear = $toYear - 1;

        $fromDate = '1/1/' . $fromYear;
        $toDate = '31/12/' . $toYear;

        $status = [4];
        $year = [$fromYear, $toYear];
        $statusOutput = ['old', 'new'];

        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
        } else {
            return ['message' => 'Lỗi, không xác định được khoảng thời gian cần tìm', 'exception' => ''];
        }

        $date = $fromDate;
        $monthList = [];
        $stt = 0;
        while ($stt < 12) {
            $monthList[$stt]['month'] = $date->month;
            $monthList[$stt]['label'] = 'Tháng ' . $date->month;
            $date = $date->addMonth(1);
            $stt++;
        }

        $fromDate = '1/1/' . $fromYear;
        $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);

        $monthPluck = Arr::pluck($monthList, 'month');
        $label = Arr::pluck($monthList, 'label');

        $stt = 0;
        $dataRaw = $this->model
            ->select([
                DB::raw("count(id)"),
                'status',
                DB::raw("date_part('month', status_updated_at) as month"),
                DB::raw("date_part('year', status_updated_at) as year"),
            ])
            ->whereRaw("to_char(status_updated_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'")
            ->whereIn('status', $status)
            ->groupBy(['status_text', 'status', 'month', 'year'])
            ->orderBy('month')
            ->orderBy('year')
            ->orderBy('status')
            ->get()->toArray();
        $data = [];
        $stt = 0;
        foreach ($monthPluck as $month) {
            foreach ($year as $item) {
                $filter = array_filter($dataRaw, function ($value) use ($month, $item, $status) {
                    return $value['month'] == $month and $value['year'] == $item and $value['status'] == $status[0];
                });
                if (empty($filter)) {
                    $addData = ['count' => 0, 'status' => $status[0], 'month' => $month, 'year' => $item];
                    $data[$item][$stt] = $addData;
                } else {
                    foreach ($filter as $fil) {
                        $data[$item][$stt] =  $fil;
                    }
                }
                $stt++;
            }
        }

        $result = [];
        $stt = 0;
        foreach ($year as $item) {
            $indexOfItem = array_search($item, $year);

            $count = Arr::pluck($data[$item], 'count');
            $result[$stt]['label'] = $item;
            $result[$stt]['count'] = $count;
            $result[$stt]['status'] = $statusOutput[$indexOfItem];

            $stt++;
        }
        $result = array_merge(['label' => $label], ['data' => $result]);

        $database = $this->model->getTable();
        return $result;
    }

    public function countBriefCancelByMonth()
    {
        $this->model->refresh();

        $toYear = Carbon::now()->year;
        $fromYear = $toYear - 1;

        $fromDate = '1/1/' . $fromYear;
        $toDate = '31/12/' . $toYear;

        $status = [5];
        $year = [$fromYear, $toYear];
        $statusOutput = ['old', 'new'];

        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
        } else {
            return ['message' => 'Lỗi, không xác định được khoảng thời gian cần tìm', 'exception' => ''];
        }

        $date = $fromDate;
        $monthList = [];
        $stt = 0;
        while ($stt < 12) {
            $monthList[$stt]['month'] = $date->month;
            $monthList[$stt]['label'] = 'Tháng ' . $date->month;
            $date = $date->addMonth(1);
            $stt++;
        }

        $fromDate = '1/1/' . $fromYear;
        $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);

        $monthPluck = Arr::pluck($monthList, 'month');
        $label = Arr::pluck($monthList, 'label');

        $stt = 0;
        $dataRaw = $this->model
            ->select([
                DB::raw("count(id)"),
                'status',
                DB::raw("date_part('month', created_at) as month"),
                DB::raw("date_part('year', created_at) as year"),
            ])
            ->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'")
            ->whereIn('status', $status)
            ->groupBy(['status_text', 'status', 'month', 'year'])
            ->orderBy('month')
            ->orderBy('year')
            ->orderBy('status')
            ->get()->toArray();

        $data = [];
        $stt = 0;
        foreach ($monthPluck as $month) {
            foreach ($year as $item) {
                $filter = array_filter($dataRaw, function ($value) use ($month, $item, $status) {
                    return $value['month'] == $month and $value['year'] == $item and $value['status'] == $status[0];
                });
                if (empty($filter)) {
                    $addData = ['count' => 0, 'status' => $status[0], 'month' => $month, 'year' => $item];
                    $data[$item][$stt] = $addData;
                } else {
                    foreach ($filter as $fil) {
                        $data[$item][$stt] =  $fil;
                    }
                }
                $stt++;
            }
        }

        $result = [];
        $stt = 0;
        foreach ($year as $item) {
            $indexOfItem = array_search($item, $year);

            $count = Arr::pluck($data[$item], 'count');
            $result[$stt]['label'] = $item;
            $result[$stt]['count'] = $count;
            $result[$stt]['status'] = $statusOutput[$indexOfItem];

            $stt++;
        }
        $result = array_merge(['label' => $label], ['data' => $result]);
        return $result;
    }

    private function getStatusText(int $status)
    {
        switch ($status) {
            case 1:
                return 'Mới';
                break;
            case 2:
                return 'Đang thẩm định';
                break;
            case 3:
                return 'Đang duyệt';
                break;
            case 4:
                return 'Hoàn thành';
                break;
            case 6:
                return 'Đang kiểm soát';
                break;
            default:
                return 'Hủy';
        }
    }
}
