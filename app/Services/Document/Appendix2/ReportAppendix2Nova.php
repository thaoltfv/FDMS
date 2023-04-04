<?php

namespace App\Services\Document\Appendix2;

use App\Services\Document\DocumentInterface\Report;
use Illuminate\Support\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\SimpleType\JcTable;

class ReportAppendix2Nova extends ReportAppendix2
{
    protected function printOriginalPriceDescription($section)
    {
        $section->addText('❖ Về nguyên giá của nhà cửa, vật kiến trúc:', ['bold' => true, 'size' => 13], ['align' => 'left']);
        $textRun = $section->addTextRun();
        $textRun->addText('Căn cứ vào Quyết định 22/2019/QĐ-UBND ngày 30/08/2019 của UBND TP Hồ Chí Minh về Ban hành bảng giá nhà ở, công trình, vật kiến trúc xây dựng mới trên địa bàn thành phố Hồ Chí Minh, công văn số 2189/SXD-KTXD ngày 22 tháng 02 năm 2021 và công văn số 4381/SXD-KTXD ngày 27/04/2022 Về việc điều chỉnh, quy đổi về thời điểm tính toán đối với Bảng giá nhà ở, công trình, vật kiến trúc xây dựng mới trên địa bàn Thành phố Hồ Chí Minh và căn cứ vào tình hình giá thị trường xây dựng nhà ở trên địa bàn TP.HCM. ' . $this->acronym . ' đề xuất đơn giá xây mới cho CTXD của BĐS thẩm định giá (giá CTXD đã bao gồm yếu tố lợi nhuận của nhà đầu tư)');
    }
}
