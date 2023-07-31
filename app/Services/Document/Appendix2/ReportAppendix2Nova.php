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
    }

    protected function printNew1($section, $tangibleAssets){
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
        $imgName = env('STORAGE_IMAGES','images') .'/'.'congthuc-PL2.png';
        $section->addText('     Tổ thẩm định nhận định:');
        $section->addText('     ❖    Tài sản thẩm định giá chịu hao mòn vật lý và có thể sử dụng phương pháp chuyên gia để ước tính giá trị hao mòn vật lý của tài sản');
        $section->addText('     ❖    Tổ thẩm định giá ước tính giá trị của tài sản thẩm định giá bằng phương pháp chi phí thay thế, do đó, hao mòn chức năng của tài sản được xác định bằng 0. ');
        $section->addText('     ❖    Tài sản thẩm định giá không chịu hao mòn ngoại biên.');
        $section->addText('     Căn cứ hướng dẫn tại Tiêu chuẩn thẩm định giá Việt Nam số 09, tỷ lệ hao mòn vật lý của công trình xây dựng được ước tính bằng phương pháp chuyên gia theo công thức:');
        $section->addImage(storage_path('app/public/'.$imgName), array(
            'width' => 400,
            'align' => 'center',
            'space' => [
            'line' => 1000,
            'rule' => 'single',
        ],));
        $section->addText('     Tham khảo Công văn số 1326/BXD-QLN ngày 08/08/2011 của Bộ Xây dựng về việc Hướng dẫn kiểm kê, đánh giá lại giá trị tài sản cố định là nhà cửa, vật kiến trúc, tỷ trọng các kết cấu chính đối với công trình xây dựng tương tự tài sản thẩm định giá như sau:');
        $table1 = $section->addTable($this->styleTable);
        $table1->addRow(400, $this->rowHeader);
        $table1->addCell(3000, $this->cellRowSpan)->addText('Loại nhà', ['bold' => true], $this->cellHCenteredKeepNext);
        $table1->addCell(7500, ['gridSpan' => 5, 'valign' => 'center'])->addText('Tỷ lệ giá trị các kết cấu chính (%)', ['bold' => true], $this->cellHCenteredKeepNext);
        $table1->addRow();
        $table1->addCell(1500, $this->cellRowContinue)->addText(null, null, ['keepNext' => true]);
        $table1->addCell(1500, ['valign' => 'center'])->addText('Móng, khung cột', ['bold' => true], $this->cellHCenteredKeepNext);
        $table1->addCell(1500, ['valign' => 'center'])->addText('Tường', ['bold' => true], $this->cellHCenteredKeepNext);
        $table1->addCell(1500, ['valign' => 'center'])->addText('Nền, sàn', ['bold' => true], $this->cellHCenteredKeepNext);
        $table1->addCell(1500, ['valign' => 'center'])->addText('Kết cấu đỡ mái', ['bold' => true], $this->cellHCenteredKeepNext);
        $table1->addCell(1500, ['valign' => 'center'])->addText('Mái', ['bold' => true], $this->cellHCenteredKeepNext);

        $stt = 1;
        $countTangible = count($tangibleAssets);
        foreach ($tangibleAssets as $tangibleAsset) {
            $p1 = $tangibleAsset->comparisonTangibleFactor->p1 ?? 0;
            $p2 = $tangibleAsset->comparisonTangibleFactor->p2 ?? 0;
            $p3 = $tangibleAsset->comparisonTangibleFactor->p3 ?? 0;
            $d4 = $tangibleAsset->comparisonTangibleFactor->d4 ?? 0;
            $p5 = $tangibleAsset->comparisonTangibleFactor->p5 ?? 0;
            $table1->addRow(400, $this->cantSplit);
            $table1->addCell(3000, $this->cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table1->addCell(1500, $this->cellVCentered)->addText($p1 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table1->addCell(1500, $this->cellVCentered)->addText($p2 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table1->addCell(1500, $this->cellVCentered)->addText($p3 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table1->addCell(1500, $this->cellVCentered)->addText($d4 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table1->addCell(1500, $this->cellVCentered)->addText($p5 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $stt++;
        }

        $section->addText('     Căn cứ thực trạng công trình xây dựng để xác định tỷ lệ chất lượng còn lại của các kết cấu chính của nhà theo TCXDVN 373:2006 “chỉ dẫn đánh giá mức độ nguy hiểm của kết cấu nhà” và Thông tư 13/LB-TT ngày 18/08/1994 về: Hướng dẫn phương pháp xác định “giá trị còn lại các kết cấu chính” như sau:');
        $section->addTextBreak(1);
        

        
        $countTangible = count($tangibleAssets);
        foreach ($tangibleAssets as $tangibleAsset) {
            $section->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name).':',['bold' => true]);
            $table2 = $section->addTable($this->styleTable);
            $table2->addRow(400, $this->rowHeader);
            $table2->addCell(500, $this->cellRowSpan)->addText('STT', ['bold' => true], $this->cellHCenteredKeepNext);
            $table2->addCell(3000, $this->cellRowSpan)->addText('Hạng mục', ['bold' => true], $this->cellHCenteredKeepNext);
            $table2->addCell(5500, $this->cellRowSpan)->addText('Hiện trạng', ['bold' => true], $this->cellHCenteredKeepNext);
            $table2->addCell(1500, $this->cellRowSpan)->addText('Tỷ lệ CLCL (%)', ['bold' => true], $this->cellHCenteredKeepNext);
            $h1 = $tangibleAsset->comparisonTangibleFactor->h1 ?? 0;
            $h2 = $tangibleAsset->comparisonTangibleFactor->h2 ?? 0;
            $h3 = $tangibleAsset->comparisonTangibleFactor->h3 ?? 0;
            $h4 = $tangibleAsset->comparisonTangibleFactor->h4 ?? 0;
            $h5 = $tangibleAsset->comparisonTangibleFactor->h5 ?? 0;
            $note1 = json_decode($tangibleAsset->comparisonTangibleFactor->note)->note1 ?? 'Không có mô tả chi tiết';
            $note2 = json_decode($tangibleAsset->comparisonTangibleFactor->note)->note2 ?? 'Không có mô tả chi tiết';
            $note3 = json_decode($tangibleAsset->comparisonTangibleFactor->note)->note3 ?? 'Không có mô tả chi tiết';
            $note4 = json_decode($tangibleAsset->comparisonTangibleFactor->note)->note4 ?? 'Không có mô tả chi tiết';
            $note5 = json_decode($tangibleAsset->comparisonTangibleFactor->note)->note5 ?? 'Không có mô tả chi tiết';
            $table2->addRow(400, $this->cantSplit);
            $table2->addCell(500, $this->cellRowSpan)->addText('1', null, $this->cellHCenteredKeepNext);   
            $table2->addCell(3000, ['valign' => 'center'])->addText('Móng, khung cột', null, $this->cellHLeftKeepNext); 
            $table2->addCell(5500, ['valign' => 'center'])->addText($note1, null, $this->cellHLeftKeepNext);        
            $table2->addCell(1500, $this->cellVCentered)->addText($h1 . '%', null, $this->cellHCenteredKeepNext);
            $table2->addRow(400, $this->cantSplit);
            $table2->addCell(500, $this->cellRowSpan)->addText('2', null, $this->cellHCenteredKeepNext);   
            $table2->addCell(3000, ['valign' => 'center'])->addText('Tường', null, $this->cellHLeftKeepNext); 
            $table2->addCell(5500, ['valign' => 'center'])->addText($note2, null, $this->cellHLeftKeepNext);        
            $table2->addCell(1500, $this->cellVCentered)->addText($h2 . '%', null, $this->cellHCenteredKeepNext);
            $table2->addRow(400, $this->cantSplit);
            $table2->addCell(500, $this->cellRowSpan)->addText('3', null, $this->cellHCenteredKeepNext);   
            $table2->addCell(3000, ['valign' => 'center'])->addText('Nền, sàn', null, $this->cellHLeftKeepNext); 
            $table2->addCell(5500, ['valign' => 'center'])->addText($note3, null, $this->cellHLeftKeepNext);        
            $table2->addCell(1500, $this->cellVCentered)->addText($h3 . '%', null, $this->cellHCenteredKeepNext);
            $table2->addRow(400, $this->cantSplit);
            $table2->addCell(500, $this->cellRowSpan)->addText('4', null, $this->cellHCenteredKeepNext);   
            $table2->addCell(3000, ['valign' => 'center'])->addText('Kết cấu đỡ mái', null, $this->cellHLeftKeepNext); 
            $table2->addCell(5500, ['valign' => 'center'])->addText($note4, null, $this->cellHLeftKeepNext);        
            $table2->addCell(1500, $this->cellVCentered)->addText($h4 . '%', null, $this->cellHCenteredKeepNext);
            $table2->addRow(400, $this->cantSplit);
            $table2->addCell(500, $this->cellRowSpan)->addText('5', null, $this->cellHCenteredKeepNext);   
            $table2->addCell(3000, ['valign' => 'center'])->addText('Mái', null, $this->cellHLeftKeepNext); 
            $table2->addCell(5500, ['valign' => 'center'])->addText($note5, null, $this->cellHLeftKeepNext);        
            $table2->addCell(1500, $this->cellVCentered)->addText($h5 . '%', null, $this->cellHCentered);
            $section->addTextBreak(1);
        }

        $section->addText('     Trên cơ sở đó, tỷ lệ hao mòn vật lý, chất lượng còn lại của tài sản ước tính theo phương pháp chuyên gia là:');

        
    }

    protected $cellHLeftKeepNext = array('align' => 'left', 'keepNext' => true);

    protected function printNew2($section, $realEstate){
        $section->addText('     ❖ Giá trị công trình xây dựng:', ['bold' => true]);
        $section->addText('     Căn cứ Tiêu chuẩn Thẩm định giá VN số 09, chi phí thay thế công trình xây dựng ước tính bằng phương pháp chi phí thay thế theo công thức:');
        
        $table3 = $section->addTable();
        $table3->addRow();
        $c0 = $table3->addCell(3000);
        $c0->addText('    Chi phí thay thế',['italic' => true],['align' => 'center']);
        $c01 = $table3->addCell(500);
        $c01->addText('=',null,['align' => 'center']);
        $c02 = $table3->addCell(3000);
        $c02->addText('Đơn giá xây dựng công trình tương tự',['italic' => true],['align' => 'center']);
        $c03 = $table3->addCell(500);
        $c03->addText('X',null,['align' => 'center']);
        $c04 = $table3->addCell(3000);
        $c04->addText('Diện tích sàn xây dựng',['italic' => true],['align' => 'center']);
        
        $section->addText('     Căn cứ Tiêu chuẩn Thẩm định giá VN số 09, giá trị hao mòn công trình xây dựng ước tính bằng phương pháp chi phí thay thế theo công thức:');
        
        $table4 = $section->addTable();
        $table4->addRow();
        $c0 = $table4->addCell(3000);
        $c0->addText('    Tổng giá trị hao mòn',['italic' => true],['align' => 'center']);
        $c01 = $table4->addCell(500);
        $c01->addText('=',null,['align' => 'center']);
        $c02 = $table4->addCell(3000);
        $c02->addText('Chi phí thay thế',['italic' => true],['align' => 'center']);
        $c03 = $table4->addCell(500);
        $c03->addText('X',null,['align' => 'center']);
        $c04 = $table4->addCell(3000);
        $c04->addText('Tỷ lệ hao mòn',['italic' => true],['align' => 'center']);

        $section->addText('     Căn cứ Tiêu chuẩn Thẩm định giá Việt Nam số 09, giá trị công trình xây dựng ước tính bằng phương pháp chi phí thay thế theo công thức:');

        $table5 = $section->addTable();
        $table5->addRow();
        $c0 = $table5->addCell(3000);
        $c0->addText('    Giá trị ước tính của Công trình xây dựng',['italic' => true],['align' => 'center']);
        $c01 = $table5->addCell(500);
        $c01->addText('=',null,['align' => 'center']);
        $c02 = $table5->addCell(3000);
        $c02->addText('Chi phí thay thế',['italic' => true],['align' => 'center']);
        $c03 = $table5->addCell(500);
        $c03->addText('-',null,['align' => 'center']);
        $c04 = $table5->addCell(3000);
        $c04->addText('Tổng giá trị hao mòn (không bao gồm hao mòn chức năng)',['italic' => true],['align' => 'center']);

        $appraise = $realEstate->appraises;
        $tangibleAssetTotal = CommonService::getCertificateAssetPrice($appraise, 'tangible_asset_price');
        $textRun = $section->addTextRun();
        $textRun->addText('Như vậy, giá trị công trình xây dựng là: ');
        $textRun->addText(number_format($tangibleAssetTotal, 0, ',', '.').' đồng',['bold' => true]);

    }

    protected function printRemainQualityFunc2($section, $tangibleAssets)
    {
        $section->addText('✔ Phương pháp 2: Phương pháp chuyên gia (PP2): ', ['bold' => true, 'size' => 13, 'italic' => true], ['align' => 'left', 'keepNext' => true]);     
        $this->printNew1($section, $tangibleAssets);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(1500, $this->cellRowSpan)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(7500, ['gridSpan' => 10, 'valign' => 'center'])->addText('Phần kết cấu chính', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('CLCL (%)', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addRow();
        $table->addCell(1500, $this->cellRowContinue)->addText(null, null, ['keepNext' => true]);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Móng, khung cột', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tường', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Nền, sàn', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Kết cấu đỡ mái', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Mái', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowContinue)->addText(null, null, ['keepNext' => true]);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(1500, $this->cellRowContinue)->addText(null, null, ['keepNext' => true]);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellVCentered)->addText('H= Σ ph / Σ p', null,  $this->cellHCenteredKeepNext);

        $stt = 1;
        $countTangible = count($tangibleAssets);
        foreach ($tangibleAssets as $tangibleAsset) {
            $p1 = $tangibleAsset->comparisonTangibleFactor->p1 ?? 0;
            $h1 = $tangibleAsset->comparisonTangibleFactor->h1 ?? 0;
            $p2 = $tangibleAsset->comparisonTangibleFactor->p2 ?? 0;
            $h2 = $tangibleAsset->comparisonTangibleFactor->h2 ?? 0;
            $p3 = $tangibleAsset->comparisonTangibleFactor->p3 ?? 0;
            $h3 = $tangibleAsset->comparisonTangibleFactor->h3 ?? 0;
            $d4 = $tangibleAsset->comparisonTangibleFactor->d4 ?? 0;
            $h4 = $tangibleAsset->comparisonTangibleFactor->h4 ?? 0;
            $p5 = $tangibleAsset->comparisonTangibleFactor->p5 ?? 0;
            $h5 = $tangibleAsset->comparisonTangibleFactor->h5 ?? 0;
            $table->addRow(400, $this->cantSplit);
            $table->addCell(1500, $this->cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p1 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h1 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p2 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h2 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p3 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h3 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($d4 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h4 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p5 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h5 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $clcl2 = ($p1 + $p2 + $p3 + $d4 + $p5) != 0 ? round(($p1 * $h1 + $p2 * $h2 + $p3 * $h3 + $d4 * $h4 + $p5 * $h5) / ($p1 + $p2 + $p3 + $d4 + $p5), 0).'%' : 0 .'%';
            $table->addCell(1500, $this->cellRowSpan)->addText($clcl2, null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $this->total[$tangibleAsset->id]['clcl2'] = $clcl2;
            $stt++;
        }
        
    }

    protected function printContentByRealEstate($section, $realEstate, $key)
    {
        $appraise = $realEstate->appraises;
        $tangibleAssets = $appraise->tangibleAssets;
        $this->total = [];
        if ($this->isOnlyAsset) {
            $textRun = $section->addTextRun();
            $textRun->addText('     ' . ($key + 1) . '. Tài sản thẩm định:', ['bold' => true, 'size' => 14]);
        } else {
            $textRun = $section->addTextRun('Heading2');
            $textRun->addText('Tài sản thẩm định ' . ($key + 1) . ': ', ['bold' => true, 'size' => 14]);
        }
        if ($appraise->assetType->description == "ĐẤT TRỐNG") {
            $textRun->addText('Tài sản thẩm định không có công trình xây dựng', ['size' => 13, 'bold' => false]);
            return;
        }
        $this->printAssetInfo($section, $realEstate->appraise_asset, $appraise->full_address);
        // $textRun->addText('Công trình xây dựng tọa lạc tại ' . ($appraise->ward ? $appraise->ward->name : ''), ['size' => 13, 'bold' => false]);
        // $textRun->addText(', ' . ($appraise->district ? $appraise->district->name : ''), ['size' => 13, 'bold' => false]);
        // $textRun->addText(', ' . ($appraise->province ? $appraise->province->name : ''), ['size' => 13, 'bold' => false]);

        $dgxdSlug = 'dg-uoc-tinh';
        $appraiseDgxd = $appraise->appraisal_dgxd;
        if (!empty($appraiseDgxd)) {
            $dgxdSlug = $appraiseDgxd->slug_value;
        }

        $this->printOriginalPriceDescription($section, $dgxdSlug);
        $this->printBuildingComapanyInfo($section, $tangibleAssets, $dgxdSlug);
        $this->printBuildingPriceChoosed($section, $dgxdSlug);
        $this->printRemainQualityDescription($section);
        $remainQualitySlug = 'trung-binh-cong';
        $appraisalCLCL = $appraise->appraisal_clcl;
        if (!empty($appraisalCLCL)) {
            $remainQualitySlug = $appraisalCLCL->slug_value;
        }
        if ($remainQualitySlug == 'trung-binh-cong' || $remainQualitySlug == 'tuoi-doi') {
            $this->printRemainQualityFunc1($section, $tangibleAssets);
        }
        if ($remainQualitySlug == 'trung-binh-cong' || $remainQualitySlug == 'chuyen-gia') {
            $this->printRemainQualityFunc2($section, $tangibleAssets);
        }
        // if ($remainQualitySlug == 'trung-binh-cong') {
        //     $this->printRemainQualityFuncAvg($section, $tangibleAssets, $appraisalCLCL);
        // }

        $this->printNew2($section, $realEstate);
    }
}
