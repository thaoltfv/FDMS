<?php

namespace App\Services;

use App\Models\ApartmentAssetVersion;
use App\Models\Appraise;
use App\Models\AppraiseVersion;
use App\Models\Certificate;
use App\Models\CertificateApartmentVersion;
use App\Models\CertificateAssetVersion;
use App\Models\RealEstate;
use Illuminate\Support\Facades\DB;

class AppraiseVersionService
{
    public static function getCertificateByRealEstateId(int $realEstateId)
    {
        $check = Certificate::query()->with('realEstate:id,real_estate_id')->whereHas('realEstate', function ($has) use ($realEstateId) {
            $has->where('certificate_real_estates.real_estate_id', $realEstateId);
        })->first(['id', 'status', 'sub_status']);
        return $check;
    }
    public static function checkVersionByRealEstate(int $realEstateId)
    {
        $check = false;
        $certificate = self::getCertificateByRealEstateId($realEstateId);
        if (isset($certificate)) {
            $certificateAppraise = $certificate->realEstate->where('real_estate_id', $realEstateId)->first();
            $certificateAssetVersion = CertificateAssetVersion::query()->where('appraise_id', $certificateAppraise->id)->max('version');
            $appraiseVersion = AppraiseVersion::query()->where('appraise_id', $realEstateId)->max('version');
            if (isset($appraiseVersion) && isset($certificateAssetVersion)) {
                if ($appraiseVersion == $certificateAssetVersion) {
                    $check = true;
                }
            }
        }
        return $check;
    }
    public static function checkVersionByCertificate(int $certificateId)
    {
        $listVersion = [];
        $ids = [];
        $realEstateIdList = [];
        $certificate = Certificate::query()->where('id', $certificateId)->with('realEstate')->first();
        if (!empty($certificate) && !empty($certificate->realEstate)) {
            foreach ($certificate->realEstate as $item) {
                $realEstateId = $item->real_estate_id;
                if (!empty($item->appraises)) {
                    $certificateRealEstateId = $item->appraises->id;
                    $version1 = self::getVersionAppraise($realEstateId);
                    $version2 = self::getVersionCertificateAsset($certificateRealEstateId);
                    // dd($version1, $version2, $certificateRealEstateId,$realEstateId);
                    if ($version1 != $version2) {
                        $ids[] = $realEstateId;
                        $realEstateIdList[]['id'] = $realEstateId;
                        $realEstateIdList[$realEstateId] = $item->id;
                    }
                }
                if (!empty($item->apartment)) {
                    $certificateRealEstateId = $item->apartment->id;
                    $version1 = self::getVersionApartment($realEstateId);
                    $version2 = self::getVersionCertificateApartment($certificateRealEstateId);
                    // dd($version1, $version2, $certificateRealEstateId,$realEstateId);
                    if ($version1 != $version2) {
                        $ids[] = $realEstateId;
                        $realEstateIdList[]['id'] = $realEstateId;
                        $realEstateIdList[$realEstateId] = $item->id;
                    }
                }
            }
            if (!empty($ids)) {
                $listVersion = self::getRealEstate($ids, $realEstateIdList);
            }
        }
        return $listVersion;
    }
    public static function getVersionAppraise($RealEstateId)
    {
        return AppraiseVersion::query()->where('appraise_id', $RealEstateId)->orderBy('updated_at', 'desc')->first()->version ?? 0;
    }

    public static function getVersionCertificateAsset($certificateRealEstateId)
    {
        return CertificateAssetVersion::query()->where('appraise_id', $certificateRealEstateId)->orderBy('version', 'desc')->first()->version ?? 0;
    }
    public static function getVersionApartment($RealEstateId)
    {
        return ApartmentAssetVersion::query()->where('apartment_asset_id', $RealEstateId)->orderBy('id', 'desc')->first()->version ?? 0;
    }
    public static function getVersionCertificateApartment($certificateRealEstateId)
    {
        return CertificateApartmentVersion::query()->where('apartment_asset_id', $certificateRealEstateId)->orderBy('version', 'desc')->first()->version ?? 0;
    }
    public static function getRealEstate($ids, $realEstateIdList)
    {
        $select = [
            'id',
            'created_at',
            'created_by',
            'asset_type_id',
            DB::raw("appraise_asset as name, total_area,
                coalesce(case
                    when  round_total > 0
                    then ceil(total_price / power(10, round_total)) * power(10, round_total)
                    when   round_total < 0
                        then floor( total_price * abs(power(10, round_total))  ) / abs(power(10, round_total))
                    else
                        total_price
                end, 0) as total_price
                "),
        ];
        $with = [
            'createdBy:id,name',
            'assetType:id,description,acronym',
        ];
        $result = RealEstate::query()->with($with)->whereIn('id', $ids)->get($select);
        foreach ($result as $item) {
            $item->real_estate_id = $realEstateIdList[$item->id];
            $item->append('last_version');
        }
        return $result;
    }
    public static function getVersionAppraises($ids)
    {
        return Appraise::query()->whereIn('id', $ids)->with('lastVerion')->get('id');
    }
}
