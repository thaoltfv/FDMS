<?php

namespace App\Services\Document\Appendix1;

use App\Enum\EstimateAssetDefault;
use App\Services\CommonService;
use App\Services\Document\DocumentInterface\Report;
use Illuminate\Support\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;

class ReportAppendix1 extends Report
{
    protected $isPrintNational = false;
    protected $isOnlyAsset = false;
    protected $realEstates = [];
    protected $isTangibleAsset = false;
    protected $isApartment = false;
    protected $columnWidthFirst = 1900;
    protected $columnWidthSecond = 1900;
    protected $columnWidthThird = 3300;
    protected $columnWidthFourth = 5000;
    protected $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
    protected $comparisonFactors = null;
    protected $otherComparisonFactors = null;
    protected $otherCompare = null;
    protected $factors = [];
    protected $compareDataByAsset = [];
    protected $total = [];
    protected $assetPrice = [];
    protected $gdtt = [];
    protected $baseAcronym = '';
    protected $asset1;
    protected $asset2;
    protected $asset3;
    protected $comparisonFactor1 = null;
    protected $comparisonFactor2 = null;
    protected $comparisonFactor3 = null;
    protected $adapter1 = null;
    protected $adapter2 = null;
    protected $adapter3 = null;
    protected $landType = [];
    protected $adapters = null;
    protected $appraiseTitle = 'QUYỀN SỬ DỤNG ĐẤT';
    protected $apartmentTitle = 'QUYỀN SỞ HỮU CĂN HỘ CHUNG CƯ';
    #footer, reportname, path
    public function getFooterString($data)
    {
        $data = (object)$data;
        $createdName =  $this->createdName;
        if (isset($data->document_date) && !empty(trim($data->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $data->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        if (is_countable($data->realEstate)) $reportID = 'HSTD_' . $data->id;
        else $reportID = 'TSTD_' . $data->id;

        return mb_strtoupper($this->envDocument)  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID;
    }
    public function getReportName()
    {
        return '3_PL1';
    }

    public function printTitle(Section $section, $data)
    {
        $this->setProperties($data);
        $section->addImage($this->logoUrl, $this->styleImageLogo);
        $section->addText('PHỤ LỤC 1 KÈM THEO BÁO CÁO THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);
        $title = $this->isApartment ? $this->apartmentTitle : $this->appraiseTitle;
        $section->addText($title, ['italic' => true, 'size' => 14], ['align' => 'center']);
    }

    private function setProperties($data)
    {

        $this->isOnlyAsset = (!is_countable($data->realEstate) || count($data->realEstate) == 1);
        $this->realEstates = $data->realEstate;
        $this->isTangibleAsset = in_array('DCN', $data->document_type);
        $this->isApartment = in_array('CC', $data->document_type);
        $this->styleTable = [
            'borderSize' => 1,
            'align' => JcTable::START,
            'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
        ];
    }
    //content
    public function printContent(Section $section, $certificate)
    {
        $datas = [];
        if ($this->isApartment) {
            $datas = $this->getApartmentData();
            $this->factors = EstimateAssetDefault::COMPARATION_FACTORS_APARTMENT;
        } else {
            $datas = $this->getAppraiseData();
            $this->factors = EstimateAssetDefault::COMPARATION_FACTORS;
        }
        $this->printContent1($section, $datas);
    }

    protected function processAssetData($asset)
    {
        $this->asset1 = $asset->assetGeneral[0];
        $this->asset2 = $asset->assetGeneral[1];
        $this->asset3 = $asset->assetGeneral[2];
        $this->assetPrice = [];
        $this->total = [];
        $this->gdtt = [];
        $this->landType = [];
        $this->comparisonFactors = $asset->comparisonFactor->where('status', 1);
        $this->comparisonFactor1 = $this->comparisonFactors->where('asset_general_id', $this->asset1->id);
        $this->comparisonFactor2 = $this->comparisonFactors->where('asset_general_id', $this->asset2->id);
        $this->comparisonFactor3 = $this->comparisonFactors->where('asset_general_id', $this->asset3->id);
        if ($this->isApartment) {
            $this->getApartmentAsset($asset);
            $this->landType['apartment'] =  $this->getApartmentAsset($asset->apartmentAssetProperties);
            $this->landType['asset1'] =  $this->getApartmentAsset($this->asset1->room_details[0]);
            $this->landType['asset2'] =  $this->getApartmentAsset($this->asset2->room_details[0]);
            $this->landType['asset3'] =  $this->getApartmentAsset($this->asset3->room_details[0]);
            $this->adapter1 = $asset->apartmentAdapter->where('asset_general_id', $this->asset1->id)->first();
            $this->adapter2 = $asset->apartmentAdapter->where('asset_general_id', $this->asset2->id)->first();
            $this->adapter3 = $asset->apartmentAdapter->where('asset_general_id', $this->asset3->id)->first();
            $area1 =  $this->landType['asset1']['main_area'];
            $area2 =  $this->landType['asset2']['main_area'];
            $area3 =  $this->landType['asset3']['main_area'];
        } else {
            $this->adapter1 = $asset->appraiseAdapter->where('asset_general_id', $this->asset1->id)->first();
            $this->adapter2 = $asset->appraiseAdapter->where('asset_general_id', $this->asset2->id)->first();
            $this->adapter3 = $asset->appraiseAdapter->where('asset_general_id', $this->asset3->id)->first();

            $this->getAllLandType($asset);
            $this->adapters = $asset->appraiseAdapter;
            $this->landType['asset1'] = $this->getAssetLandType($this->asset1, $asset);
            $this->landType['asset2'] = $this->getAssetLandType($this->asset2, $asset);
            $this->landType['asset3'] = $this->getAssetLandType($this->asset3, $asset);
            $area1 =  $this->sumArea($this->landType['asset1'], 'main_area');
            $area2 =  $this->sumArea($this->landType['asset2'], 'main_area');
            $area3 =  $this->sumArea($this->landType['asset3'], 'main_area');
        }
        $this->assetPrice['asset1'] = $this->getAssetPriceData($this->asset1, $this->adapter1, $area1);
        $this->assetPrice['asset2'] = $this->getAssetPriceData($this->asset2, $this->adapter2, $area2);
        $this->assetPrice['asset3'] = $this->getAssetPriceData($this->asset3, $this->adapter3, $area3);

        $this->getAssetComparison($this->comparisonFactor1, $this->assetPrice['asset1']['avg_price']);
        $this->getAssetComparison($this->comparisonFactor2, $this->assetPrice['asset2']['avg_price']);
        $this->getAssetComparison($this->comparisonFactor3, $this->assetPrice['asset3']['avg_price']);
    }

    private function getComparisonType($compare, $type)
    {
        $result = $compare->where('type', $type)->first();
        return $result;
    }

    private function getAssetComparison($comparisons, $avgPrice)
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
                $compare->min_max = $minPer . '% - ' . $maxPer . '%';
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
                $other->min_max = $minPer . '% - ' . $maxPer . '%';
                $other->total_adjust_price = $totalAdjustPrice;
                $other->total_adjust_price_abs = $totalAdjustPriceABS;
                $other->stt = $stt;
                $stt++;
            }
        }
    }

    private function getAssetPriceData($item, $adapter, $mainArea)
    {
        $result = [];
        $totalAmount = floatval($item->total_amount);
        $buildingPrice = floatval($item->total_construction_amount);
        $purposePrice = 0;
        $violatePrice = 0;
        $adjustPercent = floatval($adapter->percent);
        $totalEstimateAmount = round($totalAmount * $adjustPercent / 100);
        if ($this->isApartment) {
            $estimateAmount = $totalEstimateAmount;
        } else {
            $purposePrice = floatval($adapter->change_purpose_price);
            $violatePrice = floatval($adapter->change_violate_price);
            $estimateAmount = $totalEstimateAmount - $buildingPrice +  $purposePrice - $violatePrice;
        }
        $avgPrice = round($estimateAmount / $mainArea);
        $result = [
            'id' => $item->id,
            'building_price' => $buildingPrice,
            'total_amount' => $totalAmount,
            'percent' => $adjustPercent,
            'total_estimate_amount' => $totalEstimateAmount,
            'change_violate_price' => $violatePrice,
            'change_purpose_price' => $purposePrice,
            'estimate_amount' => $estimateAmount,
            'avg_price' => $avgPrice
        ];
        return $result;
    }

    private function getAssetLandType($item, $asset)
    {
        $result = [];
        $assetDetails = $asset->properties[0]->propertyDetail;
        $unitArea = $asset->assetUnitArea->where('asset_general_id', $item->id);
        $unitPrice = $asset->assetUnitPrice->where('asset_general_id', $item->id);
        foreach ($assetDetails as $detail) {
            $totalArea = 0;
            $mainArea = 0;
            $idPurose = $detail->land_type_purpose_id;
            $violateArea = $unitArea->where('land_type_id', $idPurose)->first();
            $priceData = $unitPrice->where('land_type_id', $idPurose)->first();
            $isMain = $detail->is_transfer_facility ?: false;;
            $acronym = $detail->landTypePurpose->acronym;

            foreach ($item->properties[0]->property_detail as $cDetail) {
                if ($cDetail->land_type_purpose === $idPurose) {
                    $totalArea = $cDetail->total_area;
                }
            }
            $planinngArea = $violateArea ? $violateArea->violation_asset_area : 0;
            $price = 0;
            if ($priceData && ($priceData->update_value || $priceData->original_value)) {
                $price = $priceData->update_value ?: $priceData->original_value;
            }

            if ($totalArea > 0) {
                $mainArea = $totalArea - $planinngArea;
            }
            $result[$idPurose] = $this->setLandTypeData($idPurose, $acronym, $isMain, $totalArea, $planinngArea, $mainArea, $price);
        }
        $assetDetails = $item->properties[0]->property_detail;
        foreach ($assetDetails as $detail) {
            $idPurose = $detail->land_type_purpose;
            if (empty($result[$idPurose])) {
                $mainArea = 0;
                $violateArea = $unitArea->where('land_type_id', $idPurose)->first();
                $priceData = $unitPrice->where('land_type_id', $idPurose)->first();
                $isMain = false;
                $acronym = $detail->land_type_purpose_data->acronym;
                $totalArea = $detail->total_area;
                $planinngArea = $violateArea ? $violateArea->violation_asset_area : 0;
                $price = 0;
                if ($priceData && ($priceData->update_value || $priceData->original_value)) {
                    $price = $priceData->update_value ?: $priceData->original_value;
                }
                if ($totalArea > 0) {
                    $mainArea = $totalArea - $planinngArea;
                }
                $result[$idPurose] = $this->setLandTypeData($idPurose, $acronym, $isMain, $totalArea, $planinngArea, $mainArea, $price);
            }
        }
        return $result;
    }

    private function getApartmentAsset($detail)
    {
        $result = [];
        $result = [
            'main_area' => floatval($detail->area),
        ];
        return $result;
    }
    private function getAllLandType($asset)
    {
        $assetDetails = $asset->properties[0]->propertyDetail;
        foreach ($assetDetails as $detail) {
            $totalArea = $detail->total_area ?? 0;
            $planinngArea = $detail->planning_area ?? 0;
            $mainArea = $totalArea - $planinngArea;
            $price = $detail->circular_unit_price;
            $id = $detail->land_type_purpose_id;
            $acronym = $detail->landTypePurpose->acronym;
            $isMain = $detail->is_transfer_facility;
            if ($isMain)
                $this->baseAcronym = $acronym;
            $this->landType['appraise'][$id] = $this->setLandTypeData($id, $acronym, $isMain, $totalArea, $planinngArea, $mainArea, $price);
        }
        foreach ($asset->assetGeneral as $item) {
            $assetDetails = $item->properties[0]->property_detail;
            foreach ($assetDetails as $detail) {
                $id = $detail->land_type_purpose;
                if (empty($this->landType['appraise'][$id])) {
                    $acronym = $detail->land_type_purpose_data->acronym;
                    $price = 0;
                    $this->landType['appraise'][$id] = $this->setLandTypeData($id, $acronym, false, 0, 0, 0, $price);
                }
            }
        }
    }

    protected function printAssetInfo($section, $name, $address)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tên tài sản: ', ['size' => 13, 'bold' => true]);
        $textRun->addText($name ? $name : '', ['size' => 13, 'bold' => false]);
        if (!empty($address)) {
            $textRun = $section->addTextRun();
            $textRun->addText('     - Địa chỉ: ', ['size' => 13, 'bold' => true]);
            $textRun->addText($address ? $address : '', ['size' => 13, 'bold' => false]);
        }
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
            $this->mapImage($section, $asset->pic);
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
    protected function surveyDescription(Section $section, $asset)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('           Qua khảo sát hiện trạng thực tế tại khu vực thẩm định giá và tham khảo thông tin từ thị trường, ' . mb_strtoupper($this->acronym) . ' nhận thấy có ');
        $textRun->addText(count($asset->assetGeneral), $this->styleBold);
        $textRun->addText(' tài sản so sánh có các yếu tố tương đồng nhất với tài sản thẩm định, và sử dụng làm cơ sở điều chỉnh để tiến hành xác định giá trị tài sản thẩm định, cụ thể như sau: ');
    }
    protected function collectInfomation(Section $section, $asset)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('✓ ', ['bold' => false]);
        $textRun->addText('Thu thập thông tin TSTĐG và các TSSS ', $this->styleBold);
        $section->addText('BẢNG TỔNG HỢP THÔNG TIN TSTĐG VÀ TSSS', null, ['align' => 'center']);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(600, $this->cellRowSpan)->addText('TT', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText('Chỉ tiêu', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(' TSTĐ', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS1', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS2', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS3', $this->styleBold, $this->cellHCentered);
        $datas = [];
        if ($this->isApartment)
            $datas = $this->collectInfomationApartmentData($asset);
        else
            $datas = $this->collectInfomationAppraiseData($asset);
        foreach ($datas as $index => $data) {
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, ($data[0] == '') ? $this->cellRowContinue : $this->cellRowSpan)->addText($data[0], null, $data[6] ? $this->cellHCenteredKeepNext : $this->cellHCentered);
            $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText($data[1], null, $data[6] ? $this->cellHCenteredKeepNext : $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($data[2], null, $data[6] ? $this->cellHCenteredKeepNext : $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($data[3], null, $data[6] ? $this->cellHCenteredKeepNext : $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($data[4], null, $data[6] ? $this->cellHCenteredKeepNext : $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($data[5], null, $data[6] ? $this->cellHCenteredKeepNext : $this->cellHCentered);
        }
    }
    protected function mapImage(Section $section, $pic)
    {
        $mapImage = $pic->where('picType.description', 'HÌNH BẢN ĐỒ')->first();
        if (!empty($mapImage) && !empty($mapImage->link)) {
            $textRun = $section->addTextRun();
            $textRun->addText('✓ ', ['bold' => false]);
            $textRun->addText('Sơ đồ vị trí TSTĐG và các TSSS.', $this->styleBold);
            $section->addImage($mapImage->link, $this->styleMapImage);
        }
    }
    protected function collectInfomationApartmentData($asset)
    {
        $data = [];
        $stt = 1;
        $data[] = $this->collectInfoSource($stt++, 'Nguồn tin thu thập', $asset);
        $data[] = $this->collectInfoSourceByApartment('', 'Hình thức thu thập', $asset);
        // $data[] = $this->collectInfoSourceNote('', 'Ghi chú', $asset);
        // $data[] = $this->collectInfoTransactionType($stt++, 'Loại giao dịch', $asset);
        // $data[] = $this->collectInfoTransactionTime('', 'Thời điểm giao dịch', $asset);
        $data[] = $this->collectInfoCoordinate($stt++, 'Tọa độ', $asset);
        $data[] = $this->collectInfoProjectName($stt++, 'Chung cư', $asset);
        $data[] = $this->collectInfoRank($stt++, 'Loại căn hộ', $asset);
        $data[] = $this->collectInfoAddress($stt++, 'Vị trí', $asset);
        $data[] = $this->collectInfoLegal($stt++, 'Pháp lý', $asset);
        $data[] = $this->collectInfoFloor($stt++, 'Tầng', $asset);
        $data[] = $this->collectInfoApartmentName($stt++, 'Mã căn hộ', $asset);
        $data[] = $this->collectInfoArea($stt++, "Diện tích (đ/$this->m2)", $asset);
        $data[] = $this->collectInfoBedroomNum($stt++, 'Số phòng ngủ', $asset);
        $data[] = $this->collectInfoWcNum($stt++, 'Số phòng vệ sinh', $asset);
        $data[] = $this->collectInfoFurnitureQuality($stt++, 'Tình trạng nội thất', $asset);
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
        $data[] = $this->collectInfoSellingPriceRate($stt++, 'Tỷ lệ rao bán', $asset);
        $data[] = $this->collectInfoAppraiseTotalEstimatePrice($stt++, 'Tổng giá trị tài sản ước tính', $asset);
        $data[] = $this->collectInfoAppraiseAvgPrice($stt++, "Đơn giá bình quân (đ/$this->m2)", $asset);

        return $data;
    }
    protected function collectInfomationAppraiseData($asset)
    {
        $data = [];
        $stt = 1;
        $data[] = $this->collectInfoSource($stt++, 'Nguồn tin thu thập', $asset);
        $data[] = $this->collectInfoSourceBy('', 'Hình thức thu thập', $asset);
        $data[] = $this->collectInfoSourceNote('', 'Ghi chú', $asset);
        $data[] = $this->collectInfoTransactionType($stt++, 'Loại giao dịch', $asset);
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
        $data[] = $this->collectInfoAppraiseFrontSideWidth($stt++, 'Chiều rộng mặt tiền (m)', $asset);
        $data[] = $this->collectInfoAppraiseInsightWitdh($stt++, 'Chiều dài (m)', $asset);
        $data[] = $this->collectInfoAppraiseShape($stt++, 'Hình dáng', $asset);
        $data[] = $this->collectInfoAppraiseBuidingType($stt++, 'Kết cấu xây dựng', $asset);
        $data[] = $this->collectInfoAppraiseBuidingArea('', "DTSXD ($this->m2)", $asset);
        $data[] = $this->collectInfoAppraiseBuidingRemainRate('', 'Tỷ lệ CLCL', $asset);
        $data[] = $this->collectInfoAppraiseBuidingUnitPrice('', "Đơn giá xây dựng mới (đ/$this->m2)", $asset);
        $data[] = $this->collectInfoAppraiseBuidingPrice($stt++, 'Giá trị còn lại CTXD (đ)', $asset);
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
        $data[] = $this->collectInfoAppraiseChangePurposePrice($stt++, "Chi phí chuyển MĐSD (đ)", $asset);
        $data[] = $this->collectInfoAppraiseEstimateAmount($stt++, 'Giá trị QSDĐ ' . $this->baseAcronym . ' ước tính (đ)', $asset);
        $data[] = $this->collectInfoAppraiseAvgPrice($stt++, 'Đ/giá ' . $this->baseAcronym . " bình quân (đ/$this->m2)", $asset);
        return $data;
    }
    protected function getStringCoordinates($coordinates)
    {
        $str = '';
        if (!empty($coordinates)) {
            $arrCoord = explode(',', $coordinates);
            $str0 = trim($arrCoord[0] ?: '');
            $str1 = trim($arrCoord[1] ?: '');
            $str = $str0 . "\n" . $str1;
        }
        return $str;
    }
    protected function collectDescription(Section $section, $asset)
    {
        $section->addPageBreak(1);
        $textRun = $section->addTextRun();
        $textRun->addText('✓ ', ['bold' => false]);
        $textRun->addText('Phân tích, so sánh, điều chỉnh mức giá do các yếu khác biệt của các tài sản so sánh với tài sản cần thẩm định giá:', ['bold' => true, 'italic' => true]);
        $table = $section->addTable('Rowspan');
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellVCentered)->addText(null, ['bold' => false], $this->cellHCentered);
        $cell = $table->addCell(10000, $this->cellColSpan);
        $cell->addText('Phân tích, so sánh các tài sản so sánh với tài sản cần thẩm định giá:', $this->styleBold);

        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellVCentered)->addText('+', $this->styleBold, $this->cellHCentered);
        $cell = $table->addCell(10000, $this->cellColSpan);
        $cell->addText('Căn cứ vào quá trình khảo sát thực tế tài sản thẩm định giá.', ['bold' => false]);

        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellVCentered)->addText('+', $this->styleBold, $this->cellHCentered);
        $cell = $table->addCell(10000, $this->cellColSpan);
        $cell->addText('Căn cứ vào bảng thông tin các tài sản so sánh (TSSS) mà ' . mb_strtoupper($this->acronym) . ' đã thu thập trong vòng 02 năm so với thời điểm định giá.', ['bold' => false]);
    }
    protected function comparisonDescription(Section $section, $asset)
    {
        $table = $section->addTable('Rowspan');
        if ($this->isApartment) {
            $this->comparisonDescriptionApartment($table, 1, $this->comparisonFactor1);
            $this->comparisonDescriptionApartment($table, 2, $this->comparisonFactor2);
            $this->comparisonDescriptionApartment($table, 3, $this->comparisonFactor3);
        } else {
            $this->comparisonDescriptionAppraise($table, 1, $this->comparisonFactor1);
            $this->comparisonDescriptionAppraise($table, 2, $this->comparisonFactor2);
            $this->comparisonDescriptionAppraise($table, 3, $this->comparisonFactor3);
        }
    }
    protected function comparisonDescriptionApartment($table, $stt, $comparison)
    {
        $table->addRow(500, $this->cantSplit);
        $table->addCell(600, $this->cellRowSpan)->addText('▪', $this->styleBold, $this->cellHCenteredKeepNext);
        $cell = $table->addCell(10000, $this->cellColSpan);
        $cell->addText('TSSS' . $stt . ':', $this->styleBold, $this->styleAlignStart);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellRowSpan)->addText('', $this->styleBold, $this->cellHCentered);
        $cell = $table->addCell(10000, $this->cellColSpan);
        foreach ($comparison as $i) {
            $type = $i->type;
            $iDesription = $i->description ?: 'TƯƠNG ĐỒNG';
            $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? $i->apartment_title : $i->asset_title . ' so với ' . $i->apartment_title;
            $description = $i->name . ' ' . mb_strtolower($iDesription) . ' TSTĐ ' . '(';
            // $description = $i->name . ' ' . $iDesription . ' TSTĐ ' . '(';
            if ($type == 'dien_tich')
                $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? number_format($i->apartment_title, 2, ',', '.') . $this->m2 : number_format($i->asset_title, 2, ',', '.') . $this->m2 . ' so với ' . number_format($i->apartment_title, 2, ',', '.') . $this->m2;
            else
                $strCompare = CommonService::mbUcfirst(($iDesription == 'TƯƠNG ĐỒNG') ? $i->apartment_title : $i->asset_title . ' so với ' . $i->apartment_title);

            $description .= $strCompare . ')';
            $cell->addText($description, ['bold' => false]);
        }
    }
    protected function comparisonDescriptionAppraise($table, $stt, $comparison)
    {
        $table->addRow(500, $this->cantSplit);
        $table->addCell(600, $this->cellRowSpan)->addText('▪', $this->styleBold, $this->cellHCenteredKeepNext);
        $cell = $table->addCell(10000, $this->cellColSpan);
        $cell->addText('TSSS' . $stt . ':', $this->styleBold, $this->styleAlignStart);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellRowSpan)->addText('', $this->styleBold, $this->cellHCentered);
        $cell = $table->addCell(10000, $this->cellColSpan);
        foreach ($this->factors as $type) {
            $i = $comparison->where('type', $type)->first();
            if (!empty($i)) {
                $iDesription = $i->description ?: 'TƯƠNG ĐỒNG';
                $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? $i->appraise_title : $i->asset_title . ' so với ' . $i->appraise_title;
                $description = $i->name . ' ' . mb_strtolower($iDesription) . ' TSTĐ ' . '(';
                // $description = $i->name . ' ' . $iDesription . ' TSTĐ ' . '(';
                if ($type == 'quy_mo')
                    $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? number_format($i->appraise_title, 2, ',', '.') . $this->m2 : number_format($i->asset_title, 2, ',', '.') . $this->m2 . ' so với ' . number_format($i->appraise_title, 2, ',', '.') . $this->m2;
                elseif ($type == 'chieu_rong_mat_tien' || $type == 'chieu_sau_khu_dat' || $type == 'do_rong_duong')
                    $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? number_format($i->appraise_title, 2, ',', '.') . 'm' : number_format($i->asset_title, 2, ',', '.') . 'm' . ' so với ' . number_format($i->appraise_title, 2, ',', '.') . 'm';
                else
                    $strCompare = CommonService::mbUcfirst(($iDesription == 'TƯƠNG ĐỒNG') ? $i->appraise_title : $i->asset_title . ' so với ' . $i->appraise_title);

                $description .= $strCompare . ')';
                $cell->addText($description, ['bold' => false]);
            }
        }
        $otherComparisonFactors =  $comparison->where('type', 'yeu_to_khac');
        if (!empty($otherComparisonFactors) && count($otherComparisonFactors) > 0) {
            foreach ($otherComparisonFactors as $other) {
                $iDesription = $other->description ?: 'TƯƠNG ĐỒNG';
                $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? $other->appraise_title : $other->asset_title . ' so với ' . $other->appraise_title;
                $description = $other->name . ' ' . mb_strtolower($iDesription) . ' TSTĐ ' . '(';
                // $description = $other->name . ' ' . $iDesription . ' TSTĐ ' . '(';
                if ($type == 'quy_mo')
                    $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? number_format($other->appraise_title, 2, ',', '.') . $this->m2 : number_format($other->asset_title, 2, ',', '.') . $this->m2 . ' so với ' . number_format($other->appraise_title, 2, ',', '.') . $this->m2;
                elseif ($type == 'chieu_rong_mat_tien' || $type == 'chieu_sau_khu_dat' || $type == 'do_rong_duong')
                    $strCompare = ($iDesription == 'TƯƠNG ĐỒNG') ? number_format($other->appraise_title, 2, ',', '.') . 'm' : number_format($other->asset_title, 2, ',', '.') . 'm' . ' so với ' . number_format($other->appraise_title, 2, ',', '.') . 'm';
                else
                    $strCompare =($iDesription == 'TƯƠNG ĐỒNG') ? $other->appraise_title : $other->asset_title . ' so với ' . $other->appraise_title;

                $description .= $strCompare . ')';
                $cell->addText($description, ['bold' => false]);
            }
        }
    }
    protected function getDifferenceAsset(Section $section, $asset)
    {
        $section->addTextBreak();
        $table = $section->addTable('Rowspan');
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellVCentered)->addText(null, $this->styleBold, $this->cellHCenteredKeepNext);
        $cell = $table->addCell(10000, $this->cellColSpan);
        $cell->addText('Điều chỉnh mức giá do các yếu khác biệt của các tài sản so sánh với tài sản cần thẩm định giá:', ['bold' => true, 'italic' => true], ['align' => JcTable::START, 'keepNext' => true]);
        foreach ($this->factors as $type) {
            $compare1 = $this->comparisonFactor1->where('type', $type)->first();
            if (!empty($compare1)) {
                $compare2 = $this->comparisonFactor2->where('type', $type)->where('position', $compare1->position)->first();
                $compare3 = $this->comparisonFactor3->where('type', $type)->where('position', $compare1->position)->first();
                $table->addRow(500, $this->cantSplit);
                $table->addCell(600, $this->cellRowSpan)->addText('+', $this->styleBold, $this->cellHCenteredKeepNext);
                $cell = $table->addCell(10000, $this->cellColSpan);
                $cell->addText($compare1->name, $this->styleBold, ['align' => JcTable::START, 'keepNext' => true]);
                $this->getDifferenceAssetByType($table, $compare1, 1);
                $this->getDifferenceAssetByType($table, $compare2, 2);
                $this->getDifferenceAssetByType($table, $compare3, 3);
            }
        }
        $otherComparisonFactors =  $this->comparisonFactor1->where('type', 'yeu_to_khac');
        if (!empty($otherComparisonFactors) && count($otherComparisonFactors) > 0) {
            foreach ($otherComparisonFactors as $other) {
                $compare2 = $this->comparisonFactor2->where('type', 'yeu_to_khac')->where('position',  $other->position)->first();
                $compare3 = $this->comparisonFactor3->where('type', 'yeu_to_khac')->where('position',  $other->position)->first();
                $table->addRow(500, $this->cantSplit);
                $table->addCell(600, $this->cellRowSpan)->addText('+', $this->styleBold, $this->cellHCenteredKeepNext);
                $cell = $table->addCell(10000, $this->cellColSpan);
                $cell->addText( $other->name, $this->styleBold, ['align' => JcTable::START, 'keepNext' => true]);
                $this->getDifferenceAssetByType($table,  $other, 1);
                $this->getDifferenceAssetByType($table, $compare2, 2);
                $this->getDifferenceAssetByType($table, $compare3, 3);
            }
        }
    }
    private function getDifferenceAssetByType($table, $compare, $stt)
    {
        if ($stt === 3) {
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, $this->cellVCentered)->addText('', $this->styleBold, $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS' . $stt . ':', $this->styleBold, ['align' => 'right']);
            $cell = $table->addCell(10000 - $this->columnWidthSecond, $this->cellVCentered);
            $description = $compare->description ?: '';
            $cell->addText(CommonService::mbUcfirst($description) . ' TSTĐ ' . abs($compare->adjust_percent) . '%');
        } else {
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, $this->cellVCentered)->addText('', $this->styleBold, $this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS' . $stt . ':', $this->styleBold, ['align' => 'right', 'keepNext' => true]);
            $cell = $table->addCell(10000 - $this->columnWidthSecond, $this->cellVCentered);
            $description = $compare->description ?: '';
            $cell->addText(CommonService::mbUcfirst($description) . ' TSTĐ ' . abs($compare->adjust_percent) . '%', null, $this->keepNext);
        }
    }
    protected function getAdjustComparisonFactor(Section $section, $asset)
    {
        $section->addText('BẢNG ĐIỀU CHỈNH CÁC YẾU TỐ SO SÁNH TSTĐG VÀ TSSS', ['bold' => false, 'size' => 13], $this->cellHCentered);
        $section->addText('Đvt: đ/' . $this->m2, ['italic' => true], ['align' => 'right', 'keepNext' => true]);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(600, $this->cellVCentered)->addText('TT', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText('YẾU TỐ', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(' TSTĐ', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS1', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS2', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('TSSS3', $this->styleBold, $this->cellHCentered);
        $this->getAdjustComparisonFactorAppraise($table, $asset);
    }
    protected function getAdjustComparisonFactorAppraise(Table $table, $asset)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, $this->cellVCentered)->addText('1', ['bold' => false], $this->cellHCentered);
        $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText('Đơn giá quyền sử dụng đất', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText('-', $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($this->assetPrice['asset1']['avg_price'], 0, ',', '.'), $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($this->assetPrice['asset2']['avg_price'], 0, ',', '.'), $this->styleBold, $this->cellHCentered);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($this->assetPrice['asset3']['avg_price'], 0, ',', '.'), $this->styleBold, $this->cellHCentered);
        $compares = $this->comparisonFactor1;
        $alphas = range('A', 'Z');
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
                        $this->addCompareRowDescription($table, $title, $alphas[$stt], $compare1->apartment_title, $compare1->asset_title, $compare2->asset_title, $compare3->asset_title, true);
                } else {
                    if ($type == 'quy_mo')
                        $this->addCompareRowLengh($table, $title . " ($this->m2)", $alphas[$stt], $compare1->appraise_title, $compare1->asset_title, $compare2->asset_title, $compare3->asset_title, true);
                    elseif ($type == 'chieu_rong_mat_tien' || $type == 'chieu_sau_khu_dat' || $type == 'do_rong_duong')
                        $this->addCompareRowLengh($table, $title . ' (m)', $alphas[$stt], $compare1->appraise_title, $compare1->asset_title, $compare2->asset_title, $compare3->asset_title, true);
                    else
                        $this->addCompareRowDescription($table, $title, $alphas[$stt], $compare1->appraise_title, $compare1->asset_title, $compare2->asset_title, $compare3->asset_title, true);
                }
                $this->addCompareRowExt($table,  $rateTitle, '', '-', $compare1->adjust_percent, $compare2->adjust_percent, $compare3->adjust_percent, false, '%');
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
                $this->addCompareRowExt($table, $title, $alphas[$stt], $other1->appraise_title, $other1->asset_title, $other2->asset_title, $other3->asset_title, true);
                $this->addCompareRowExt($table,  $rateTitle, '', '-', $other1->adjust_percent, $other2->adjust_percent, $other3->adjust_percent, false, '%');
                $this->addCompareRowPriceAjust($table,  $adjustTitle, '', '-', $other1->adjust_price, $other2->adjust_price, $other3->adjust_price);
                $this->addCompareRowPrice($table,  $priceAfterAdjust, '', '-', $other1->total_price, $other2->total_price, $other3->total_price);
                $stt ++;
            }
        }

        $this->getAppraisalMethod($table, $asset);
    }

    protected function getAppraisalMethod($table, $asset)
    {
        $slugValue = 'trung-binh';
        $method = $asset->appraisal->where('slug', 'thong_nhat_muc_gia_chi_dan')->first();
        $slugValue = $method->slug_value;

        if ($this->isApartment) {
            $roundData = $asset->price->where('slug', 'round_total')->first();
        } else {
            $roundData = $asset->assetPrice->where('slug', 'land_asset_purpose_' . $this->baseAcronym . '_round')->first();
        }
        $roundTotal = $roundData ? $roundData->value : 0;
        $lastCompare1 = $this->comparisonFactor1->sortBy('stt')->reverse()->first();
        $lastCompare2 = $this->comparisonFactor2->sortBy('stt')->reverse()->first();
        $lastCompare3 = $this->comparisonFactor3->sortBy('stt')->reverse()->first();
        $price1 = $lastCompare1->total_price;
        $price2 = $lastCompare2->total_price;
        $price3 = $lastCompare3->total_price;
        $minPrice = $price1;
        $maxPrice = $price1;
        $price = 0;
        if ($minPrice > $price2) $minPrice = $price2;
        if ($minPrice > $price3) $minPrice = $price3;
        if ($maxPrice < $price2) $maxPrice = $price2;
        if ($maxPrice < $price3) $maxPrice = $price3;
        $avgPrice = round(($price1 + $price2 + $price3) / 3);
        if ($slugValue == 'trung-binh') {
            $price = $avgPrice;
        } elseif ($slugValue == 'lon-nhat') {
            $price = $maxPrice;
        } else {
            $price = $minPrice;
        }
        $namePP = "trung bình của 3 TSSS";
        if (isset($slugValue) && ($slugValue == 'thap-nhat')) {
            $mgtb = $minPrice;
            $maTSSS = "";
            if ($price1 == $mgtb) {
                $maTSSS = "TSSS 1";
            } else if ($price2 == $mgtb) {
                $maTSSS = "TSSS 2";
            } else {
                $maTSSS = "TSSS 3";
            }
            $namePP = "thấp nhất của " . $maTSSS;
        }
        if (isset($slugValue) && ($slugValue == 'cao-nhat')) {
            $mgtb = $maxPrice;
            $maTSSS = "";
            if ($price1 == $mgtb) {
                $maTSSS = "TSSS 1";
            } else if ($price2 == $mgtb) {
                $maTSSS = "TSSS 2";
            } else {
                $maTSSS = "TSSS 3";
            }
            $namePP = "cao nhất của " . $maTSSS;
        }
        $diff1 = round(($price1 - $avgPrice) * 100 / $avgPrice);
        $diff2 = round(($price2 - $avgPrice) * 100 / $avgPrice);
        $diff3 = round(($price3 - $avgPrice) * 100 / $avgPrice);
        $priceRound = CommonService::roundPrice($price, $roundTotal);
        $this->total = ['price' => $price, 'method_name' => $namePP, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'avg_price' => $avgPrice, 'round_total' => $roundTotal, 'price_round' => $priceRound];
        $stt = 2;
        $this->addCompareRowPrice($table, 'Mức giá chỉ dẫn', $stt++, '-', $price1, $price2, $price3, true);
        $this->addCompareRowPrice($table, 'Mức giá trung bình của các mức giá chỉ dẫn', $stt++, $avgPrice, 0, 0, 0, false, '2-3');
        $this->addCompareRowExt($table, 'Mức độ chênh lệch với mức giá trung bình của các mức giá chỉ dẫn (%) (không quá ±15%)', $stt++, '-', $diff1, $diff2, $diff3, false, '%', '2-1');
        $this->addCompareRowPrice($table, 'Tổng giá trị điều chỉnh gộp', $stt++, '-', $lastCompare1->total_adjust_price_abs, $lastCompare2->total_adjust_price_abs, $lastCompare3->total_adjust_price_abs, false, '2-1');
        $this->addCompareRowExt($table, 'Tổng số lần điều chỉnh (lần)', $stt++, '-', $lastCompare1->adjust_times, $lastCompare2->adjust_times, $lastCompare3->adjust_times, false, '', '2-1');
        $this->addCompareRowExt($table, 'Biên độ điều chỉnh (%)', $stt++, '-', $lastCompare1->min_max, $lastCompare2->min_max, $lastCompare3->min_max, false, '', '2-1');
        $this->addCompareRowPrice($table, 'Tổng giá trị điều chỉnh thuần', $stt++, '-', $lastCompare1->total_adjust_price, $lastCompare2->total_adjust_price, $lastCompare3->total_adjust_price, false, '2-1');
        $this->addCompareRowPrice($table, 'Thống nhất mức giá chỉ dẫn', $stt++, $price, 0, 0, 0, true, '2-3');
        $this->addCompareRowPrice($table, 'Làm tròn', $stt++, $priceRound, 0, 0, 0, true, '2-3');
    }

    protected function conclusion($section, $asset)
    {
        $this->conclusion1($section);
        if ($this->isApartment)
            $this->conclusionApartment($section, $asset);
        else
            $this->conclusionAppraise($section, $asset);
    }
    protected function conclusionApartment($section, $asset)
    {
        $priceRound = number_format($this->total['price_round'], 0, ',', '.');
        $section->addText('     - Làm tròn: ' . $priceRound . 'đ/' . $this->m2);
    }
    protected function conclusion1($section)
    {
        $mgcdMin = number_format($this->total['min_price'], 0, ',', '.');
        $mgcdMax = number_format($this->total['max_price'], 0, ',', '.');
        $namePP = $this->total['method_name'];

        $price = number_format($this->total['price'], 0, ',', '.');
        $textRun = $section->addTextRun();
        $textRun->addText('❖ ', ['bold' => false]);
        $textRun->addText('Kết luận:', ['bold' => true]);
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tổng hợp các nguồn thông tin, điều chỉnh các TSSS, mức giá chênh lệch với mức giá trung bình của các mức giá chỉ dẫn không quá ±15%.', ['bold' => false]);
        $textRun = $section->addTextRun();
        $textRun->addText('     - Mức giá sau khi điều chỉnh từ các TSSS về mức giá ước tính của TSTĐ dao động từ khoảng Mức giá chỉ dẫn thấp nhất ' . $mgcdMin . 'đ/' . $this->m2 . ' đến mức giá chỉ dẫn cao nhất ' . $mgcdMax . 'đ/' . $this->m2 . '. Tổ thẩm định lựa chọn đơn giá đất sau điều chỉnh ' . $namePP . ' trên làm mức giá chỉ dẫn cho TSTĐ: ' . $price . 'đ/' . $this->m2);
    }
    protected function conclusionAppraise($section, $asset)
    {
        $mainPurpose = '';
        $main = [];
        $isMultiPurpose = false;
        $isZoning = false;
        $mgtb = $this->total['price'];
        $roundTotal = $this->total['round_total'];
        $priceRounded = $this->total['price_round'];
        if ($asset->layer_cutting_procedure) {
            $mgtbr = CommonService::roundPrice($asset->layer_cutting_price, $roundTotal);
        } else {
            $mgtbr = CommonService::roundPrice($mgtb, $roundTotal);
        }
        foreach ($asset->properties[0]->propertyDetail as $index => $item) {
            if ($item->is_transfer_facility) {
                $mainPurpose = $item->landTypePurpose->acronym;
                $firstText = "     + Đất " . $mainPurpose . ": " . number_format($mgtb, 0, ',', '.') . " đ/m";
                $secondText = "– Làm tròn: " . number_format($priceRounded, 0, ',', '.') . " đ/m";
                $textrun = $section->addTextRun('rightTab');
                $textrun->addText($firstText);
                $textrun->addText('2', ['superScript' => true]);
                $textrun->addText(" \t ");
                $textrun->addText($secondText);
                $textrun->addText('2', ['superScript' => true]);

                if ($asset->layer_cutting_procedure) {
                    $textRun = $section->addTextRun();
                    $textRun->addText('     -    Mức giá sau khi điều chỉnh theo chiều sâu: ', []);
                    $firstText = "     + Đất " . $mainPurpose . ": " . number_format($asset->layer_cutting_price, 0, ',', '.') . " đ/m";
                    $secondText = "– Làm tròn: " . number_format($mgtbr, 0, ',', '.') . " đ/m";
                    $textRun = $section->addTextRun('rightTab');
                    $textRun->addText($firstText);
                    $textRun->addText('2', ['superScript' => true]);
                    $textRun->addText(" \t ");
                    $textRun->addText($secondText);
                    $textRun->addText('2', ['superScript' => true]);
                }
                $this->gdtt[$index] = $mgtbr;
                $main = [
                    'acronym' => $mainPurpose,
                    'unit_price' => $item->circular_unit_price,
                    'price' => $mgtbr
                ];
            } else {
                $isMultiPurpose = true;
            }
            if ($item->is_zoning) {
                $isZoning = true;
            }
        }
        if ($isMultiPurpose) {
            $this->conclusionAppraiseMultiLandType($section, $asset, $main);
        }
        if ($isZoning) {
            $this->conclusionAppraiseZoning($section, $asset, $main);
        }
    }
    protected function conclusionAppraiseMultiLandType($section, $asset, $main)
    {
        $method = $asset->appraisal->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->first();
        if (!empty($method)) {
            $sttTmp = 0;
            $mgtbr = $main['price'];
            $baseAcronym = $main['acronym'];
            $mainUnitPrice = $main['unit_price'];
            foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                if (!$item->is_transfer_facility && ($item->total_area - $item->planning_area) > 0) {
                    $acronym = $item->landTypePurpose->acronym;
                    $priceData = $asset->assetPrice;
                    $donGiaDat = $this->getAssetPriceByType($priceData, 'land_asset_purpose_' . $acronym . '_price');
                    $round = $this->getAssetPriceByType($priceData, 'land_asset_purpose_' . $acronym . '_round');
                    $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                    $textRun = $section->addTextRun(['keepNext' => true]);
                    $textRun->addText('❖ ', ['bold' => false]);
                    $textRun->addText('Xác định giá đất ' . $acronym . ' thị trường:', ['bold' => true]);

                    if ($method->slug_value == 'theo-chi-phi-chuyen-mdsd-dat') {
                        $UBNDPrice = $item->circular_unit_price;
                        $cpcdmdsd = $mainUnitPrice - $UBNDPrice;
                        if ($cpcdmdsd >= 0) {
                            $formular = ' = ' . number_format($mgtbr, 0, ',', '.') . ' - ' . number_format($cpcdmdsd, 0, ',', '.') . ' = ' . number_format($mgtbr - $cpcdmdsd, 0, ',', '.') . 'đ/' . $this->m2 . '';
                            $textRun = $section->addTextRun();
                            $textRun->addText('     - Đơn giá đất ' . $acronym . ' thị trường = đơn giá đất ' . $baseAcronym . ' thị trường - chi phí chuyển MĐSD từ đất ' . $acronym . ' sang đất ' . $baseAcronym . $formular, ['bold' => false]);
                        } else {
                            $formular = ' = ' . number_format($mgtbr, 0, ',', '.') . ' + ' . number_format(abs($cpcdmdsd), 0, ',', '.') . ' = ' . number_format($mgtbr - $cpcdmdsd, 0, ',', '.') . 'đ/' . $this->m2 . '';
                            $textRun = $section->addTextRun();
                            $textRun->addText('     - Đơn giá đất ' . $acronym . ' thị trường = đơn giá đất ' . $baseAcronym . ' thị trường + chi phí chuyển MĐSD từ đất ' . $baseAcronym . ' sang đất ' . $acronym . $formular, ['bold' => false]);
                        }
                    } elseif ($method->slug_value == 'theo-ty-le-gia-dat-co-so-chinh') {
                        $resultTmp = number_format($mgtbr * $method->value / 100, 0, ',', '.');
                        $formular = '     Đất ' . $acronym . ' = ' . number_format($mgtbr, 0, ',', '.') . 'đ/' . $this->m2 . ' x ' . $method->value . '% = ' . $resultTmp . 'đ/' . $this->m2 . '';
                        if (!$sttTmp) {
                            $textRun = $section->addTextRun();
                            $textRun->addText('     - Qua khảo sát thực tế tại khu vực thẩm định giá, TTĐ nhận định Đơn giá đất CHN thị trường bằng ' . $method->value . '% đơn giá đất ' . $baseAcronym . ' thị trường là phù hợp', ['bold' => false]);
                        }
                        $textRun = $section->addTextRun();
                        $textRun->addText($formular, ['bold' => false]);
                        $sttTmp++;
                    }
                    $firstText = "     + Đất " . $acronym . ": " . number_format($donGiaDat, 0, ',', '.') . " đ/m";
                    $secondText = "– Làm tròn: " . number_format($donGiaDatRound, 0, ',', '.') . " đ/m";
                    $textrun = $section->addTextRun('rightTab');
                    $textrun->addText($firstText);
                    $textrun->addText('2', ['superScript' => true]);
                    $textrun->addText(" \t ");
                    $textrun->addText($secondText);
                    $textrun->addText('2', ['superScript' => true]);
                    if ($method->slug_value == 'theo-phuong-phap-doc-lap') {
                        $textRun = $section->addTextRun();
                        $textRun->addText('Chi tiết xem Bảng điều chỉnh Quyền sử dụng đất CLN kèm theo', ['bold' => false, 'italic' => true, 'color' => 'FF0000']);
                    }
                    $this->gdtt[$index] = $donGiaDat;
                }
            }
        }
    }
    protected function conclusionAppraiseZoning($section, $asset, $main)
    {
        $method = $asset->appraisal->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->first();
        if (!empty($method)) {
            $sttTmp = 0;
            $mgtbr = $main['price'];
            $baseAcronym = $main['acronym'];
            $mainUnitPrice = $main['unit_price'];
            $textRun = $section->addTextRun(['keepNext' => true]);
            $textRun->addText('❖ ', ['bold' => false]);
            $textRun->addText('Xác định giá đất vi phạm quy hoạch:', ['bold' => true]);

            foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                if ($item->is_zoning) {
                    $acronym = $item->landTypePurpose->acronym;
                    $positionDescription = $item->positionType->description ?? '';
                    $priceData = $asset->assetPrice;
                    $donGiaDat = $this->getAssetPriceByType($priceData, 'land_asset_purpose_' . $acronym . '_violation_price');
                    $round = $this->getAssetPriceByType($priceData, 'land_asset_purpose_' . $acronym . '_violation_round');
                    $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                    if ($method->slug_value == 'theo-gia-dat-qd-ubnd') {
                        if (!$sttTmp) {
                            $textRun = $section->addTextRun();
                            $textRun->addText('     - Phần diện tích đất thuộc ' . $item->type_zoning . ' (hạn chế khả năng sử dụng) nên ' . mb_strtoupper($this->acronym) . ' ước tính theo đơn giá đất quyết định UBND:', ['bold' => false]);
                        }
                    }
                    if ($method->slug_value == 'theo-ty-le-gia-dat-thi-truong') {
                        if (!$sttTmp) {
                            $textRun = $section->addTextRun();
                            $textRun->addText('     - Phần diện tích đất vi phạm quy hoạch (hạn chế khả năng sử dụng) nên ' . mb_strtoupper($this->acronym) . ' ước tính bằng ' . $method->value . '% đơn giá đất theo thị trường:', ['bold' => false]);
                        }
                        $priceTmp = isset($this->gdtt[$index]) ? $this->gdtt[$index] : 0;
                        $resultTmp = number_format($priceTmp * $method->value / 100, 0, ',', '.');
                        $formular = '     Đất ' . $acronym . ' = ' . number_format($priceTmp, 0, ',', '.') . 'đ/' . $this->m2 . ' x ' . $method->value . '% = ' . $resultTmp . 'đ/' . $this->m2 . '';
                        $textRun = $section->addTextRun();
                        $textRun->addText($formular, ['bold' => false]);
                    }
                    $firstText = '     + Đất ' . ($acronym ?? '') . ', ' . CommonService::mbUcfirst($positionDescription) . ': ' . number_format($donGiaDat, 0, ',', '.') . 'đ/m';
                    $secondText = "– Làm tròn: " . number_format($donGiaDatRound, 0, ',', '.') . " đ/m";
                    $textrun = $section->addTextRun('rightTab');
                    $textrun->addText($firstText);
                    $textrun->addText('2', ['superScript' => true]);
                    $textrun->addText(" \t ");
                    $textrun->addText($secondText);
                    $textrun->addText('2', ['superScript' => true]);
                    $sttTmp++;
                }
            }
        }
    }
    #region bảng thông tin tổng hợp TSTD và TSSS
    protected function getOtherFactorData($asset)
    {
        $data = [];
        $dataYTK = $this->comparisonFactors->where('type', 'yeu_to_khac');
        if (!empty($dataYTK)) {
            $stt = 0;
            foreach ($asset->assetGeneral as $item) {
                $ytks = $dataYTK->where('asset_general_id', $item->id);
                if (!empty($ytks)) {
                    foreach ($ytks as $ytk) {
                        $description = $ytk->description ?: '';
                        if ($this->isApartment)
                            $data[$ytk->position][$item->id] = ['name' => $ytk->name, 'title' => $ytk->apartment_title, 'asset_title' => $ytk->asset_title, 'description' => $description, 'adjust_percent' => $ytk->adjust_percent];
                        else
                            $data[$ytk->position][$item->id] = ['name' => $ytk->name, 'title' => $ytk->appraise_title, 'asset_title' => $ytk->asset_title, 'description' => $description, 'adjust_percent' => $ytk->adjust_percent];
                    }
                }
                $stt++;
            }
        }
        return $data;
    }
    protected function collectInfoOtherFactor($asset)
    {
        $data = [];
        $datas = $this->getOtherFactorData($asset);
        $this->otherCompare = $datas;
        foreach ($datas as $i => $other) {
            $first = current($other);
            $data[$i] = [
                0,
                $first['name'],
                $first['title'],
            ];
            $index = 3;
            foreach ($asset->assetGeneral as $item) {
                $data[$i][$index] = $other[$item->id]['asset_title'];
                $index++;
            }
            $data[$i][$index] = false;
        }
        return $data;
    }
    protected function collectInfoSource($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            'Chưa biết',
            ($this->asset1->contact_person ?: 'Liên hệ:') . "\n" . $this->asset1->contact_phone ?: '',
            ($this->asset2->contact_person ?: 'Liên hệ:') . "\n" . $this->asset2->contact_phone ?: '',
            ($this->asset3->contact_person ?: 'Liên hệ:') . "\n" . $this->asset3->contact_phone ?: '',
            false
        ];
        return $data;
    }

    protected function collectInfoSourceBy($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            ($this->asset1->source && $this->asset1->source->description) ? CommonService::mbUcfirst($this->asset1->source->description) : '-',
            ($this->asset2->source && $this->asset2->source->description) ? CommonService::mbUcfirst($this->asset2->source->description) : '-',
            ($this->asset3->source && $this->asset3->source->description) ? CommonService::mbUcfirst($this->asset3->source->description) : '-',
            false
        ];
        return $data;
    }

    protected function collectInfoSourceByApartment($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            ($this->asset1 && $this->asset1) ? CommonService::mbUcfirst($this->asset1) : '-',
            ($this->asset2 && $this->asset2) ? CommonService::mbUcfirst($this->asset2) : '-',
            ($this->asset3 && $this->asset3) ? CommonService::mbUcfirst($this->asset3) : '-',
            false
        ];
        return $data;
    }

    protected function collectInfoSourceNote($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            $this->asset1->note ? CommonService::mbUcfirst($this->asset1->note) : '-',
            $this->asset2->note ? CommonService::mbUcfirst($this->asset2->note) : '-',
            $this->asset3->note ? CommonService::mbUcfirst($this->asset3->note) : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoTransactionType($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            ($this->asset1->transaction_type && $this->asset1->transaction_type->description) ? CommonService::mbUcfirst($this->asset1->transaction_type->description) : '-',
            ($this->asset2->transaction_type && $this->asset2->transaction_type->description) ? CommonService::mbUcfirst($this->asset2->transaction_type->description) : '-',
            ($this->asset3->transaction_type && $this->asset3->transaction_type->description) ? CommonService::mbUcfirst($this->asset3->transaction_type->description) : '-',
            false
        ];
        return $data;
    }
    private function getTransactionTime($description, $publicDate)
    {
        $result = '-';
        // if ($description == 'ĐÃ BÁN') {
            $date = date_create($publicDate);
            $result = 'Tháng ' . date_format($date, 'm/Y');
        // }
        return $result;
    }
    protected function collectInfoTransactionTime($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            $this->getTransactionTime($this->asset1->transaction_type->description, $this->asset1->public_date),
            $this->getTransactionTime($this->asset2->transaction_type->description, $this->asset2->public_date),
            $this->getTransactionTime($this->asset3->transaction_type->description, $this->asset3->public_date),
            false
        ];
        return $data;
    }
    protected function collectInfoCoordinate($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            $this->getStringCoordinates($asset->coordinates),
            $this->getStringCoordinates($this->asset1->coordinates),
            $this->getStringCoordinates($this->asset2->coordinates),
            $this->getStringCoordinates($this->asset3->coordinates),
            false
        ];
        return $data;
    }
    protected function collectInfoRank($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->block && $asset->apartmentAssetProperties->block->rank && $asset->apartmentAssetProperties->block->rank->description) ? $asset->apartmentAssetProperties->block->rank->description : '-',
            ($this->asset1->block && $this->asset1->block->rank && $this->asset1->block->rank->description) ? $this->asset1->block->rank->description : '-',
            ($this->asset2->block && $this->asset2->block->rank && $this->asset2->block->rank->description) ? $this->asset2->block->rank->description : '-',
            ($this->asset3->block && $this->asset3->block->rank && $this->asset3->block->rank->description) ? $this->asset3->block->rank->description : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoApartmentFullName($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->appraise_asset) ? $asset->appraise_asset : '-'
        ];
        $index = 3;
        foreach ($asset->assetGeneral as $item) {
            $apartmentName = ($item->apartment_specification && $item->apartment_specification->apartment_name) ? 'Căn hộ ' . $item->apartment_specification->apartment_name : '';
            $floorName = ($item->floor && $item->floor->name) ? 'tầng ' . $item->floor->name : '';
            $blockName = ($item->block && $item->block->name) ? 'khu ' . $item->block->name : '';
            $projectName = ($item->project && $item->project->name) ? 'chung cư ' . $item->project->name : '';
            $data[$index] =  $apartmentName . ' ' . $floorName . ' ' . $blockName . ' ' . $projectName;
            $index++;
        }
        return $data;
    }
    protected function collectInfoAddress($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            $asset->full_address ?: '-',
            $this->asset1->full_address ?: '-',
            $this->asset2->full_address ?: '-',
            $this->asset3->full_address ?: '-',
            false
        ];
        return $data;
    }
    private function getLandAddress($item)
    {
        $landNo = isset($item->properties[0]->compare_property_doc[0]->plot_num) ? 'Thửa số: ' . $item->properties[0]->compare_property_doc[0]->plot_num . ', ' : '';
        $docNo = isset($item->properties[0]->compare_property_doc[0]->doc_num) ? 'tờ: ' . $item->properties[0]->compare_property_doc[0]->doc_num . ', ' : '';
        $address = $landNo . $docNo . $item->ward->name . ', ' . $item->district->name . ', ' . $item->province->name;
        return $address;
    }
    protected function collectInfoAddressAppraise($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            $asset->full_address ?: '-',
            $this->getLandAddress($this->asset1),
            $this->getLandAddress($this->asset2),
            $this->getLandAddress($this->asset3),
            false
        ];
        return $data;
    }
    protected function collectInfoLegal($stt, $title, $asset)
    {
        $compare1 = $this->getComparisonType($this->comparisonFactor1, 'phap_ly');
        $compare2 = $this->getComparisonType($this->comparisonFactor2, 'phap_ly');
        $compare3 = $this->getComparisonType($this->comparisonFactor3, 'phap_ly');
        $data = [
            $stt,
            $title,
            $compare1 ? CommonService::mbUcfirst($compare1->appraise_title) : '-',
            $compare1 ? CommonService::mbUcfirst($compare1->asset_title) : '-',
            $compare2 ? CommonService::mbUcfirst($compare2->asset_title) : '-',
            $compare3 ? CommonService::mbUcfirst($compare3->asset_title) : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoFloor($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->floor && $asset->apartmentAssetProperties->floor->name) ? $asset->apartmentAssetProperties->floor->name : '-',
            ($this->asset1->floor && $this->asset1->floor->name) ? $this->asset1->floor->name : '-',
            ($this->asset2->floor && $this->asset2->floor->name) ? $this->asset2->floor->name : '-',
            ($this->asset3->floor && $this->asset3->floor->name) ? $this->asset3->floor->name : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoApartmentName($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->apartment_name) ? $asset->apartmentAssetProperties->apartment_name : '-',
            ($this->asset1->apartment_specification && $this->asset1->apartment_specification->apartment_name) ? $this->asset1->apartment_specification->apartment_name : '-',
            ($this->asset2->apartment_specification && $this->asset2->apartment_specification->apartment_name) ? $this->asset2->apartment_specification->apartment_name : '-',
            ($this->asset3->apartment_specification && $this->asset3->apartment_specification->apartment_name) ? $this->asset3->apartment_specification->apartment_name : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoBedroomNum($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->bedroom_num) ? $asset->apartmentAssetProperties->bedroom_num : '-',
            ($this->asset1->room_details[0] &&  $this->asset1->room_details[0]->bedroom_num) ? $this->asset1->room_details[0]->bedroom_num : '-',
            ($this->asset2->room_details[0] &&  $this->asset2->room_details[0]->bedroom_num) ? $this->asset2->room_details[0]->bedroom_num : '-',
            ($this->asset3->room_details[0] &&  $this->asset3->room_details[0]->bedroom_num) ? $this->asset3->room_details[0]->bedroom_num : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoWcNum($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->wc_num) ? $asset->apartmentAssetProperties->wc_num : '-',
            ($this->asset1->room_details[0] && $this->asset1->room_details[0]->wc_num) ? $this->asset1->room_details[0]->wc_num : '-',
            ($this->asset2->room_details[0] && $this->asset2->room_details[0]->wc_num) ? $this->asset2->room_details[0]->wc_num : '-',
            ($this->asset3->room_details[0] && $this->asset3->room_details[0]->wc_num) ? $this->asset3->room_details[0]->wc_num : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoFurnitureQuality($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->furnitureQuality && $asset->apartmentAssetProperties->furnitureQuality->description) ? CommonService::mbUcfirst($asset->apartmentAssetProperties->furnitureQuality->description) : '-',
            ($this->asset1->room_details[0] && $this->asset1->room_details[0]->furniture_quality && $this->asset1->room_details[0]->furniture_quality->description) ? CommonService::mbUcfirst($this->asset1->room_details[0]->furniture_quality->description) : '-',
            ($this->asset2->room_details[0] && $this->asset2->room_details[0]->furniture_quality && $this->asset2->room_details[0]->furniture_quality->description) ? CommonService::mbUcfirst($this->asset2->room_details[0]->furniture_quality->description) : '-',
            ($this->asset3->room_details[0] && $this->asset3->room_details[0]->furniture_quality && $this->asset3->room_details[0]->furniture_quality->description) ? CommonService::mbUcfirst($this->asset3->room_details[0]->furniture_quality->description) : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoDescription($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->description) ? $asset->apartmentAssetProperties->description : '-',
            ($this->asset1->room_details[0] && $this->asset1->room_details[0]->description) ? $this->asset1->room_details[0]->description : '-',
            ($this->asset2->room_details[0] && $this->asset2->room_details[0]->description) ? $this->asset2->room_details[0]->description : '-',
            ($this->asset3->room_details[0] && $this->asset3->room_details[0]->description) ? $this->asset3->room_details[0]->description : '-',
            false
        ];
        return $data;
    }
    private function getUtiDescription($uti)
    {
        $strUti = '';
        if (empty($uti))
            return '';
        $utiArr = CommonService::getUtilitiesDescription($uti);
        $strUti = implode(", ", $utiArr);
        return $strUti;
    }
    protected function collectInfoUtilities($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            $this->getUtiDescription(($asset->apartmentAssetProperties && $asset->apartmentAssetProperties) ? $asset->apartmentAssetProperties->utilities : []),
            $this->getUtiDescription(($this->asset1->apartment_specification && $this->asset1->apartment_specification->utilities) ? $this->asset1->apartment_specification->utilities : []),
            $this->getUtiDescription(($this->asset1->apartment_specification && $this->asset1->apartment_specification->utilities) ? $this->asset1->apartment_specification->utilities : []),
            $this->getUtiDescription(($this->asset1->apartment_specification && $this->asset1->apartment_specification->utilities) ? $this->asset1->apartment_specification->utilities : []),
            false
        ];
        return $data;
    }

    protected function collectInfoSellingAppraisePrice($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format($this->assetPrice['asset1']['total_amount'], 0, ',', '.'),
            number_format($this->assetPrice['asset2']['total_amount'], 0, ',', '.'),
            number_format($this->assetPrice['asset3']['total_amount'], 0, ',', '.'),
            false
        ];
        return $data;
    }
    protected function collectInfoSellingPriceRate($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            $this->assetPrice['asset1']['percent'] . '%',
            $this->assetPrice['asset2']['percent'] . '%',
            $this->assetPrice['asset3']['percent'] . '%',
            false
        ];
        return $data;
    }
    protected function collectInfoProjectName($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->project && $asset->project->name) ? $asset->project->name : '-',
            ($this->asset1->project && $this->asset1->project->name) ? $this->asset1->project->name : '-',
            ($this->asset2->project && $this->asset2->project->name) ? $this->asset2->project->name : '-',
            ($this->asset3->project && $this->asset3->project->name) ? $this->asset3->project->name : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoArea($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->apartmentAssetProperties && $asset->apartmentAssetProperties->area) ? number_format($asset->apartmentAssetProperties->area, 2, ',', '.') . $this->m2 : '-',
            ($this->asset1->room_details && $this->asset1->room_details[0]) ? number_format($this->asset1->room_details[0]->area, 2, ',', '.') . $this->m2 : '-',
            ($this->asset2->room_details && $this->asset2->room_details[0]) ? number_format($this->asset2->room_details[0]->area, 2, ',', '.') . $this->m2 : '-',
            ($this->asset3->room_details && $this->asset3->room_details[0]) ? number_format($this->asset3->room_details[0]->area, 2, ',', '.') . $this->m2 : '-',
            false
        ];
        return $data;
    }
    // appraise
    protected function collectInfoAreaAppraise($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->properties && $asset->properties[0]) ? number_format($asset->properties[0]->appraise_land_sum_area, 2, ',', '.') : '-',
            ($this->asset1->total_area) ? number_format($this->asset1->total_area, 2, ',', '.')  : '-',
            ($this->asset2->total_area) ? number_format($this->asset2->total_area, 2, ',', '.')  : '-',
            ($this->asset3->total_area) ? number_format($this->asset3->total_area, 2, ',', '.')  : '-',
            true
        ];
        return $data;
    }
    protected function collectInfoAppraiseSumArea($stt, $title, $columnName)
    {
        $data =  [
            $stt,
            $title,
            !empty($this->sumArea($this->landType['appraise'], $columnName)) ? number_format($this->sumArea($this->landType['appraise'], $columnName), 2, ',', '.') : '-',
            !empty($this->sumArea($this->landType['asset1'], $columnName)) ? number_format($this->sumArea($this->landType['asset1'], $columnName), 2, ',', '.') : '-',
            !empty($this->sumArea($this->landType['asset2'], $columnName)) ? number_format($this->sumArea($this->landType['asset2'], $columnName), 2, ',', '.') : '-',
            !empty($this->sumArea($this->landType['asset3'], $columnName)) ? number_format($this->sumArea($this->landType['asset3'], $columnName), 2, ',', '.') : '-',
            true
        ];
        return $data;
    }
    private function sumArea($land, $colName)
    {
        $sum = array_sum(array_column($land, $colName));
        return $sum ?? 0;
    }
    protected function collectInfoAppraiseArea($stt, $title, $id, $columnName)
    {
        $data =  [
            $stt,
            $title,
            !empty($this->landType['appraise'][$id]) && !empty($this->landType['appraise'][$id][$columnName]) ? number_format($this->landType['appraise'][$id][$columnName], 2, ',', '.') : '-',
            !empty($this->landType['asset1'][$id]) && !empty($this->landType['asset1'][$id][$columnName]) ? number_format($this->landType['asset1'][$id][$columnName], 2, ',', '.') : '-',
            !empty($this->landType['asset2'][$id]) && !empty($this->landType['asset2'][$id][$columnName]) ? number_format($this->landType['asset2'][$id][$columnName], 2, ',', '.') : '-',
            !empty($this->landType['asset3'][$id]) && !empty($this->landType['asset3'][$id][$columnName]) ? number_format($this->landType['asset3'][$id][$columnName], 2, ',', '.') : '-',
            true
        ];
        return $data;
    }
    protected function collectInfoAppraiseUBNDPrice($stt, $title, $id, $columnName)
    {
        $data =  [
            $stt,
            $title,
            !empty($this->landType['appraise'][$id]) && !empty($this->landType['appraise'][$id][$columnName]) ? number_format($this->landType['appraise'][$id][$columnName], 0, ',', '.') : '-',
            !empty($this->landType['asset1'][$id]) && !empty($this->landType['asset1'][$id][$columnName]) ? number_format($this->landType['asset1'][$id][$columnName], 0, ',', '.') : '-',
            !empty($this->landType['asset2'][$id]) && !empty($this->landType['asset2'][$id][$columnName]) ? number_format($this->landType['asset2'][$id][$columnName], 0, ',', '.') : '-',
            !empty($this->landType['asset3'][$id]) && !empty($this->landType['asset3'][$id][$columnName]) ? number_format($this->landType['asset3'][$id][$columnName], 0, ',', '.') : '-',
            true
        ];
        return $data;
    }
    protected function collectInfoOnlyTitle($stt, $title)
    {
        $data =  [
            $stt,
            $title,
            '',
            '',
            '',
            '',
            true
        ];
        return $data;
    }
    protected function collectInfoAppraiseFrontSideWidth($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->properties[0] && $asset->properties[0]->front_side_width) ? number_format($asset->properties[0]->front_side_width, 2, ',', '.') : '-',
            ($this->asset1->properties[0] && $this->asset1->properties[0]->front_side_width) ? number_format($this->asset1->properties[0]->front_side_width, 2, ',', '.') : '-',
            ($this->asset2->properties[0] && $this->asset2->properties[0]->front_side_width) ? number_format($this->asset2->properties[0]->front_side_width, 2, ',', '.') : '-',
            ($this->asset3->properties[0] && $this->asset3->properties[0]->front_side_width) ? number_format($this->asset3->properties[0]->front_side_width, 2, ',', '.') : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseInsightWitdh($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->properties[0] && $asset->properties[0]->insight_width) ? number_format($asset->properties[0]->insight_width, 2, ',', '.'): '-',
            ($this->asset1->properties[0] && $this->asset1->properties[0]->insight_width) ? number_format($this->asset1->properties[0]->insight_width, 2, ',', '.') : '-',
            ($this->asset2->properties[0] && $this->asset2->properties[0]->insight_width) ? number_format($this->asset2->properties[0]->insight_width, 2, ',', '.') : '-',
            ($this->asset3->properties[0] && $this->asset3->properties[0]->insight_width) ? number_format($this->asset3->properties[0]->insight_width, 2, ',', '.') : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseShape($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            ($asset->properties[0] && $asset->properties[0]->landShape && $asset->properties[0]->landShape->description) ? CommonService::mbUcfirst($asset->properties[0]->landShape->description) : '-',
            ($this->asset1->properties[0] && $this->asset1->properties[0]->land_shape && $this->asset1->properties[0]->land_shape->description) ? CommonService::mbUcfirst($this->asset1->properties[0]->land_shape->description) : '-',
            ($this->asset2->properties[0] && $this->asset2->properties[0]->land_shape && $this->asset2->properties[0]->land_shape->description) ? CommonService::mbUcfirst($this->asset2->properties[0]->land_shape->description) : '-',
            ($this->asset3->properties[0] && $this->asset3->properties[0]->land_shape && $this->asset3->properties[0]->land_shape->description) ? CommonService::mbUcfirst($this->asset3->properties[0]->land_shape->description) : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseBuidingType($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            (count($asset->tangibleAssets) > 0 && $asset->tangibleAssets[0] && $asset->tangibleAssets[0]->buildingType && $asset->tangibleAssets[0]->buildingType->description) ? CommonService::mbUcfirst($asset->tangibleAssets[0]->buildingType->description) : '-',
            (count($this->asset1->tangible_assets) > 0 && $this->asset1->tangible_assets[0] && $this->asset1->tangible_assets[0]->building_type && $this->asset1->tangible_assets[0]->building_type->description) ? CommonService::mbUcfirst($this->asset1->tangible_assets[0]->building_type->description) : '-',
            (count($this->asset2->tangible_assets) > 0 && $this->asset2->tangible_assets[0] && $this->asset2->tangible_assets[0]->building_type && $this->asset2->tangible_assets[0]->building_type->description) ? CommonService::mbUcfirst($this->asset2->tangible_assets[0]->building_type->description) : '-',
            (count($this->asset3->tangible_assets) > 0 && $this->asset3->tangible_assets[0] && $this->asset3->tangible_assets[0]->building_type && $this->asset3->tangible_assets[0]->building_type->description) ? CommonService::mbUcfirst($this->asset3->tangible_assets[0]->building_type->description) : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseBuidingArea($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            (count($asset->tangibleAssets) > 0 && $asset->tangibleAssets[0] && $asset->tangibleAssets[0]->total_construction_base) ? number_format($asset->tangibleAssets[0]->total_construction_base, 2, ',', '.') : '-',
            (count($this->asset1->tangible_assets) > 0 && $this->asset1->tangible_assets[0] && $this->asset1->tangible_assets[0]->total_construction_base) ? number_format($this->asset1->tangible_assets[0]->total_construction_base, 2, ',', '.')  : '-',
            (count($this->asset2->tangible_assets) > 0 && $this->asset2->tangible_assets[0] && $this->asset2->tangible_assets[0]->total_construction_base) ? number_format($this->asset2->tangible_assets[0]->total_construction_base, 2, ',', '.')  : '-',
            (count($this->asset3->tangible_assets) > 0 && $this->asset3->tangible_assets[0] && $this->asset3->tangible_assets[0]->total_construction_base) ? number_format($this->asset3->tangible_assets[0]->total_construction_base, 2, ',', '.')  : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseBuidingRemainRate($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            (count($this->asset1->tangible_assets) > 0 && $this->asset1->tangible_assets[0] && $this->asset1->tangible_assets[0]->remaining_quality) ? $this->asset1->tangible_assets[0]->remaining_quality . '%'  : '-',
            (count($this->asset2->tangible_assets) > 0 && $this->asset2->tangible_assets[0] && $this->asset2->tangible_assets[0]->remaining_quality) ? $this->asset2->tangible_assets[0]->remaining_quality . '%'  : '-',
            (count($this->asset3->tangible_assets) > 0 && $this->asset3->tangible_assets[0] && $this->asset3->tangible_assets[0]->remaining_quality) ? $this->asset3->tangible_assets[0]->remaining_quality . '%'  : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseBuidingUnitPrice($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            (count($this->asset1->tangible_assets) > 0 && $this->asset1->tangible_assets[0] && $this->asset1->tangible_assets[0]->unit_price_m2) ? number_format($this->asset1->tangible_assets[0]->unit_price_m2, 0, ',', '.')  : '-',
            (count($this->asset2->tangible_assets) > 0 && $this->asset2->tangible_assets[0] && $this->asset2->tangible_assets[0]->unit_price_m2) ? number_format($this->asset2->tangible_assets[0]->unit_price_m2, 0, ',', '.')  : '-',
            (count($this->asset3->tangible_assets) > 0 && $this->asset3->tangible_assets[0] && $this->asset3->tangible_assets[0]->unit_price_m2) ? number_format($this->asset3->tangible_assets[0]->unit_price_m2, 0, ',', '.')  : '-',
            true
        ];
        return $data;
    }
    protected function collectInfoAppraiseBuidingPrice($stt, $title, $asset)
    {

        $data = [
            $stt,
            $title,
            '-',
            $this->assetPrice['asset1']['building_price'] ?  number_format($this->assetPrice['asset1']['building_price'], 0, ',', '.') : '-',
            $this->assetPrice['asset2']['building_price'] ?  number_format($this->assetPrice['asset2']['building_price'], 0, ',', '.') : '-',
            $this->assetPrice['asset3']['building_price'] ?  number_format($this->assetPrice['asset3']['building_price'], 0, ',', '.') : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraisePropertyDescripion($stt, $title, $asset)
    {

        $data = [
            $stt,
            $title,
            (count($asset->properties) > 0 && $asset->properties[0] && $asset->properties[0]->description) ? $asset->properties[0]->description : '-',
            (count($this->asset1->properties) > 0 && $this->asset1->properties[0] && $this->asset1->properties[0]->description) ? $this->asset1->properties[0]->description : '-',
            (count($this->asset2->properties) > 0 && $this->asset2->properties[0] && $this->asset2->properties[0]->description) ? $this->asset2->properties[0]->description : '-',
            (count($this->asset3->properties) > 0 && $this->asset3->properties[0] && $this->asset3->properties[0]->description) ? $this->asset3->properties[0]->description : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraisePropertyMaterial($stt, $title, $asset)
    {
        //Filter with all status
        $comparisonFactors = $asset->comparisonFactor;
        $comparisonFactor1 = $comparisonFactors->where('asset_general_id', $this->asset1->id);
        $comparisonFactor2 = $comparisonFactors->where('asset_general_id', $this->asset2->id);
        $comparisonFactor3 = $comparisonFactors->where('asset_general_id', $this->asset3->id);
        //Update new querry with non status to get ket_cau_duong
        $compare1 = $this->getComparisonType($comparisonFactor1, 'ket_cau_duong');
        $compare2 = $this->getComparisonType($comparisonFactor2, 'ket_cau_duong');
        $compare3 = $this->getComparisonType($comparisonFactor3, 'ket_cau_duong');
        $data = [
            $stt,
            $title,
            $compare1 ? CommonService::mbUcfirst($compare1->appraise_title) : '-',
            $compare1 ? CommonService::mbUcfirst($compare1->asset_title) : '-',
            $compare2 ? CommonService::mbUcfirst($compare2->asset_title) : '-',
            $compare3 ? CommonService::mbUcfirst($compare3->asset_title) : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseRoadWidth($stt, $title, $asset)
    {
        //Filter with all status
        $comparisonFactors = $asset->comparisonFactor;
        $comparisonFactor1 = $comparisonFactors->where('asset_general_id', $this->asset1->id);
        $comparisonFactor2 = $comparisonFactors->where('asset_general_id', $this->asset2->id);
        $comparisonFactor3 = $comparisonFactors->where('asset_general_id', $this->asset3->id);
        //Update new querry with non status to get do_rong_duong
        $compare1 = $this->getComparisonType($comparisonFactor1, 'do_rong_duong');
        $compare2 = $this->getComparisonType($comparisonFactor2, 'do_rong_duong');
        $compare3 = $this->getComparisonType($comparisonFactor3, 'do_rong_duong');
        $data = [
            $stt,
            $title,
            $compare1 ? number_format($compare1->appraise_title, 2, ',', '.') : '-',
            $compare1 ? number_format($compare1->asset_title, 2, ',', '.') : '-',
            $compare2 ? number_format($compare2->asset_title, 2, ',', '.') : '-',
            $compare3 ? number_format($compare3->asset_title, 2, ',', '.') : '-',
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseTotalEstimatePrice($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format($this->assetPrice['asset1']['total_estimate_amount'], 0, ',', '.'),
            number_format($this->assetPrice['asset2']['total_estimate_amount'], 0, ',', '.'),
            number_format($this->assetPrice['asset3']['total_estimate_amount'], 0, ',', '.'),
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseViolatePrice($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format($this->assetPrice['asset1']['change_violate_price'], 0, ',', '.'),
            number_format($this->assetPrice['asset2']['change_violate_price'], 0, ',', '.'),
            number_format($this->assetPrice['asset3']['change_violate_price'], 0, ',', '.'),
            false
        ];
        return $data;
    }

    protected function collectInfoAppraiseChangePurposePrice($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format(abs($this->assetPrice['asset1']['change_purpose_price']), 0, ',', '.'),
            number_format(abs($this->assetPrice['asset2']['change_purpose_price']), 0, ',', '.'),
            number_format(abs($this->assetPrice['asset3']['change_purpose_price']), 0, ',', '.'),
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseEstimateAmount($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format($this->assetPrice['asset1']['estimate_amount'], 0, ',', '.'),
            number_format($this->assetPrice['asset2']['estimate_amount'], 0, ',', '.'),
            number_format($this->assetPrice['asset3']['estimate_amount'], 0, ',', '.'),
            false
        ];
        return $data;
    }
    protected function collectInfoAppraiseAvgPrice($stt, $title, $asset)
    {
        $data = [
            $stt,
            $title,
            '-',
            number_format($this->assetPrice['asset1']['avg_price'], 0, ',', '.'),
            number_format($this->assetPrice['asset2']['avg_price'], 0, ',', '.'),
            number_format($this->assetPrice['asset3']['avg_price'], 0, ',', '.'),
            false
        ];
        return $data;
    }

    //
    #endregion

    protected function getApartmentData()
    {
        $result = [];
        if (is_countable($this->realEstates)) {
            foreach ($this->realEstates as $realEstate) {
                $asset = $realEstate->apartment;
                $asset->assetGeneral = $asset->asset_general;
                $result[] = $asset;
            }
        } else {
            $asset = $this->realEstates->apartment;
            $asset->assetGeneral = $asset->asset_general;
            $result[] = $asset;
        }

        return $result;
    }
    protected function getAppraiseData()
    {
        $result = [];
        if (is_countable($this->realEstates)) {
            foreach ($this->realEstates as $realEstate) {
                $asset = $realEstate->appraises;
                $asset->assetGeneral = $asset->asset_general;
                $result[] = $asset;
            }
        } else {
            $asset = $this->realEstates->appraises;
            $asset->assetGeneral = $asset->asset_general;
            $result[] = $asset;
        }
        return $result;
    }

    protected function signature(Section $section, $certificate)
    {
        ////không có chữ ký
    }
    private function addCompareRowPriceAjust($table, $title, $alpha, $col1, $col2, $col3, $col4, $isBold = false)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ($alpha == '') ? $this->cellRowContinue : $this->cellRowSpan)->addText($alpha, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText($title, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($col1, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col2, 0, ',', '.'), ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col3, 0, ',', '.'), ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col4, 0, ',', '.'), ['bold' => $isBold], $this->cellHCenteredKeepNext);
    }
    private function addCompareRowPrice($table, $title, $alpha, $col1, $col2, $col3, $col4, $isBold = false, $panType = '1-1')
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ($alpha == '') ? $this->cellRowContinue : $this->cellRowSpan)->addText($alpha, ['bold' => $isBold], $this->cellHCentered);
        if ($panType == '2-1') {
            $table->addCell($this->columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText($title, ['bold' => $isBold], ['align' => 'left']);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col2, 0, ',', '.'), ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col3, 0, ',', '.'), ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col4, 0, ',', '.'), ['bold' => $isBold], $this->cellHCentered);
        } elseif ($panType == '2-3') {
            $table->addCell($this->columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText($title, ['bold' => $isBold], ['align' => 'left']);
            $table->addCell($this->columnWidthFourth, ['gridSpan' => 3, 'valign' => 'center'])->addText(number_format($col1, 0, ',', '.'), null, $this->cellHCentered);
        } else {
            $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText($title, ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($col1, ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col2, 0, ',', '.'), ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col3, 0, ',', '.'), ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col4, 0, ',', '.'), ['bold' => $isBold], $this->cellHCentered);
        }
    }
    private function addCompareRowExt($table, $title, $alpha, $col1, $col2, $col3, $col4, $isBold = false, $ext = '', $panType = '1-1')
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ($alpha == '') ? $this->cellRowContinue : $this->cellRowSpan)->addText($alpha, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        if ($panType == '2-1') {
            $table->addCell($this->columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText($title, ['bold' => $isBold], ['align' => 'left']);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($col2 . $ext, ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($col3 . $ext, ['bold' => $isBold], $this->cellHCentered);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($col4 . $ext, ['bold' => $isBold], $this->cellHCentered);
        } elseif ($panType == '2-3') {
            $table->addCell($this->columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText($title, ['bold' => $isBold], ['align' => 'left']);
            $table->addCell($this->columnWidthFourth, ['gridSpan' => 3, 'valign' => 'center'])->addText($col1 . $ext, null, $this->cellHCentered);
        } else {
            $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText($title, ['bold' => $isBold],$this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText($col1 != '-' ?  $col1 . $ext : $col1, null,$this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText( $col2 . $ext, null,$this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText( $col3 . $ext, null,$this->cellHCenteredKeepNext);
            $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText( $col4 . $ext, null,$this->cellHCenteredKeepNext);
        }
    }
    private function addCompareRowDescription($table, $title, $alpha, $col1, $col2, $col3, $col4, $isBold = false, $ext = '')
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ($alpha == '') ? $this->cellRowContinue : $this->cellRowSpan)->addText($alpha, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText($title, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(CommonService::mbUcfirst($col1), null, $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(CommonService::mbUcfirst($col2) . $ext, null, $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(CommonService::mbUcfirst($col3) . $ext, null, $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(CommonService::mbUcfirst($col4) . $ext, null, $this->cellHCenteredKeepNext);
    }
    private function addCompareRowLengh($table, $title, $alpha, $col1, $col2, $col3, $col4, $isBold = false)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ($alpha == '') ? $this->cellRowContinue : $this->cellRowSpan)->addText($alpha, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthFirst, $this->cellVCentered)->addText($title, ['bold' => $isBold], $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(is_numeric($col1) ? number_format($col1, 2, ',', '.') : '-', null, $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col2, 2, ',', '.') , null, $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col3, 2, ',', '.') , null, $this->cellHCenteredKeepNext);
        $table->addCell($this->columnWidthSecond, $this->cellVCentered)->addText(number_format($col4, 2, ',', '.') , null, $this->cellHCenteredKeepNext);
    }
    private function setLandTypeData($id, $acronym, $isTransfer, $totalArea, $planinngArea, $mainArea, $price)
    {
        $data = [
            'id' => $id,
            'acronym' => $acronym,
            'is_transfer_facility' => $isTransfer,
            'total_area' => $totalArea,
            'planning_area' => $planinngArea,
            'main_area' => $mainArea,
            'price' => $price,
        ];
        return $data;
    }
    private function getAssetPriceByType($price, $type)
    {
        $data = $price->where('slug', $type)->first();
        $result = $data ? $data->value : 0;
        return $result;
    }
}
