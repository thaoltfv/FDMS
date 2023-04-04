<?php


namespace App\Enum;


class CompareMaterData
{
    public const TRADE_TYPE =[
        1=>'ĐANG RAO BÁN',
        2=>'ĐÃ BÁN',
        3=>'ĐANG RAO CHO THUÊ',
        4=>'ĐÃ CHO THUÊ',
    ];
    public const BUILDING_CATEGORY =[
        1=>'1',
        2=>'2',
        3=>'3',
        4=>'4',
        5=>'<4',

    ];
    public const BUILDING_TYPE =[
        1=>'NHÀ Ở RIÊNG LẺ',
        3=>'BIỆT THỰ',
        4=>'NHÀ XƯỞNG (KHO)',
    ];

    public const POSITION_TYPE =[
        1=>'VỊ TRÍ 1',
        2=>'VỊ TRÍ 2',
        3=>'VỊ TRÍ 3',
        4=>'VỊ TRÍ 4',
    ];

    public const STRUCTURE =[
        1=>'Trệt',
        2=>'Lầu',
    ];

    public const FACTORY_TYPE =[
        1=>'Tường gạch thu hồi mái ngói',
        2=>'Tường gạch thu hồi mái tôn',
        3=>'Tường gạch, bổ trụ, kèo thép, mái tôn',
        4=>'Tường gạch, mái bằng',
        5=>'Cột bê tông, kèo thép, tường gạch, mái tôn',
        6=>'Cột kèo bê tông, tường gạch, mái tôn',
        7=>'Cột kèo thép, tường gạch, mái tôn',
        8=>'Cột bê tông kèo thép, tường gạch, mái tôn',
        9=>'Cột kèo thép, tường bao che tôn, mái tôn',
        10=>'Cột bê tông, kèo thép liền nhịp, tường gạch, mái tôn',
        11=>'Cột kèo thép liền nhịp, tường gạch, mái tôn',
        12=>'Cột bê tông, kèo thép, mái tôn',
        13=>'Cột kèo thép liền nhịp, tường bao che bằng tôn, mái tôn',
    ];

    public const COMPARISONS_DESCRIPTION =[
        'kem_thuan_loi'=>'KÉM THUẬN LỢI HƠN',
        'thuan_loi'=>'THUẬN LỢI HƠN',
        'tuong_dong'=>'TƯƠNG ĐỒNG',
    ];

    public const CERTIFICATE_BRIEF_CHECK_DUPLICATE =[
        'document_num' => 'Số hợp đồng',
        'certificate_num' => 'Số chứng thư',
    ];

}
