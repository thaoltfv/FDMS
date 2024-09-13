<?php

namespace App\Services\Document\Appendix1;

use App\Services\Document\Appendix1\ReportAppendix1;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use App\Services\CommonService;

class ReportAppendix1Nova extends ReportAppendix1
{
    protected $appraiseTitle = 'ƯỚC TÍNH GIÁ TRỊ QUYỀN SỬ DỤNG ĐẤT';

    protected function surveyDescription(Section $section, $asset)
    {
        $textRun = $section->addTextRun();
        // $textRun->addText('           Qua khảo sát hiện trạng thực tế tại khu vực thẩm định giá và tham khảo thông tin từ thị trường, ' . mb_strtoupper($this->companyAcronym) . ' nhận thấy có ');
        // $textRun->addText(count($asset->assetGeneral), $this->styleBold);
        // $textRun->addText(' tài sản so sánh có các yếu tố tương đồng nhất với tài sản thẩm định, và sử dụng làm cơ sở điều chỉnh để tiến hành ước tính giá trị tài sản thẩm định, cụ thể như sau: ');
        $textRun->addText('           Tại thời điểm thẩm định giá, qua khảo sát hiện trạng thực tế và thu thập thông tin thị trường tại khu vực thẩm định giá. Do thị trường khu vực tài sản thẩm định giá hạn chế thu thập được giao dịch thành công, nên tổ thẩm định giá thu thập thông tin rao bán có phỏng vấn và ghi nhận mức giá thương lượng phù hợp khi tiến hành giao dịch mua bán tài sản. Các thông tin tài sản so sánh tương đồng do Tổ thẩm định giá thu thập được như sau: ');
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
        $textRun->addText('     - Tổ thẩm định giá nhận thấy chất lượng thông tin về các tài sản so sánh là tương đương nhau, đồng thời nhận thấy mức giá chỉ dẫn, tổng giá trị điều chỉnh gộp, tổng giá trị điều chỉnh thuần, tổng số lần điều chỉnh, biên độ điều chỉnh của các tài sản chênh lệch không đáng kể. Do đó tổ thẩm định giá sử dụng mức giá chỉ dẫn ' . $namePP . ' làm mức giá tham chiếu của tài sản thẩm định giá.');
    }

    protected function collectInfoExploreTime($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            'Tháng ' . date_format(date_create(CommonService::getCompareWithId($this->asset1->id)), 'm/Y'),
            'Tháng ' . date_format(date_create(CommonService::getCompareWithId($this->asset2->id)), 'm/Y'),
            'Tháng ' . date_format(date_create(CommonService::getCompareWithId($this->asset3->id)), 'm/Y'),
            false
        ];
        return $data;
    }

    protected function getLandAddress($item)
    {
        // dd($item);
        $landNo = isset($item->properties[0]->compare_property_doc[0]->plot_num) ? 'Thửa số: ' . $item->properties[0]->compare_property_doc[0]->plot_num . ', ' : '';
        $docNo = isset($item->properties[0]->compare_property_doc[0]->doc_num) ? 'tờ: ' . $item->properties[0]->compare_property_doc[0]->doc_num . ', ' : '';
        // $address = $landNo . $docNo . $item->ward->name . ', ' . $item->district->name . ', ' . $item->province->name;
        // $address = $landNo . $docNo . $item->full_address;
        $address = $item->full_address;
        return htmlspecialchars($address);
    }

    protected function collectInfoLoaiCanHo($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            (isset($asset->apartmentAssetProperties) && isset($asset->apartmentAssetProperties->loaicanho) && isset($asset->apartmentAssetProperties->loaicanho->description)) ? CommonService::mbUcfirst(htmlspecialchars($asset->apartmentAssetProperties->loaicanho->description)) : '-',
            (isset($this->asset1->room_details[0]) && isset($this->asset1->room_details[0]->loaicanho) && isset($this->asset1->room_details[0]->loaicanho->description)) ? CommonService::mbUcfirst(htmlspecialchars($this->asset1->room_details[0]->loaicanho->description)) : '-',
            (isset($this->asset2->room_details[0]) && isset($this->asset2->room_details[0]->loaicanho) && isset($this->asset2->room_details[0]->loaicanho->description)) ? CommonService::mbUcfirst(htmlspecialchars($this->asset2->room_details[0]->loaicanho->description)) : '-',
            (isset($this->asset3->room_details[0]) && isset($this->asset3->room_details[0]->loaicanho) && isset($this->asset3->room_details[0]->loaicanho->description)) ? CommonService::mbUcfirst(htmlspecialchars($this->asset3->room_details[0]->loaicanho->description)) : '-',
            false
        ];
        return $data;
    }

    protected function collectInfoSellingPriceRate_new($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format(100 - $this->assetPrice['asset1']['percent'], 2, ',', '.') . '%',
            number_format(100 - $this->assetPrice['asset2']['percent'], 2, ',', '.') . '%',
            number_format(100 - $this->assetPrice['asset3']['percent'], 2, ',', '.') . '%',
            false
        ];
        return $data;
    }

    protected function collectInfomationApartmentData($asset)
    {
        $data = [];
        $stt = 1;
        $data[] = $this->collectInfoSource($stt++, 'Nguồn tin thu thập', $asset);
        $data[] = $this->collectInfoSourceByApartment('', 'Hình thức thu thập', $asset);
        $data[] = $this->collectInfoSourceNoteApartment('', 'Ghi chú', $asset);
        $data[] = $this->collectInfoTransactionType($stt++, 'Loại giao dịch', $asset);
        $data[] = $this->collectInfoTransactionTime('', 'Thời điểm giao dịch', $asset);
        $data[] = $this->collectInfoExploreTime('', 'Thời điểm khảo sát', $asset);
        $data[] = $this->collectInfoCoordinate($stt++, 'Tọa độ', $asset);
        $data[] = $this->collectInfoProjectName($stt++, 'Chung cư', $asset);
        $data[] = $this->collectInfoRank($stt++, 'Loại chung cư', $asset);
        $data[] = $this->collectInfoAddress($stt++, 'Vị trí', $asset);
        $data[] = $this->collectInfoLegal($stt++, 'Pháp lý', $asset);
        $data[] = $this->collectInfoFloor($stt++, 'Tầng', $asset);
        $data[] = $this->collectInfoApartmentName($stt++, 'Mã căn hộ', $asset);
        $data[] = $this->collectInfoLoaiCanHo($stt++, 'Loại căn hộ', $asset);
        $data[] = $this->collectInfoArea($stt++, "Diện tích ($this->m2)", $asset);
        $data[] = $this->collectInfoBedroomNum($stt++, 'Số phòng ngủ', $asset);
        $data[] = $this->collectInfoWcNum($stt++, 'Số phòng vệ sinh', $asset);
        $data[] = $this->collectInfoFurnitureQuality($stt++, 'Tình trạng nội thất', $asset);
        $data[] = $this->collectInfoDirection($stt++, 'Hướng chính', $asset);
        $data[] = $this->collectInfoDescription($stt++, 'Mô tả căn hộ', $asset);
        $data[] = $this->collectInfoUtilities($stt++, 'Tiện ích', $asset);
        // yếu tố khác
        $others = $this->collectInfoOtherFactor($asset);
        foreach ($others as $other) {
            $other[0] = $stt++;
            $data[] = $other;
        }
        // giá trị tài sản
        $data[] = $this->collectInfoSellingAppraisePrice($stt++, 'Giá rao bán (đ)', $asset);
        // $data[] = $this->collectInfoSellingPriceRate($stt++, 'Tỷ lệ rao bán', $asset);
        // $data[] = $this->collectInfoSellingNegotiatedPrice('', 'Số tiền thương lượng', $asset);
        $data[] = $this->collectInfoSellingPriceRate_new($stt++, 'Tỷ lệ thương lượng', $asset);
        // $data[] = $this->collectInfoAppraiseTotalEstimatePrice($stt++, 'Tổng giá trị tài sản ước tính', $asset);
        $data[] = $this->collectInfoAppraiseTotalEstimatePrice($stt++, 'Giá rao bán sau thương lượng (đ)', $asset);
        $data[] = $this->collectInfoAppraiseAvgPrice($stt++, "Đơn giá bình quân (đ/$this->m2)", $asset);

        return $data;
    }
    protected function collectInfomationAppraiseData($asset)
    {
        $data = [];
        $method = $asset->appraisal->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->first();
        $stt = 1;
        $data[] = $this->collectInfoSource($stt++, 'Nguồn tin thu thập', $asset);
        $data[] = $this->collectInfoTransactionType($stt++, 'Tình trạng giao dịch', $asset);
        $data[] = $this->collectInfoTransactionTime('', 'Thời điểm giao dịch', $asset);
        $data[] = $this->collectInfoExploreTime('', 'Thời điểm khảo sát', $asset);
        $data[] = $this->collectInfoCoordinate($stt++, 'Tọa độ', $asset);
        $data[] = $this->collectInfoAddressAppraise($stt++, 'Địa chỉ thửa đất', $asset);
        $data[] = $this->collectInfoDistanceAppraise('', 'Khoảng cách TSSS đến TSTĐ', $asset);
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
        // $data[] = $this->collectInfoSellingPriceRate($stt++, 'Tỷ lệ rao bán', $asset);
        // $data[] = $this->collectInfoSellingNegotiatedPrice('', 'Số tiền thương lượng', $asset);
        $data[] = $this->collectInfoSellingPriceRate_new($stt++, 'Tỷ lệ thương lượng', $asset);
        // $data[] = $this->collectInfoAppraiseTotalEstimatePrice($stt++, 'Tổng giá trị tài sản ước tính (đ)', $asset);
        $data[] = $this->collectInfoAppraiseTotalEstimatePrice($stt++, 'Giá rao bán sau thương lượng (đ)', $asset);
        $data[] = $this->collectInfoAppraiseViolatePrice($stt++, 'Giá trị phần diện tích vi phạm QH (đ)', $asset);
        if ($method->slug_value !== 'theo-ty-le-gia-dat-co-so-chinh') {
            $data[] = $this->collectInfoAppraiseChangePurposePrice($stt++, 'Tiền nộp thuế quy đổi chuyển mục đích sử dụng đất (đ)', $asset);
        }
        $data[] = $this->collectInfoAppraiseEstimateAmount($stt++, 'Giá trị QSDĐ ' . $this->baseAcronym . ' ước tính (đ)', $asset);
        if ($method->slug_value === 'theo-ty-le-gia-dat-co-so-chinh') {
            $data[] = $this->tiledatquydoi($stt++, 'Tỉ lệ đất ' . $this->notbaseAcronym . '/đất ' . $this->baseAcronym . '', $asset);
            $data[] = $this->dientichdatquydoi($stt++, 'Diện tích đất ' . $this->notbaseAcronym . ' quy về đất ' . $this->baseAcronym . '(' . $this->m2 . ')', $asset);
            $data[] = $this->dientichdatcuoicung($stt++, 'Diện tích đất ' . $this->baseAcronym . ' sau khi quy đổi (' . $this->m2 . ')', $asset);
        }
        $data[] = $this->collectInfoAppraiseAvgPrice($stt++, 'Đơn giá QSDĐ ' . $this->baseAcronym . '(đ/' . $this->m2 . ')', $asset);
        return $data;
    }

    protected function collectInfoSellingNegotiatedPrice($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format($this->assetPrice['asset1']['change_negotiated_price'] ? $this->assetPrice['asset1']['change_negotiated_price'] : 0, 0, ',', '.'),
            number_format($this->assetPrice['asset2']['change_negotiated_price'] ? $this->assetPrice['asset2']['change_negotiated_price'] : 0, 0, ',', '.'),
            number_format($this->assetPrice['asset3']['change_negotiated_price'] ? $this->assetPrice['asset3']['change_negotiated_price'] : 0, 0, ',', '.'),
            false
        ];
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
            $address = htmlspecialchars($asset->full_address);
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

    protected function getDifferenceAssetByType($table, $compare, $stt)
    {
        if ($stt === 3) {
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, $this->cellVCentered)->addText('', $this->styleBold, $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS' . $stt . ':', $this->styleBold, ['align' => 'right']);
            $cell = $table->addCell(10000 - $this->columnWidthSecond, $this->cellVCentered);
            $description = $compare->description ?: '';
            $cell->addText(CommonService::mbUcfirst(htmlspecialchars($description)) . ' TSTĐ ' . number_format(abs($compare->adjust_percent), $this->countDecimals($compare->adjust_percent), ',', '.') . '%');
        } else {
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, $this->cellVCentered)->addText('', $this->styleBold, $this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS' . $stt . ':', $this->styleBold, ['align' => 'right', 'keepNext' => true]);
            $cell = $table->addCell(10000 - $this->columnWidthSecond, $this->cellVCentered);
            $description = $compare->description ?: '';
            $cell->addText(CommonService::mbUcfirst(htmlspecialchars($description)) . ' TSTĐ ' . number_format(abs($compare->adjust_percent), $this->countDecimals($compare->adjust_percent), ',', '.') . '%', null, $this->keepNext);
        }
    }

    protected function getAdjustComparisonFactorAppraise(Table $table, $asset)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellVCentered)->addText('1', ['bold' => false], $this->cellHCentered);
        if ($this->isApartment) {
            $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText('Đơn giá quyền sở hữu căn hộ', $this->styleBold, $this->cellHCentered);
        } else {
            $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText('Đơn giá quyền sử dụng đất', $this->styleBold, $this->cellHCentered);
        }
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('-', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($this->assetPrice['asset1']['avg_price'], $this->countDecimals($this->assetPrice['asset1']['avg_price']), ',', '.'), $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($this->assetPrice['asset2']['avg_price'], $this->countDecimals($this->assetPrice['asset2']['avg_price']), ',', '.'), $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($this->assetPrice['asset3']['avg_price'], $this->countDecimals($this->assetPrice['asset3']['avg_price']), ',', '.'), $this->styleBold, $this->cellHCentered);
        $compares = $this->comparisonFactor1;
        $alphas = range('A', 'Z');
        $coefficientTitle = 'Hệ số tương quan';
        $rateTitle = 'Tỷ lệ điều chỉnh';
        $adjustTitle = 'Mức điều chỉnh';
        $priceAfterAdjust = 'Giá sau điều chỉnh';
        $stt = 0;
        foreach ($this->factors as $type) {
            $compare1 = $this->comparisonFactor1->where('type', $type)->first();
            if (!empty($compare1)) {
                $compare2 = $this->comparisonFactor2->where('type', $type)->first();
                $compare3 = $this->comparisonFactor3->where('type', $type)->first();
                $title = $compare1->name;
                if ($this->isApartment) {
                    if ($type == 'dien_tich')
                        $this->addCompareRowLengh($table, $title . " ($this->m2)", $alphas[$stt], $compare1->apartment_title, $compare1->asset_title, $compare2->asset_title, $compare3->asset_title, true);
                    else
                        $this->addCompareRowDescription($table, $title, $alphas[$stt], htmlspecialchars($compare1->apartment_title), htmlspecialchars($compare1->asset_title), htmlspecialchars($compare2->asset_title), htmlspecialchars($compare3->asset_title), true);
                } else {
                    if ($type == 'quy_mo')
                        $this->addCompareRowLengh($table, $title . " ($this->m2)", $alphas[$stt], $compare1->appraise_title, $compare1->asset_title, $compare2->asset_title, $compare3->asset_title, true);
                    elseif ($type == 'chieu_rong_mat_tien' || $type == 'chieu_sau_khu_dat' || $type == 'do_rong_duong')
                        $this->addCompareRowLengh($table, $title . ' (m)', $alphas[$stt], $compare1->appraise_title, $compare1->asset_title, $compare2->asset_title, $compare3->asset_title, true);
                    else
                        $this->addCompareRowDescription($table, $title, $alphas[$stt], htmlspecialchars($compare1->appraise_title), htmlspecialchars($compare1->asset_title), htmlspecialchars($compare2->asset_title), htmlspecialchars($compare3->asset_title), true);
                }
                $this->addCompareRowExt1($table,  $coefficientTitle, '', '100', $compare1->adjust_coefficient, $compare2->adjust_coefficient, $compare3->adjust_coefficient, false, '%');
                $this->addCompareRowExt1($table,  $rateTitle, '', '-', $compare1->adjust_percent, $compare2->adjust_percent, $compare3->adjust_percent, false, '%');
                $this->addCompareRowPriceAjust($table,  $adjustTitle, '', '-', $compare1->adjust_price, $compare2->adjust_price, $compare3->adjust_price);
                $this->addCompareRowPrice($table,  $priceAfterAdjust, '', '-', $compare1->total_price, $compare2->total_price, $compare3->total_price);
                $stt++;
            }
        }
        // other
        $others = $this->comparisonFactor1->where('type', 'yeu_to_khac');
        if (!empty($others) && count($others) > 0) {
            foreach ($others as $other1) {
                $position = $other1->position;
                $title = $other1->name;
                $other2 = $this->comparisonFactor2->where('type', 'yeu_to_khac')->where('position', $position)->first();
                $other3 = $this->comparisonFactor3->where('type', 'yeu_to_khac')->where('position', $position)->first();
                if ($this->isApartment) {
                    $this->addCompareRowExt($table, $title, $alphas[$stt], htmlspecialchars($other1->apartment_title), htmlspecialchars($other1->asset_title), htmlspecialchars($other2->asset_title), htmlspecialchars($other3->asset_title), true);
                } else {
                    $this->addCompareRowExt($table, $title, $alphas[$stt], htmlspecialchars($other1->appraise_title), htmlspecialchars($other1->asset_title), htmlspecialchars($other2->asset_title), htmlspecialchars($other3->asset_title), true);
                }
                $this->addCompareRowExt1($table,  $coefficientTitle, '', '100', $other1->adjust_coefficient, $other2->adjust_coefficient, $other3->adjust_coefficient, false, '%');
                $this->addCompareRowExt1($table,  $rateTitle, '', '-', $other1->adjust_percent, $other2->adjust_percent, $other3->adjust_percent, false, '%');
                $this->addCompareRowPriceAjust($table,  $adjustTitle, '', '-', $other1->adjust_price, $other2->adjust_price, $other3->adjust_price);
                $this->addCompareRowPrice($table,  $priceAfterAdjust, '', '-', $other1->total_price, $other2->total_price, $other3->total_price);
                $stt++;
            }
        }

        $this->getAppraisalMethod($table, $asset);
    }
    protected function addCompareRowExt1($table, $title, $alpha, $col1, $col2, $col3, $col4, $isBold = false, $ext = '', $panType = '1-1')
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ($alpha == '') ? $this->cellRowContinue : $this->cellRowSpan)->addText($alpha, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        if ($panType == '2-1') {
            $table->addCell($this->columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText($title, ['bold' => $isBold], ['align' => 'left']);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col2, $this->countDecimals($col2), ',', '.') . '%', ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col3, $this->countDecimals($col3), ',', '.') . '%', ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col4, $this->countDecimals($col4), ',', '.') . '%', ['bold' => $isBold], $this->cellHCentered);
        } elseif ($panType == '2-3') {
            $table->addCell($this->columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText($title, ['bold' => $isBold], ['align' => 'left']);
            $table->addCell($this->columnWidthFourth, ['gridSpan' => 3, 'valign' => 'center'])->addText($col1 . $ext, null, $this->cellHCentered);
        } else {
            $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText($title, ['bold' => $isBold], $this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($col1 != '-' ?  $col1 . $ext : $col1, null, $this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col2, $this->countDecimals($col2), ',', '.') . '%', null, $this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col3, $this->countDecimals($col3), ',', '.') . '%', null, $this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col4, $this->countDecimals($col4), ',', '.') . '%', null, $this->cellHCenteredKeepNext);
        }
    }

    protected function getAssetComparison($comparisons, $avgPrice)
    {
        $legalPrice = 0;
        $totalPriceAfter = $avgPrice;
        $adjustTimes = 0;
        $totalAdjustPrice = 0;
        $totalAdjustPriceABS = 0;
        $minPer = 0;
        $maxPer = 0;
        $stt = 0;
        foreach ($this->factors as $type) {
            $compare = $comparisons->where('type', $type)->first();
            if (!empty($compare)) {
                $percent = $compare->adjust_percent;
                $adjustPrice = 0;
                if ($compare->type == 'phap_ly') {
                    $adjustPrice = round($avgPrice * $percent / 100);
                    $legalPrice = $avgPrice + $adjustPrice;
                    $minPer = abs($percent);
                    $maxPer = abs($percent);
                } else {
                    $adjustPrice = round($legalPrice * $percent / 100);
                    if (($percent != 0  && $minPer > abs($percent)) || $minPer === 0) $minPer = abs($percent);
                    if ($maxPer < abs($percent)) $maxPer = abs($percent);
                }
                $totalPriceAfter += $adjustPrice;
                $totalAdjustPrice += $adjustPrice;
                $totalAdjustPriceABS += abs($adjustPrice);
                if ($percent != 0)
                    $adjustTimes++;
                $compare->adjust_price = $adjustPrice;
                $compare->total_price = $totalPriceAfter;
                $compare->adjust_times = $adjustTimes;
                $compare->min_max = number_format($minPer, $this->countDecimals($minPer), ',', '.') . '% - ' . number_format($maxPer, $this->countDecimals($maxPer), ',', '.') . '%';
                $compare->total_adjust_price = $totalAdjustPrice;
                $compare->total_adjust_price_abs = $totalAdjustPriceABS;
                $compare->stt = $stt;
                $stt++;
            }
        }
        $others = $comparisons->where('type', 'yeu_to_khac');
        if (!empty($others) && count($others) > 0) {
            foreach ($others as $other) {
                $percent = $other->adjust_percent;
                $adjustPrice = 0;
                $adjustPrice = round($legalPrice * $percent / 100);
                if (($percent != 0  && $minPer > abs($percent)) || $minPer === 0) $minPer = abs($percent);
                if ($maxPer < abs($percent)) $maxPer = abs($percent);
                $totalPriceAfter += $adjustPrice;
                $totalAdjustPrice += $adjustPrice;
                $totalAdjustPriceABS += abs($adjustPrice);
                if ($percent != 0)
                    $adjustTimes++;
                $other->adjust_price = $adjustPrice;
                $other->total_price = $totalPriceAfter;
                $other->adjust_times = $adjustTimes;
                $other->min_max = number_format($minPer, $this->countDecimals($minPer), ',', '.') . '% - ' . number_format($maxPer, $this->countDecimals($maxPer), ',', '.') . '%';
                $other->total_adjust_price = $totalAdjustPrice;
                $other->total_adjust_price_abs = $totalAdjustPriceABS;
                $other->stt = $stt;
                $stt++;
            }
        }
    }

    protected function countDecimals($fNumber)
    {
        $fNumber = floatval($fNumber);

        for ($iDecimals = 0; $fNumber != round($fNumber, $iDecimals); $iDecimals++);

        return $iDecimals;
    }

    protected function collectInfoDistanceAppraise($stt, $title, $asset)
    {
        $this->asset1 = $asset->assetGeneral[0];
        $this->asset2 = $asset->assetGeneral[1];
        $this->asset3 = $asset->assetGeneral[2];
        //Filter with all status
        $comparisonFactors = $asset->comparisonFactor;
        $comparisonFactor1 = $comparisonFactors->where('asset_general_id', $this->asset1->id);
        $comparisonFactor2 = $comparisonFactors->where('asset_general_id', $this->asset2->id);
        $comparisonFactor3 = $comparisonFactors->where('asset_general_id', $this->asset3->id);
        //Update new querry with non status to get ket_cau_duong
        $compare1 = $this->getComparisonType($comparisonFactor1, 'khoang_cach');
        $compare2 = $this->getComparisonType($comparisonFactor2, 'khoang_cach');
        $compare3 = $this->getComparisonType($comparisonFactor3, 'khoang_cach');
        $data = [
            $stt,
            $title,
            '-',
            $compare1 ? number_format($compare1->asset_title, $this->countDecimals($compare1->asset_title), ',', '.') . ' m'  : '-',
            $compare2 ? number_format($compare2->asset_title, $this->countDecimals($compare2->asset_title), ',', '.') . ' m'  : '-',
            $compare3 ? number_format($compare3->asset_title, $this->countDecimals($compare3->asset_title), ',', '.') . ' m'  : '-',
            false
        ];
        return $data;
    }
}
