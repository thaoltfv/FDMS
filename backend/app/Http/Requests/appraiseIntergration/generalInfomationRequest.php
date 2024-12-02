<?php

namespace App\Http\Requests\appraiseIntergration;

use Illuminate\Foundation\Http\FormRequest;

class generalInfomationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST': {
                    return [
                        'asset_type_id' => 'required',
                        'province_id' => 'required',
                        'district_id' => 'required',
                        'ward_id' => 'required',
                        'street_id' => 'required',
                        'coordinates' => 'required',
                        'appraise_asset' => 'required'
                    ];
                }
            case 'PUT':
            case 'PATCH': {
                    return [];
                }
            default:
                return [];
        }
    }
    public function messages()
    {
        return [
            'asset_type_id.required' => 'Vui lòng chọn loại TS.',
            'coordinates.required' => 'Vui lòng chọn toạ độ.',
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'ward_id.required' => 'Vui lòng chọn Phường/Xã.',
            'street_id.required' => 'Vui lòng chọn Đường.',
            'appraise_asset.required' => 'Vui lòng nhập tên TS'

        ];

    }
}
