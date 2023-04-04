<?php
namespace App\Services\Excel;

use App\Http\ResponseTrait;
use App\Services\CommonService;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Rap2hpoutre\FastExcel\FastExcel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportShinhanCertificateAsset
{
    use ResponseTrait;

    private $fontName = 'Arial';
    private $fontSize = 10;

    public function exportAsset($realEstate){
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'certification_assets/' . $now->format('Y') . '/' . $now->format('m') . '/';

        if(!File::exists(storage_path('app/public/'. $path))){
            File::makeDirectory(storage_path('app/public/'. $path), 0755, true);
        }
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $id = $realEstate->id;
        $fileName = 'TSTĐ' . '_' . $id . '_Shinhan_' . $downloadTime . '_' . $downloadDate .'.xlsx';
        $filePath = $path. '/'. $fileName;

        $data = $this->dataGenerator($realEstate);
        $rows_style = (new StyleBuilder())
        ->setFontName($this->fontName)
        ->setFontSize($this->fontSize)
        ->build();
        if ($data) {
            (new FastExcel($data))
            ->rowsStyle($rows_style)
            ->export(storage_path('app/public/'. $filePath) ,function($data){
                return [
                    'Index' => $data[0],
                    'Group' => $data[1],
                    'GroupIndex' => $data[2],
                    'Field' => $data[3],
                    'Value' => $data[4],
                    'Tab' => $data[5],
                    'Tiếng Việt' => $data[6],
                ];}
            );
        }
        $this->formatExcel($filePath);
        $file = [];
        $file['url'] = Storage::disk('public')->url($filePath);
        $file['file_name'] = $fileName;
        return $file;
    }
    private function dataGenerator($realEstate) {
        //Generate for land and tangible
        $asset = $realEstate->appraises;
        $apartment = $realEstate->apartment;
        $certificate = $realEstate->certificate;
        $validLandPurpose = ['ODT', 'ONT'];
        $coordinates = explode(',', $realEstate->coordinates);
        $data = [];
        $estimateUnit = 0;
        $validArea = 0;
        $estimateLandTotal = 0;
        $property = null;
        $primaryStreetFrontage = '';
        $accessWidth = '';
        $addressDescription ='';

        if ($asset) {
            $appraisePrice = $asset->assetPrice;
            $property = $asset->properties->first();
            $road_width = $asset->comparisonFactor->where('type', 'do_rong_duong')->first();
            $primaryStreetFrontage = $property ? $property->description : '';
            $accessWidth = $road_width ? $road_width->appraise_title : '';
            $addressDescription = CommonService::mbUcfirst($this->getToThua($asset)) . ', ' . CommonService::mbCaseTitle($asset->ward->name) . ', ' . CommonService::mbCaseTitle($asset->district->name) . ', ' . CommonService::mbCaseTitle($asset->province->name) . '.';
            foreach($property->propertyDetail as $detail) {
                if (in_array($detail->landTypePurpose->acronym, $validLandPurpose)) {
                    $acronym = $detail->landTypePurpose->acronym;
                    $_estimateUnit = $appraisePrice->where('slug','land_asset_purpose_' . $acronym . '_price')->first();
                    $_roundLandUnit = $appraisePrice->where('slug','land_asset_purpose_' . $acronym . '_round')->first();
                    $_validArea = $appraisePrice->where('slug','land_asset_purpose_' . $acronym . '_area')->first();
                    $roundLandUnit = $_roundLandUnit ? $_roundLandUnit->value : 0;
                    $estimateUnit = $_estimateUnit ? CommonService::roundPrice($_estimateUnit->value, $roundLandUnit) : 0;
                    $validArea = $_validArea ? $_validArea->value : 0;
                    $estimateLandTotal += $estimateUnit * $validArea;
                    break; //Only 1 valid purpose is allowed in 1 propety
                }
            }
        } else if ($apartment) {
            $apartmentPrice = $apartment->price;
            $addressDescription = $apartment->full_address;
            $_validArea = $apartmentPrice->where('slug','apartment_area')->first();
            $_estimateUnit = $apartmentPrice->where('slug','apartment_asset_price')->first();
            $_estimateTotal = $apartmentPrice->where('slug','apartment_total_price')->first();
            $_roundUnit = $apartmentPrice->where('slug','round_total')->first();
            $_roundTotal = $apartmentPrice->where('slug','apartment_round_total')->first();
            $_validArea = $apartmentPrice->where('slug','apartment_area')->first();

            $validArea = $_validArea ? $_validArea->value : 0;
            $roundUnit = $_roundUnit ? $_roundUnit->value : 0;
            $roundTotal = $_roundTotal ? $_roundTotal->value : 0;
            $estimateUnit = $_estimateUnit ? CommonService::roundPrice($_estimateUnit->value, $roundUnit) : 0;
            $estimateLandTotal = $_estimateTotal ? CommonService::roundPrice($_estimateTotal->value, $roundTotal) : 0;
        }

        $data[] = ['', 'OINFORMATION', 1, 'AppraiserName', $realEstate->createdBy->name, 'OFFICIAL APPRAISAL INFO', 'Nhân viên thẩm định'];
        $data[] = ['', 'OINFORMATION', 1, 'AppraiserPhone', $realEstate->createdBy->phone, 'OFFICIAL APPRAISAL INFO', 'Số điện thoại nhân viên thẩm định'];
        $data[] = ['', 'OINFORMATION', 1, 'ReportNo', $certificate ? $certificate->certificate_num : '', 'OFFICIAL APPRAISAL INFO', 'Chứng thư TĐG số'];
        $data[] = ['', 'OINFORMATION', 1, 'AppraisalDate', $certificate ? date('d/m/Y', strtotime($certificate->appraise_date)) : '', 'OFFICIAL APPRAISAL INFO', 'Ngày TĐG'];
        $data[] = ['', 'OINFORMATION', 1, 'InstructorName', $realEstate->contact_person, 'OFFICIAL APPRAISAL INFO', 'Người hướng dẫn khảo sát'];
        $data[] = ['', 'OINFORMATION', 1, 'InstructorPhone', $realEstate->contact_phone, 'OFFICIAL APPRAISAL INFO', 'Số điện thoại người hướng dẫn'];
        $data[] = ['', 'OINFORMATION', 1, 'AssetAddress', $addressDescription, 'OFFICIAL APPRAISAL INFO', 'Địa chỉ tài sản'];
        // $data[] = ['', 'OINFORMATION', 1, 'AssetAddress', $asset->full_address, 'OFFICIAL APPRAISAL INFO', 'Địa chỉ tài sản'];
        $data[] = ['', 'OINFORMATION', 1, 'PrimaryStreetFrontage', $primaryStreetFrontage, 'OFFICIAL APPRAISAL INFO', 'Mặt tiền chính tiếp giáp'];
        $data[] = ['', 'OINFORMATION', 1, 'AccessWidth', $accessWidth, 'OFFICIAL APPRAISAL INFO', 'Độ rộng đường tiếp giáp (m)'];
        $data[] = ['', 'OINFORMATION', 1, 'OwnerName', '', 'OFFICIAL APPRAISAL INFO', 'Chủ tài sản'];
        $data[] = ['', 'OINFORMATION', 1, 'BuildingAge', '', 'OFFICIAL APPRAISAL INFO', 'Tuổi công trình'];
        $data[] = ['', 'OINFORMATION', 1, 'Latitude', count($coordinates) > 1 ? trim($coordinates[0]) : '', 'OFFICIAL APPRAISAL INFO', 'Vĩ độ GPS'];
        $data[] = ['', 'OINFORMATION', 1, 'Longitude', count($coordinates) > 1 ? trim($coordinates[1]) : '', 'OFFICIAL APPRAISAL INFO', 'Kinh độ GPS'];
        $data[] = ['', 'OINFORMATION', 1, 'AmplitudeRemark', '', 'OFFICIAL APPRAISAL INFO', 'Ghi chú khác'];
        $data[] = ['', 'OINFORMATION', 1, 'PlanningInfo', $realEstate->planning_info, 'OFFICIAL APPRAISAL INFO', 'Thông tin quy hoạch'];
        $data[] = ['', 'OINFORMATION', 1, 'Source', $realEstate->planning_source, 'OFFICIAL APPRAISAL INFO', 'Nguồn thông tin'];
        for ($index = 1; $index < 4; $index++) {
            if ($index === 1) {
                $landTypePurpose = '';
                if ($property) {
                    foreach($property->propertyDetail as $detail) {
                        if ($landTypePurpose === '')
                            $landTypePurpose = $detail->landTypePurpose->acronym;
                        else
                            $landTypePurpose .= ' + ' . $detail->landTypePurpose->acronym;
                    }
                }
                $data[] = ['', 'IORESULT', $index, 'Type', '', 'OFFICIAL RESULT', 'Hạng mục'];
                $data[] = ['', 'IORESULT', $index, 'Zone', $realEstate->planning_info, 'OFFICIAL RESULT', 'Quy hoạch'];
                $data[] = ['', 'IORESULT', $index, 'Usage', $landTypePurpose, 'OFFICIAL RESULT', 'Mục đích sử dụng'];
                $data[] = ['', 'IORESULT', $index, 'Structure', '', 'OFFICIAL RESULT', 'Kết cấu'];
                $data[] = ['', 'IORESULT', $index, 'AreaQuantity', $validArea, 'OFFICIAL RESULT', 'Diện tích (m2) / Số lượng'];
                $data[] = ['', 'IORESULT', $index, 'UnitPrice', $estimateUnit, 'OFFICIAL RESULT', 'Đơn giá (VNĐ)'];
                $data[] = ['', 'IORESULT', $index, 'Amount', $estimateLandTotal, 'OFFICIAL RESULT', 'Giá trị (VNĐ)'];
            } else {
                $data[] = ['', 'IORESULT', $index, 'Type', '', 'OFFICIAL RESULT', 'Hạng mục'];
                $data[] = ['', 'IORESULT', $index, 'Zone', '', 'OFFICIAL RESULT', 'Quy hoạch'];
                $data[] = ['', 'IORESULT', $index, 'Usage', '', 'OFFICIAL RESULT', 'Mục đích sử dụng'];
                $data[] = ['', 'IORESULT', $index, 'Structure', '', 'OFFICIAL RESULT', 'Kết cấu'];
                $data[] = ['', 'IORESULT', $index, 'AreaQuantity', '', 'OFFICIAL RESULT', 'Diện tích (m2) / Số lượng'];
                $data[] = ['', 'IORESULT', $index, 'UnitPrice', '', 'OFFICIAL RESULT', 'Đơn giá (VNĐ)'];
                $data[] = ['', 'IORESULT', $index, 'Amount', '', 'OFFICIAL RESULT', 'Giá trị (VNĐ)'];
            }
        }
        $data[] = ['', 'IORESULT_SUM', 1, 'TotalValue', $estimateLandTotal, 'OFFICIAL RESULT', 'Tổng cộng (VNĐ)'];
        // If Realestate is not Apartment
        if($asset) {
            $index = 1;
            $comparisonAssets = $asset->asset_general;
            $data[] = ['', 'OCOMPASSET', $index, 'ComparableAssetNo', '', 'COMPARABLE ASSETS', ''];
            foreach ($comparisonAssets as $cAsset) {
                $tangible = null;
                if ($cAsset->tangible_assets && count($cAsset->tangible_assets) > 0)
                    $tangible = $cAsset->tangible_assets[0]; // Comparision asset only allow 1 tangible

                $unitArea = $asset->assetUnitArea->where('asset_general_id', $cAsset->id);
                $mainArea = 0;
                if ($cAsset->properties && $cAsset->properties[0]->property_detail) {
                    foreach($cAsset->properties[0]->property_detail as $detail) {
                        $violateArea = $unitArea->where('land_type_id', $detail->land_type_purpose)->first();
                        $planinngArea = $violateArea ? $violateArea->violation_asset_area : 0;
                        $mainArea += $detail->total_area - $planinngArea;
                    }
                }

                $adapter = $asset->appraiseAdapter->where('asset_general_id', $cAsset->id)->first();
                $adjustPercent = $adapter->percent ? $adapter->percent : $cAsset->adjust_percent;
                $adjustPercent = (floatval($adjustPercent))/100;
                $priceAfterAdjust = $adjustPercent * $cAsset->total_amount;

                $constructionPrice = floatval($cAsset->total_construction_amount);
                $purposePrice = floatval($adapter->change_purpose_price);
                $violatePrice = floatval($adapter->change_violate_price);

                $estimateMainPurposePrice = 0;
                if ($mainArea > 0)
                    $estimateMainPurposePrice = round(($priceAfterAdjust - $constructionPrice + $purposePrice - $violatePrice) / $mainArea);

                $data[] = ['', 'OCOMPASSET', $index, 'ProvinceCity', CommonService::mbCaseTitle($cAsset->province->name), 'COMPARABLE ASSETS', 'Tỉnh/TP'];
                $data[] = ['', 'OCOMPASSET', $index, 'District', CommonService::mbCaseTitle($cAsset->district->name), 'COMPARABLE ASSETS', 'Quận/Huyện'];
                $data[] = ['', 'OCOMPASSET', $index, 'WardCommune', CommonService::mbCaseTitle($cAsset->ward->name), 'COMPARABLE ASSETS', 'Phường/Xã'];
                $data[] = ['', 'OCOMPASSET', $index, 'Street', CommonService::mbCaseTitle($cAsset->street->name), 'COMPARABLE ASSETS', 'Đường'];
                $data[] = ['', 'OCOMPASSET', $index, 'UnitNo', '', 'COMPARABLE ASSETS', 'Số nhà/Số căn hộ'];
                $data[] = ['', 'OCOMPASSET', $index, 'ContactName', $cAsset->contact_person, 'COMPARABLE ASSETS', 'Người cung cấp thông tin'];
                $data[] = ['', 'OCOMPASSET', $index, 'ContactPhone', $cAsset->contact_phone, 'COMPARABLE ASSETS', 'Số điện thoại'];
                $data[] = ['', 'OCOMPASSET', $index, 'TransactionType', CommonService::mbCaseTitle($cAsset->transaction_type->description), 'COMPARABLE ASSETS', 'Loại giao dịch'];
                $data[] = ['', 'OCOMPASSET', $index, 'AskingTransactionDate', date('d/m/Y', strtotime($cAsset->public_date)), 'COMPARABLE ASSETS', 'Ngày rao bán / giao dịch'];
                $data[] = ['', 'OCOMPASSET', $index, 'LandArea', $cAsset->total_area, 'COMPARABLE ASSETS', 'Diện tích đất (m2)'];
                $data[] = ['', 'OCOMPASSET', $index, 'BuildingArea', $tangible ? $tangible->total_construction_base : '', 'COMPARABLE ASSETS', 'Diện tích CTXD (m2)'];
                $data[] = ['', 'OCOMPASSET', $index, 'ApartmentArea', '', 'COMPARABLE ASSETS', 'Diện tích căn hộ (m2)'];
                $data[] = ['', 'OCOMPASSET', $index, 'Level', '', 'COMPARABLE ASSETS', 'Tầng'];
                $data[] = ['', 'OCOMPASSET', $index, 'NoOfFloors', $tangible ? $tangible->floor : '', 'COMPARABLE ASSETS', 'Số tầng'];
                $data[] = ['', 'OCOMPASSET', $index, 'ConstructionUnitPrice', $tangible ? $tangible->unit_price_m2 : '', 'COMPARABLE ASSETS', 'Đơn giá xây dựng (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'RQR', $tangible ? $tangible->remaining_quality/100 : '', 'COMPARABLE ASSETS', 'CLCL (%)'];
                $data[] = ['', 'OCOMPASSET', $index, 'ConstructionValue', $tangible ? intval($tangible->estimation_value) : '', 'COMPARABLE ASSETS', 'Giá trị CTXD (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'AskingTransactionPrice',  $cAsset->total_amount, 'COMPARABLE ASSETS', 'Giá rao bán/Giá giao dịch (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'ReliabilityLevel', $adjustPercent, 'COMPARABLE ASSETS', 'Tỷ lệ giao dịch thực tế (%)'];
                $data[] = ['', 'OCOMPASSET', $index, 'PriceAfterReliabilityAdjustment', $priceAfterAdjust, 'COMPARABLE ASSETS', 'Giá rao bán/Giá giao dịch sau điều chỉnh (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'UnitPrice', $estimateMainPurposePrice, 'COMPARABLE ASSETS', 'Đơn giá QSDĐ/QSHCH (VNĐ)'];
                $index += 1;
            }
        } else if ($apartment) {
            $index = 1;
            $comparisonAssets = $apartment->asset_general;
            $data[] = ['', 'OCOMPASSET', $index, 'ComparableAssetNo', '', 'COMPARABLE ASSETS', ''];
            foreach ($comparisonAssets as $cAsset) {
                $mainArea = $cAsset->room_details[0] ? $cAsset->room_details[0]->area : 0;
                $adapter = $apartment->apartmentAdapter->where('asset_general_id', $cAsset->id)->first();
                $adjustPercent = $adapter->percent ? $adapter->percent : $cAsset->adjust_percent;
                $adjustPercent = (floatval($adjustPercent))/100;
                $priceAfterAdjust = $adjustPercent * $cAsset->total_amount;
                $estimateMainPurposePrice = 0;
                if ($mainArea > 0)
                    $estimateMainPurposePrice = round($priceAfterAdjust / $mainArea);
                $data[] = ['', 'OCOMPASSET', $index, 'ProvinceCity', isset($cAsset->project->province) ? CommonService::mbCaseTitle($cAsset->project->province->name) : '', 'COMPARABLE ASSETS', 'Tỉnh/TP'];
                $data[] = ['', 'OCOMPASSET', $index, 'District', isset($cAsset->project->district) ? CommonService::mbCaseTitle($cAsset->project->district->name) : '', 'COMPARABLE ASSETS', 'Quận/Huyện'];
                $data[] = ['', 'OCOMPASSET', $index, 'WardCommune', isset($cAsset->project->ward) ? CommonService::mbCaseTitle($cAsset->project->ward->name) : '', 'COMPARABLE ASSETS', 'Phường/Xã'];
                $data[] = ['', 'OCOMPASSET', $index, 'Street', isset($cAsset->project->street) ? CommonService::mbCaseTitle($cAsset->project->street->name) : '', 'COMPARABLE ASSETS', 'Đường'];
                $data[] = ['', 'OCOMPASSET', $index, 'UnitNo', '', 'COMPARABLE ASSETS', 'Số nhà/Số căn hộ'];
                $data[] = ['', 'OCOMPASSET', $index, 'ContactName', $cAsset->contact_person, 'COMPARABLE ASSETS', 'Người cung cấp thông tin'];
                $data[] = ['', 'OCOMPASSET', $index, 'ContactPhone', $cAsset->contact_phone, 'COMPARABLE ASSETS', 'Số điện thoại'];
                $data[] = ['', 'OCOMPASSET', $index, 'TransactionType', CommonService::mbCaseTitle($cAsset->transaction_type->description), 'COMPARABLE ASSETS', 'Loại giao dịch'];
                $data[] = ['', 'OCOMPASSET', $index, 'AskingTransactionDate', date('d/m/Y', strtotime($cAsset->public_date)), 'COMPARABLE ASSETS', 'Ngày rao bán / giao dịch'];
                $data[] = ['', 'OCOMPASSET', $index, 'LandArea', '', 'COMPARABLE ASSETS', 'Diện tích đất (m2)'];
                $data[] = ['', 'OCOMPASSET', $index, 'BuildingArea', '', 'COMPARABLE ASSETS', 'Diện tích CTXD (m2)'];
                $data[] = ['', 'OCOMPASSET', $index, 'ApartmentArea', $mainArea, 'COMPARABLE ASSETS', 'Diện tích căn hộ (m2)'];
                $data[] = ['', 'OCOMPASSET', $index, 'Level', $cAsset->floor ? $cAsset->floor->name : '', 'COMPARABLE ASSETS', 'Tầng'];
                $data[] = ['', 'OCOMPASSET', $index, 'NoOfFloors', '', 'COMPARABLE ASSETS', 'Số tầng'];
                $data[] = ['', 'OCOMPASSET', $index, 'ConstructionUnitPrice', '', 'COMPARABLE ASSETS', 'Đơn giá xây dựng (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'RQR', '', 'COMPARABLE ASSETS', 'CLCL (%)'];
                $data[] = ['', 'OCOMPASSET', $index, 'ConstructionValue', '', 'COMPARABLE ASSETS', 'Giá trị CTXD (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'AskingTransactionPrice',  $cAsset->total_amount, 'COMPARABLE ASSETS', 'Giá rao bán/Giá giao dịch (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'ReliabilityLevel', $adjustPercent, 'COMPARABLE ASSETS', 'Tỷ lệ giao dịch thực tế (%)'];
                $data[] = ['', 'OCOMPASSET', $index, 'PriceAfterReliabilityAdjustment', $priceAfterAdjust, 'COMPARABLE ASSETS', 'Giá rao bán/Giá giao dịch sau điều chỉnh (VNĐ)'];
                $data[] = ['', 'OCOMPASSET', $index, 'UnitPrice', $estimateMainPurposePrice, 'COMPARABLE ASSETS', 'Đơn giá QSDĐ/QSHCH (VNĐ)'];
                $index += 1;
            }
        }
        return $data;
    }
    private function formatExcel($filePath)
    {
        $reader= new Xlsx();
        $spreadSheet= new Spreadsheet();
        $spreadSheet= $reader->load(storage_path('app/public/'. $filePath));
        $spreadSheet->setActiveSheetIndex(0);
        $activeSheet = $spreadSheet->getActiveSheet();

        $activeSheet->getColumnDimension('A')->setWidth(6);
        $activeSheet->getColumnDimension('B')->setWidth(14);
        $activeSheet->getColumnDimension('C')->setWidth(10);
        $activeSheet->getColumnDimension('D')->setWidth(25);
        $activeSheet->getColumnDimension('E')->setWidth(40);
        $activeSheet->getColumnDimension('F')->setWidth(22);
        $activeSheet->getColumnDimension('G')->setWidth(36);

        $colFormatDate = ['AppraisalDate', 'AskingTransactionDate'];
        $colFormatCurrency = ['UnitPrice', 'Amount', 'TotalValue', 'NoOfFloors', 'ConstructionUnitPrice', 'ConstructionValue', 'AskingTransactionPrice', 'PriceAfterReliabilityAdjustment', 'UnitPrice'];
        $colFormatNumber = ['AccessWidth', 'AreaQuantity', 'LandArea', 'BuildingArea', 'ApartmentArea'];
        $colFormatPercent = ['RQR', 'ReliabilityLevel'];

        $formatCellCondition = [];
        $formatCellCondition += (array_fill_keys($colFormatDate, NumberFormat::FORMAT_DATE_DDMMYYYY));
        $formatCellCondition += (array_fill_keys($colFormatCurrency, '###,###'));
        $formatCellCondition += (array_fill_keys($colFormatNumber, '###,##0.00'));
        $formatCellCondition += (array_fill_keys($colFormatPercent, '0%'));

        $index = 1;
        while ($index < 150)
        {
            $cellValue = strval($activeSheet->getCell([4, $index])->getValue());
            if ($cellValue && key_exists($cellValue, $formatCellCondition))
                $activeSheet->getCell([5, $index])->getStyle()->getNumberFormat()->setFormatCode($formatCellCondition[$cellValue]);
            $index++;
            if (!$cellValue) break;
        }
        $objWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');
        $objWriter->save(storage_path('app/public/'. $filePath ));
        //Cleanup
        $spreadSheet->disconnectWorksheets();
        unset($spreadSheet);
    }
    protected function getToThua($appraise)
    {
        if (!$appraise) return '';
        $stoSthua = [];
        $strToThua = '';
        $appraiseLaw = $appraise->appraiseLaw;
        if (!empty($appraiseLaw) && count($appraiseLaw))
            foreach ($appraiseLaw as $appraiseLaw) {
                if (isset($appraiseLaw->landDetails)) {
                    foreach ($appraiseLaw->landDetails as $item) {
                        if (isset($item->doc_no) & isset($item->land_no))
                            $stoSthua[$item->doc_no][] = $item->land_no;
                    }
                }
            }
            $isFirst = true;
            if (!empty($stoSthua)) {
                foreach ($stoSthua as $docNo => $landNos) {
                    if (!$isFirst) {
                        $strToThua .= ", ";
                    }
                    $isFirst = false;
                    $landNos = array_unique($landNos);
                    $strToThua .= "thửa đất số " . implode(", ", $landNos) . " tờ bản đồ số " . $docNo;
                }
            }
        return $strToThua;
    }
}
