<?php

namespace App\Http\Requests\Asset;

use App\Http\Requests\FormRequest;

/**
 * @property integer id
 */
class CompareAssetGeneralRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'input_source',
            'asset_type_id',
            'status',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
            'distance_id',
            'full_address',
            'land_no',
            'doc_no',
            'coordinates',
            'source_id',
            'topographic',
            'public_date',
            'contact_person',
            'contact_phone',
            'transaction_type_id',
            'adjust_percent',
            'convert_fee_total',
            'total_area',
            'total_amount',
            'total_area_amount',
            'total_estimate_amount',
            'adjust_amount',
            'total_order_amount',
            'total_land_unit_price',
            'total_construction_area',
            'total_construction_amount',
            'average_land_unit_price',
            'total_raw_amount',
            'max_value_description',
        ];
    }
}
