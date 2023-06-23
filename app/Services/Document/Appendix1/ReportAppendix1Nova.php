<?php

namespace App\Services\Document\Appendix1;

use App\Services\Document\Appendix1\ReportAppendix1;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;

class ReportAppendix1Nova extends ReportAppendix1
{
    protected $appraiseTitle = 'ƯỚC TÍNH GIÁ TRỊ QUYỀN SỬ DỤNG ĐẤT';

    protected function surveyDescription(Section $section, $asset)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('           Qua khảo sát hiện trạng thực tế tại khu vực thẩm định giá và tham khảo thông tin từ thị trường, ' . mb_strtoupper($this->companyAcronym) . ' nhận thấy có ');
        $textRun->addText(count($asset->assetGeneral), $this->styleBold);
        $textRun->addText(' tài sản so sánh có các yếu tố tương đồng nhất với tài sản thẩm định, và sử dụng làm cơ sở điều chỉnh để tiến hành ước tính giá trị tài sản thẩm định, cụ thể như sau: ');
    }
    protected function conclusion1($section)
    {
        $namePP = $this->total['method_name'];
        $textRun = $section->addTextRun();
        $textRun->addText('❖ ', ['bold' => false]);
        $textRun->addText('Kết luận:', ['bold' => true]);
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tổng hợp các nguồn thông tin, điều chỉnh các TSSS, mức giá chênh lệch với mức giá trung bình của các mức giá chỉ dẫn không quá ±15%.', ['bold' => false]);
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tổ thẩm định nhận thấy chất lượng thông tin về các tài sản so sánh là tương đương nhau, đồng thời nhận thấy mức giá chỉ dẫn, tổng giá trị điều chỉnh gộp, tổng giá trị điều chỉnh thuần, tổng số lần điều chỉnh, biên độ điều chỉnh của các tài sản không đáng kể. Do đó tổ thẩm định sử dụng mức giá chỉ dẫn ' . $namePP . ' của 03 TSSS làm mức giá của tài sản thẩm định giá giả định.');
    }
    protected function collectInfomationAppraiseData($asset)
    {
        $data = [];
        $stt = 1;
        $data[] = $this->collectInfoSource($stt++, 'Nguồn tin thu thập', $asset);
        $data[] = $this->collectInfoTransactionType($stt++, 'Tình trạng giao dịch', $asset);
        $data[] = $this->collectInfoTransactionTime('', 'Thời điểm giao dịch', $asset);
        $data[] = $this->collectInfoCoordinate($stt++, 'Tọa độ', $asset);
        $data[] = $this->collectInfoAddressAppraise($stt++, 'Vị trí thửa đất', $asset);
        $data[] = $this->collectInfoLegal($stt++, 'Pháp lý', $asset);
        $data[] = $this->collectInfoAreaAppraise($stt, "Tổng diện tích ($this->m2)", $asset);
        $data[] = $this->collectInfoAppraiseSumArea("$stt.1", "Phù hợp QH", 'main_area');

        foreach ($this->landType['appraise'] as $key => $land) {
            $acronym = $land['acronym'];
            $data[] = $this->collectInfoAppraiseArea('', "$acronym", $key, 'main_area');
        }
        $data[] = $this->collectInfoAppraiseSumArea("$stt.2", "Vi phạm QH", 'planning_area');
        foreach ($this->landType['appraise'] as $key => $land) {
            $acronym = $land['acronym'];
            $data[] = $this->collectInfoAppraiseArea('', "$acronym", $key, 'planning_area');
        }
        $stt++;
        $data[] = $this->collectInfoOnlyTitle($stt++, "Đơn giá theo QĐ UBND");
        foreach ($this->landType['appraise'] as $key => $land) {
            $acronym = $land['acronym'];
            $data[] = $this->collectInfoAppraiseUBNDPrice('', "$acronym (đ/$this->m2)", $key, 'price');
        }
        $data[] = $this->collectInfoAppraiseFrontSideWidth($stt++, 'Chiều rộng mặt tiền', $asset);
        $data[] = $this->collectInfoAppraiseInsightWitdh($stt++, 'Chiều dài (m)', $asset);
        $data[] = $this->collectInfoAppraiseShape($stt++, 'Hình dáng', $asset);
        $data[] = $this->collectInfoAppraiseBuidingType($stt++, 'Kết cấu xây dựng', $asset);
        $data[] = $this->collectInfoAppraiseBuidingArea('', "DTSXD ($this->m2)", $asset);
        $data[] = $this->collectInfoAppraiseBuidingRemainRate('', 'Tỷ lệ CLCL', $asset);
        $data[] = $this->collectInfoAppraiseBuidingUnitPrice('', "Đơn giá xây dựng mới (đ/$this->m2)", $asset);
        $data[] = $this->collectInfoAppraiseBuidingPrice($stt++, 'Giá trị còn lại CTXD (đ)', $asset);
        $data[] = $this->collectInfoAppraiseOtherAssetPrice($stt++, 'Tổng giá trị tài sản khác', $asset);
        $data[] = $this->collectInfoAppraisePropertyDescripion($stt++, 'Vị trí', $asset);
        $data[] = $this->collectInfoAppraisePropertyMaterial($stt++, 'Kết cấu đường', $asset);
        $data[] = $this->collectInfoAppraiseRoadWidth($stt++, 'Độ rộng đường (m)', $asset);
        $ytks = $this->collectInfoOtherFactor($asset);
        foreach ($ytks as $ytk) {
            $ytk[0] = $stt++;
            $data[] = $ytk;
        }
        // // giá trị tài sản
        $data[] = $this->collectInfoSellingAppraisePrice($stt++, 'Giá rao bán (đ)', $asset);
        $data[] = $this->collectInfoSellingPriceRate($stt++, 'Tỷ lệ rao bán', $asset);
        $data[] = $this->collectInfoAppraiseTotalEstimatePrice($stt++, 'Tổng giá trị tài sản ước tính (đ)', $asset);
        $data[] = $this->collectInfoAppraiseViolatePrice($stt++, 'Giá trị phần diện tích vi phạm QH (đ)', $asset);
        $data[] = $this->collectInfoAppraiseChangePurposePrice($stt++, 'Tiền nộp thuế quy đổi chuyển mục đích sử dụng đất (đ)', $asset);
        $data[] = $this->collectInfoAppraiseEstimateAmount($stt++, 'Giá trị QSDĐ ' . $this->baseAcronym . ' ước tính (đ)', $asset);
        $data[] = $this->collectInfoAppraiseAvgPrice($stt++, 'Đơn giá QSDĐ ' . $this->baseAcronym . '(đ/'. $this->m2 .')', $asset);
        return $data;
    }

    protected function printContent1(Section $section, $datas)
    {
        foreach ($datas as $index => $asset) {

            $this->processAssetData($asset);

            if (($index + 1) > 1) {
                $section->addPageBreak();
            }
            if ($this->isOnlyAsset) {
                $textRun = $section->addTextRun();
                $textRun->addText('     ' . ($index + 1) . '. Tài sản thẩm định: ', ['bold' => true, 'size' => 14]);
            } else {
                $textRun = $section->addTextRun('Heading2');
                $textRun->addText('Tài sản thẩm định ' . ($index + 1) . ': ', ['bold' => true, 'size' => 14]);
            }
            // $textRun->addText($asset->appraise_asset ?? '', ['size' => 13, 'bold' => false]);
            $assetName = $asset->appraise_asset;
            $address = $asset->full_address;
            $this->printAssetInfo($section, $assetName, $address);

            $this->surveyDescription($section, $asset);
            // $this->mapImage($section, $asset->pic);
            $this->collectInfomation($section, $asset);
            $this->collectDescription($section, $asset);
            // // // phân tích, so sánh, điều chỉnh mức giá
            $this->comparisonDescription($section, $asset);
            // // //yếu tố khác biệt
            $this->getDifferenceAsset($section, $asset);
            // // //so sánh các yếu tố
            $this->getAdjustComparisonFactor($section, $asset);
            // //kết luận
            $this->conclusion($section, $asset);
        }
    }

}
