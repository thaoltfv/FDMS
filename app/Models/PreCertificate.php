<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\CommonService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificate extends Model
{
    use SoftDeletes;

    protected $table = 'preCertificates';
    //protected $dateFormat = 'd-m-Y H:i:s';
    protected $casts = [
        'id' => 'integer',
        'appraiser_sale_id' => 'integer',
        'business_manager_id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',

        'petitioner_name',
        'petitioner_phone',
        'petitioner_address',
        'petitioner_identity_card',

        // đối tác
        'customer_id',

        // Loại sơ bộ
        'pre_status_code',
        'status_updated_at'

        'appraise_purpose_id',
        'note',

        // nhân viên kinh doanh
        'appraiser_sale_id',

        //quản lý nghiệp vụ (new)
        'business_manager_id',

        // chuyên viên thực hiện
        'appraiser_performance_id',

        // tài liệu đính kèm
        // 'pre_certificate_file',    

        // tổng giá trị sơ bộ
        'total_preliminary_value',
        // 'pre_result_file'
        
        // Lý do hủy sơ bộ
        'cancel_reason',
    ];

    public function getStatusTextAttribute()
    {
		$status = $this->pre_status_code;
		$statusText = "";
		switch ($status) {
            case 1:
                $statusText = "Yêu cầu sơ bộ";
                break;
			case 2:
				$statusText = "Định giá sơ bộ";
			    break;
			case 3:
				$statusText = "Duyệt giá sơ bộ";
			    break;
			case 4:
				$statusText = "Thương thảo";
			    break;
			case 5:
				$statusText = "Huỷ";
			    break;
            case 6:
                return 'Hoàn Thành';
                break;
		}
        return $statusText;
    }
    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'id');
    }

    public function appraiserBusinessManager(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'business_manager_id', 'id');
    }

	public function appraiserSale(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_sale_id', 'id');
    }

	public function appraiserPerform(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_perform_id', 'id');
    }

    public function appraisePurpose(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_purpose_id');
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

	public function otherDocuments(): HasMany
    {
        return $this->hasMany(PreCertificateOtherDocuments::class, 'certificate_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }


}
