<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherCertificateBrief extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'personal_property_id',
        'asset_type_id',
        'name',
        'description',
        'status',
    ];

    public function law():HasMany
    {
        return $this->hasMany(OtherCertificateBriefLaw::class,'other_asset_id','id');
    }

    public function assetType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'asset_type_id','id');
    }

    public function price():HasOne
    {
        return $this->hasOne(OtherCertificateBriefPrice::class, 'other_asset_id', 'id');
    }

    public function otherInfomation():HasOne
    {
        return $this->hasOne(OtherCertificateBriefLawInfo::class, 'other_asset_id', 'id');
    }

    public function getDocumentDescriptionAttribute()
    {
        return 'Các hồ sơ, tài liệu về tài sản do khách hàng cung cấp là đầy đủ và tin cậy';
    }

    public function getStatusTextAttribute()
    {
        $status = $this->status;
		$statusText = "";
		switch ($status) {
            case 1:
                $statusText = "Mới";
                break;
			case 2:
				$statusText = "Đang thực hiện";
			    break;
			case 3:
				$statusText = "Đang Duyệt";
			    break;
			case 4:
				$statusText = "Hoàn Thành";
			    break;
			case 5:
				$statusText = "Huỷ";
			    break;
		}
        return $statusText;
    }
}
