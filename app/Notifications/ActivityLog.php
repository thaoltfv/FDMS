<?php

namespace App\Notifications;

use App\Models\Appraise;
use App\Models\Certificate;
use App\Services\CommonService;

trait ActivityLog
{
    private function CustomizeLogMessage($model, $activity, $log) {
        if ($activity == 'update_status') {
            // $model == Certificate::class && -> đoạn này gây ra lỗi log
            // if($model == Certificate::class && isset($model->status)) {
            if(isset($model->status)) {
                switch ($model->status) {
                    case 2:
                         $log = $log  . ' đang thẩm định';
                        break;
                    case 3:
                         $log =  $log . ' đang duyệt';
                        break;
                    case 4:
                         $log =  $log . ' đã hoàn thành';
                        break;
                    case 5:
                         $log =  $log . ' đã hủy';
                        break;
                    default:
                         $log =  $log . ' mới';
                }
            }

            // Tài sản thẩm định
            if($model == Appraise::class && isset($model->status)) {
                // switch ($model->status) {
                //     case 2:
                //         $log + ' đang thẩm định';
                //         break;
                //     case 3:
                //         $log + ' đang duyệt';
                //         break;
                //     case 4:
                //         $log + ' đã hoàn thành';
                //         break;
                //     case 5:
                //         $log + ' đã hủy';
                //         break;
                //     default:
                //         $log + ' mới';
                // }
            }
        }
        return $log;
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
}
