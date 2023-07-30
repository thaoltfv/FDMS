<?php

namespace App\Services\Document\Appendix2;

use App\Services\CommonService;
use App\Services\Document\DocumentInterface\Report;
use Illuminate\Support\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\SimpleType\JcTable;

class ReportAppendix2Nova extends ReportAppendix2
{
    public function printTitle(Section $section, $data)
    {
        $this->setProperties($data);
        $section->addImage($this->logoUrl, $this->styleImageLogo);
        $section->addText('PHỤ LỤC 2 KÈM THEO BÁO CÁO THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);
        $section->addText('CÔNG TRÌNH XÂY DỰNG', ['italic' => true, 'size' => 14], ['align' => 'center']);
    }
    protected function printOriginalPriceDescription($section, $dgxdSlug)
    {
        $section->addText('❖ Về nguyên giá của công trình xây dựng:', ['bold' => true, 'size' => 13], ['align' => 'left']);
        $textRun = $section->addTextRun();
        $textRun->addText('Căn cứ vào Quyết định 22/2019/QĐ-UBND ngày 30/08/2019 của UBND TP Hồ Chí Minh về Ban hành bảng giá nhà ở, công trình, vật kiến trúc xây dựng mới trên địa bàn thành phố Hồ Chí Minh, công văn số 2189/SXD-KTXD ngày 22 tháng 02 năm 2021 và công văn số 4381/SXD-KTXD ngày 27/04/2022 Về việc điều chỉnh, quy đổi về thời điểm tính toán đối với Bảng giá nhà ở, công trình, vật kiến trúc xây dựng mới trên địa bàn Thành phố Hồ Chí Minh');
        if ($dgxdSlug === 'dg-uoc-tinh') {
            $textRun->addText(' và căn cứ vào tình hình giá thị trường xây dựng nhà ở trên địa bàn TP.HCM. ' . $this->acronym . ' đề xuất đơn giá xây mới cho CTXD của BĐS thẩm định giá (giá CTXD đã bao gồm yếu tố lợi nhuận của nhà đầu tư)');
        }
    }
    protected function printBuildingComapanyInfo($section, $tangibleAssets, $dgxdSlug)
    {
        if ($dgxdSlug === 'dg-uoc-tinh') {
            $section->addText('BẢNG TỔNG HỢP THÔNG TIN TSTĐG VÀ TSSS', null, ['align' => 'center']);
        }
        $section->addText('Đvt: đ/' . $this->m2, ['italic' => true], ['align' => 'right', 'keepNext' => true]);
        $table = $section->addTable($this->styleTable);
        $this->printBuildingPriceDetail($table, $tangibleAssets, $dgxdSlug);
    }

    protected function printBuildingPriceDetail($table, $tangibleAssets, $dgxdSlug)
    {
        $constructionCompany = $tangibleAssets[0]->constructionCompany;
        $com1 = count($constructionCompany) && $constructionCompany[0] ? $constructionCompany[0]->name : '';
        $com2 = count($constructionCompany) && $constructionCompany[1] ? $constructionCompany[1]->name : '';
        $com3 = count($constructionCompany) && $constructionCompany[2] ? $constructionCompany[2]->name : '';
        $table->addRow(400, $this->rowHeader);
        if ($dgxdSlug === 'dg-uoc-tinh') {
            $table->addCell(2500, $this->cellRowSpan)->addText('Tên tài sản', ['bold' => true], $this->cellHCentered);
            $table->addCell(500, $this->cellRowSpan)->addText('Đvt', ['bold' => true], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText($com1, ['bold' => true], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText($com2, ['bold' => true], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText($com3, ['bold' => true], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText('Đơn giá trung bình', ['bold' => true], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText('Đơn giá quyết định', ['bold' => true], $this->cellHCentered);
        } else {
            $table->addCell(6000, $this->cellRowSpan)->addText('Tên tài sản', ['bold' => true], $this->cellHCentered);
            $table->addCell(2000, $this->cellRowSpan)->addText('Đvt', ['bold' => true], $this->cellHCentered);
            $table->addCell(2500, $this->cellRowSpan)->addText('Đơn giá', ['bold' => true], $this->cellHCentered);
        }
        foreach ($tangibleAssets as $tangibleAsset) {
            $name = $tangibleAsset->tangible_name;
            $startUsingYear = $tangibleAsset->start_using_year;
            $unitPrice1 = isset($tangibleAsset->constructionCompany[0]) ? $tangibleAsset->constructionCompany[0]->unit_price_m2 : 0;
            $unitPrice2 = isset($tangibleAsset->constructionCompany[1]) ? $tangibleAsset->constructionCompany[1]->unit_price_m2 : 0;
            $unitPrice3 = isset($tangibleAsset->constructionCompany[2]) ? $tangibleAsset->constructionCompany[2]->unit_price_m2 : 0;
            $dgqd = isset($tangibleAsset->total_desicion_average) ? $tangibleAsset->total_desicion_average : 0;
            $unitPrice = round(($unitPrice1 + $unitPrice2 + $unitPrice3) / 3, -4, PHP_ROUND_HALF_DOWN);
            $table->addRow();
            if ($dgxdSlug == 'dg-uoc-tinh') {
                $table->addCell(2500, $this->cellRowSpan)->addText(CommonService::mbUcfirst($name), ['bold' => true], $this->cellHCentered);
                $table->addCell(500, $this->cellRowSpan)->addText($this->m2, ['bold' => true], $this->cellHCentered);
                $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice1, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
                $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice2, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
                $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice3, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
                if ($unitPrice) {
                    $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
                } else {
                    $table->addCell(1500, $this->cellRowSpan)->addText("Không biết", ['bold' => false], $this->cellHCentered);
                }
                $table->addCell(1500, $this->cellRowSpan)->addText(number_format($dgqd, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
            } else {
                $table->addCell(6000, $this->cellRowSpan)->addText(CommonService::mbUcfirst($name), ['bold' => true], $this->cellHCentered);
                $table->addCell(2000, $this->cellRowSpan)->addText($this->m2, ['bold' => true], $this->cellHCentered);
                $table->addCell(2500, $this->cellRowSpan)->addText(number_format($dgqd, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
            }
            $this->total[$tangibleAsset->id]['name'] = $name;
            $this->total[$tangibleAsset->id]['start_using_year'] = $startUsingYear;
        }
    }
    protected function printBuildingPriceChoosed($section, $dgxdSlug)
    {
        $description = '';
        if ($dgxdSlug === 'dg-uoc-tinh') {
            $description = $this->companyName . ' đề xuất đơn giá xây dựng mới cho TSTĐ theo đơn giá trung bình của các công ty xây dựng đang cung cấp trên thị trường.';
            $textRun = $section->addTextRun();
            $textRun->addText('Kết luận: ', $this->styleBold, null);
            $textRun->addText($description, null, null);
        }
    }

    protected function printRemainQualityDescription($section)
    {
        $section->addText('❖ Chất lượng còn lại công trình xây dựng: ', ['bold' => true, 'size' => 13], ['align' => 'left']);
        $textRun = $section->addTextRun();
        $textRun->addText('- Căn cứ theo biên bản kiểm kê và kết quả khảo sát hiện trạng. ' . $this->acronym . ' đánh giá chất lượng còn lại của công trình xây dựng như sau:');
        $section->addText('     Căn cứ Tiêu chuẩn thẩm định giá Việt Nam số 09, tỷ lệ hao mòn của công trình xây dựng được ước tính bằng phương pháp tổng cộng theo công thức:');
        $table = $section->addTable();
        $table->addRow();
        $c0 = $table->addCell(2200);
        $c0->addText('    Tổng giá trị hao mòn',null,['align' => 'center']);
        $c01 = $table->addCell(200);
        $c01->addText('=',null,['align' => 'center']);
        $c02 = $table->addCell(2200);
        $c02->addText('Giá trị hao mòn vật lý',null,['align' => 'center']);
        $c03 = $table->addCell(200);
        $c03->addText('+',null,['align' => 'center']);
        $c04 = $table->addCell(2200);
        $c04->addText('Giá trị hao mòn chức năng',null,['align' => 'center']);
        $c05 = $table->addCell(200);
        $c05->addText('+');
        $c06 = $table->addCell(2200);
        $c06->addText('Giá trị hao mòn ngoại biên',null,['align' => 'center']);
        $this->printNew1($section);
    }

    protected function printNew1($section){
        $section->addText('     Tổ thẩm định nhận định:');
        $section->addText('     ❖    Tài sản thẩm định giá chịu hao mòn vật lý và có thể sử dụng phương pháp chuyên gia để ước tính giá trị hao mòn vật lý của tài sản');
        $section->addText('     ❖    Tổ thẩm định giá ước tính giá trị của tài sản thẩm định giá bằng phương pháp chi phí thay thế, do đó, hao mòn chức năng của tài sản được xác định bằng 0. ');
        $section->addText('     ❖    Tài sản thẩm định giá không chịu hao mòn ngoại biên.');
        $section->addText('     Căn cứ hướng dẫn tại Tiêu chuẩn thẩm định giá Việt Nam số 09, tỷ lệ hao mòn vật lý của công trình xây dựng được ước tính bằng phương pháp chuyên gia theo công thức:');
        $section->addImage('~/public/uploads/img/congthuc-PL2.png', array(
            'width' => 300,
            'align' => 'center',
            'space' => [
            'line' => 1000,
            'rule' => 'single',
        ],));
        $section->addText('     Tham khảo Công văn số 1326/BXD-QLN ngày 08/08/2011 của Bộ Xây dựng về việc Hướng dẫn kiểm kê, đánh giá lại giá trị tài sản cố định là nhà cửa, vật kiến trúc, tỷ trọng các kết cấu chính đối với công trình xây dựng tương tự tài sản thẩm định giá như sau:');

    }
}
