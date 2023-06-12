<?php
namespace App\Services\Document\Appraisal;

use App\Services\CommonService;
use App\Services\Document\DocumentInterface\Report;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;

class ReportAppraisal extends Report
{
    protected $isOnlyAsset = false;
    protected $realEstates = [];
    protected $isTangibleAsset = false;
    protected $isApartment = false;
    protected float $rowThirdWidth;
    protected float $rowFourthWidth;
    protected $locationDescription = '- Khả năng tiếp cận';
    protected $statusDescription = '- Công trình';
    protected string $principleOfValuationDescription = 'Các nguyên tắc khác (TĐGVN 04 Ban hành kèm theo thông tư số 158/2014/TT-BTC ngày 27/10/2014 của Bộ trưởng Bộ Tài Chính).';
    public function getFooterString($data)
    {
        $data = (Object)$data;
        $comAcronym = mb_strtoupper($this->companyAcronym);
        $createdName =  $this->createdName;
        if(isset($data->document_date)&&!empty(trim($data->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $data->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        $reportID = 'HSTD_'. $data->id;
        return mb_strtoupper($this->envDocument)  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID;
    }
    public function getReportName()
    {
        return '2_BC';
    }
    public function printContent(Section $section, $data)
    {
        $this->step1($section, $data);
        $this->step2($section, $data);
        $this->step3($section, $data);
        $this->step4($section, $data);
        $this->step5($section, $data);
        $this->step6($section, $data);
        $this->step7($section, $data);
        $this->step8($section, $data);
        $this->step9($section, $data);
        $this->step10($section, $data);
        $this->printAppendix($section, $data);
        // end //
        // $this->signature($section, $data);
    }
    private function setProperties($data)
    {
        $this->isOnlyAsset = (count($data->realEstate) == 1);
        $this->realEstates = $data->realEstate;
        $this->isTangibleAsset = in_array('DCN', $data->document_type);
        $this->isApartment = in_array('CC', $data->document_type);
        $this->rowThirdWidth = Converter::inchToTwip(2);
        $this->rowFourthWidth = Converter::inchToTwip(4);

    }
    public function printTitle(Section $section, $data)
    {
        $this->setProperties($data);
        $table2 = $section->addTable($this->tableBasicStyle);
        $table2->addRow(Converter::inchToTwip(.1));
        $cell21 = $table2->addCell(Converter::inchToTwip(2.5));
        $cell21->addText('Số: ' . $this->reportCode, null, array_merge($this->styleAlignCenter, ['spaceBefore' => 200]));
        $cell22 = $table2->addCell(Converter::inchToTwip(4));
        $cell22->addText(ucfirst($this->certificateLongDateText), null, array_merge($this->styleAlignRight, ['spaceBefore' => 200]));
        $section->addText("BÁO CÁO KẾT QUẢ THẨM ĐỊNH GIÁ", ['bold' => true, 'size' => '18'], array_merge($this->styleAlignCenter, ['spaceBefore' => 320]));
        $section->addText('(Kèm theo Chứng thư Thẩm định giá số ' . $this->certificateCode .', ngày ' . $this->certificateShortDateText . ')', ['italic' => true], ['align' => 'center', 'spaceAfter' => 300]);
    }
    protected function getAssetName($certificate)
    {
        $name_assets = '';
        $assets = $this->realEstates;
        foreach ($assets as $index => $item) {
            $name_assets .= ($index > 0) ? " và " : "";
            if ($item->assetType->acronym == 'CC') {
                // $apartmentName = $item->apartment->apartmentAssetProperties->full_name ?: '';
                $apartmentName = '';
                // if (!empty($item->apartment->apartmentAssetProperties)) {
                //     $apartmentName = $item->apartment->apartmentAssetProperties->full_name ?: '';
                // }
                $apartmentName = $item->apartment->appraise_asset;
                $name_assets .= $apartmentName;
            } else {
                $name_assets .= $item->appraise_asset;
            }
        }
        return $name_assets;
    }
    protected function getAssetAddress($certificate)
    {
        $address_assets = '';
        $assets = $certificate->realEstate;
        foreach ($assets as $index => $item) {
            $address_assets .= ($index > 0) ? " và " : "";
            if ($item->assetType->acronym == 'CC') {
                $address_assets .= ($item->apartment && $item->apartment->full_address) ?  $item->apartment->full_address : '';
            } else {
                $address_assets .= ($item->appraises && $item->appraises->full_address) ?  $item->appraises->full_address : '';
            }
        }

        return $address_assets;
    }
    // I
    protected function step1Sub1($section, $certificate)
    {
        $section->addTitle('Thông tin về khách hàng thẩm định giá:', 2);
        $section->addListItem('Khách hàng: ' . $certificate->petitioner_name, 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' . $certificate->petitioner_address, 0, null, 'bullets');
    }
    protected function step1Sub2($section, $certificate)
    {
        $comAcronym = !empty($this->companyAcronym) ?  ' (' . mb_strtoupper($this->companyAcronym) . ')' : '';
        $section->addTitle('Thông tin về doanh nghiệp thẩm định giá:', 2);
        $section->addListItem('Doanh nghiệp: ' . $this->companyName .  $comAcronym, 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' . $this->companyAddress, 0, null, 'bullets');
        $section->addListItem("Điện thoại: " . $this->companyPhone . "\tFax: " . $this->companyFax, 0, null, 'bullets', 'leftTab');
        $section->addListItem('Họ và tên Tổng Giám đốc: ' . ((isset($certificate->appraiserManager) && isset($certificate->appraiserManager->name)) ? $certificate->appraiserManager->name : ''), 0, null, 'bullets');
        $section->addListItem('Họ và tên Thẩm định viên: ' . ((isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : ''), 0, null, 'bullets');
        $section->addListItem('Người lập báo cáo: ' . (isset($certificate->createdBy->name) ? $certificate->createdBy->name : ''), 0, null, 'bullets');

    }
    protected function step1Sub3($section, $certificate)
    {
        $section->addTitle('Thông tin tài sản thẩm định giá:', 2);
        $type1 = 0; //Đất trống
        $type2 = 0; //Đất có nhà
        $type3 = 0; //Chung cư
        $appraiseAssetType = "Quyền sử dụng đất";
        foreach ($this->realEstates as $realEstate) {
            if ($realEstate->assetType->description == "ĐẤT TRỐNG") $type1 = 1;
            if ($realEstate->assetType->description == "ĐẤT CÓ NHÀ") $type2 = 1;
            if ($realEstate->assetType->description == "CHUNG CƯ") $type3 = 1;
        }
        if ($type1 && $type2 && $type3) {
            $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc và căn hộ chung cư";
        } else if ($type1 && $type3) {
            $appraiseAssetType = "Quyền sử dụng đất và căn hộ chung cư";
        } else if (($type1 && $type2) || ($type2)) {
            $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc";
        } else if ($type3) {
            $appraiseAssetType = 'Quyền sở hữu căn hộ chung cư';
        }
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Loại tài sản: ', ['bold' => true]);
        $listTmp->addText($appraiseAssetType . '.');
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Tên tài sản: ', ['bold' => true]);
        $listTmp->addText($this->getAssetName($certificate) . '.');
        $address = $this->getAssetAddress($certificate);
        if (!empty($address)) {
            $listTmp = $section->addListItemRun(0, 'bullets');
            $listTmp->addText('Địa chỉ: ', ['bold' => true]);
            $listTmp->addText($address . '.');
        }
    }
    protected function step1Sub4($section, $certificate)
    {
        $section->addTitle('Thông tin về cuộc thẩm định giá:', 2);
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Hợp đồng thẩm định giá: ', ['bold' => true]);
        $listTmp->addText( $this->contractCode . ' ' . $this->documentLongDateText . " giữa " . $this->companyName . " và " . $certificate->petitioner_name . '.');
        $appraiseDate = date_create($certificate->appraise_date);
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Thời điểm thẩm định giá: ', ['bold' => true]);
        $listTmp->addText(date_format($appraiseDate, "m/Y") . '.');
        $appraisePurpose = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Mục đích thẩm định giá: ', ['bold' => true]);
        $listTmp->addText($appraisePurpose . '.');
    }
    protected function step1Sub5($section, $certificate)
    {
        $section->addTitle('Thuật ngữ và những từ viết tắt:', 2);
        $section->addListItem('TSSS – Tài sản so sánh.', 0, null, 'bullets');
        $section->addListItem('TSTĐ – Tài sản thẩm định', 0, null, 'bullets');
        $section->addListItem('CTXD – Công trình xây dựng.', 0, null, 'bullets');
        $section->addListItem('CLCL – Chất lượng còn lại.', 0, null, 'bullets');
        $section->addListItem('QSHN – Quyền sở hữu nhà', 0, null, 'bullets');
        $section->addListItem('QSDĐ – Quyền sử dụng đất.', 0, null, 'bullets', $this->keepNext);
        $section->addListItem('GCN – Giấy chứng nhận', 0, null, 'bullets', $this->keepNext);
        $section->addListItem('TĐ – Thẩm định', 0, null, 'bullets', $this->keepNext);
        $section->addListItem('SS – So sánh.', 0, null, 'bullets');

    }
    protected function step1(Section $section, $certificate)
    {
        $section->addTitle('THÔNG TIN CƠ BẢN:', 1);
        // 1
        $this->step1Sub1($section, $certificate);
        //2
        $this->step1Sub2($section, $certificate);
        //3
        $this->step1Sub3($section, $certificate);
        //4
        $this->step1Sub4($section, $certificate);
        //5
        $this->step1Sub5($section, $certificate);
    }
    // II
    protected function step2 (Section $section, $certificate)
    {
        $section->addTitle('CÁC CĂN CỨ PHÁP LÝ THẨM ĐỊNH GIÁ:', 1);
        $section->addTitle('Văn bản pháp luật về thẩm định giá:', 2);
        // $section->addTableStyle('Colspan Rowspan', $this->styleTable);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(600, $this->cellVCentered)->addText('Stt', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText('Loại văn bản', ['bold' => true], $this->cellHCentered);
        $table->addCell(2000, $this->cellVCentered)->addText(' Số, ngày', ['bold' => true], $this->cellHCentered);
        $table->addCell(5300, $this->cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $this->cellHCentered);
        $index = 0;
        if (isset($certificate->legalDocumentsOnValuation))
            foreach ($certificate->legalDocumentsOnValuation as $doc) {
                $doc = $doc->toArray();
                $index += 1;
                $table->addRow(400, $this->cantSplit);
                $table->addCell(600, $this->cellVCentered)->addText($index, null, $this->cellHCentered);
                $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', [], ['align' => 'left']);
                $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', [], ['align' => 'left']);
                $table->addCell(5300, $this->cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '');
            }

        $section->addTitle('Văn bản pháp luật về đất đai:', 2);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(600, $this->cellVCentered)->addText('Stt', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText('Loại văn bản', ['bold' => true], $this->cellHCentered);
        $table->addCell(2000, $this->cellVCentered)->addText(' Số, ngày', ['bold' => true], $this->cellHCentered);
        $table->addCell(5300, $this->cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $this->cellHCentered);
        $index = 0;
        foreach ($certificate->legalDocumentsOnLand as $doc) {
            $doc = $doc->toArray();
            $index += 1;
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, $this->cellVCentered)->addText($index, null, $this->cellHCentered);
            $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', [], ['align' => 'left']);
            $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', [], ['align' => 'left']);
            $table->addCell(5300, $this->cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '');
        }
        $section->addTitle('Văn bản pháp luật về xây dựng:', 2);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(600, $this->cellVCentered)->addText('Stt', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText('Loại văn bản', ['bold' => true], $this->cellHCentered);
        $table->addCell(2000, $this->cellVCentered)->addText(' Số, ngày', ['bold' => true], $this->cellHCentered);
        $table->addCell(5300, $this->cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $this->cellHCentered);
        $index = 0;
        foreach ($certificate->legalDocumentsOnConstruction as $doc) {
            $doc = $doc->toArray();
            $index += 1;
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, $this->cellVCentered)->addText($index, null, $this->cellHCentered);
            $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', [], ['align' => 'left']);
            $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', [], ['align' => 'left']);
            $table->addCell(5300, $this->cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '');
        }
        $section->addTitle('Văn bản pháp luật của địa phương:', 2);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(600, $this->cellVCentered)->addText('Stt', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText('Loại văn bản', ['bold' => true], $this->cellHCentered);
        $table->addCell(2000, $this->cellVCentered)->addText(' Số, ngày', ['bold' => true], $this->cellHCentered);
        $table->addCell(5300, $this->cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $this->cellHCentered);
        $index = 0;
        foreach ($certificate->legalDocumentsOnLocal as $doc) {
            $doc = $doc->toArray();
            $index += 1;
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, $this->cellVCentered)->addText($index, null, $this->cellHCentered);
            $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', [], ['align' => 'left']);
            $table->addCell(2000, $this->cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', [], ['align' => 'left']);
            $table->addCell(5300, $this->cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '');
        }
    }
    // III
    protected function step3 (Section $section, $certificate)
    {
        $section->addTitle('PHÁP LÝ TÀI SẢN THẨM ĐỊNH GIÁ:', 1);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $appraiseLaw = $this->getAppraiseLaw($this->realEstates);
        $countLaw = count($appraiseLaw);
        if ($countLaw > 1)
            $table->addCell(600, $this->cellVCentered)->addText('Stt', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText('Loại văn bản', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText(' Số, ngày', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(5000, $this->cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText('Cơ quan cấp, xác nhận', ['bold' => true], $this->cellHCenteredKeepNext);
        $index = 0;
        foreach ($appraiseLaw as $law) {
            $index++;
            $table->addRow(400, $this->cantSplit);
            if ($countLaw > 1)
                $table->addCell(600, $this->cellVCentered)->addText($index, null, $this->cellHCenteredKeepNext);
            $table->addCell(2000, $this->cellVCentered)->addText($law['title'], null, ['keepNext' => true]);
            $table->addCell(2000, $this->cellVCentered)->addText($law['document_num']. $law['document_date'], null, ['keepNext' => true]);
            $table->addCell(5000, $this->cellVCentered)->addText($law['content'], null, ['keepNext' => true]);
            $table->addCell(2000, $this->cellVCentered)->addText($law['certifying_agency']);
        }
    }
    protected function getAppraiseLaw($realEstates)
    {
        $results = [];
        foreach ($realEstates as $realEstate) {
            if ($realEstate->assetType->acronym === 'CC') {
                $laws = $realEstate->apartment->law;
                foreach ($laws as $law) {
                    $result = [];
                    if (!empty($law->description))
                        $result['title'] = $law->description;
                    else
                        $result['title'] = $law->lawDocument->content?: '';
                    $result['title'] = CommonService::nl2br($result['title']);
                    $result['document_num'] = $law->document_num?:'';
                    $result['document_date'] = !empty($law->document_date) ?  ' ngày '. date_format(date_create($law->document_date), "d/m/Y") : '';
                    $result['content'] = !empty($law->content) ? CommonService::nl2br($law->content) : '';
                    $result['certifying_agency'] = !empty($law->certifying_agency) ? CommonService::nl2br($law->certifying_agency) : '';
                    array_push($results, $result);
                }
            } else {
                $laws = $realEstate->appraises->appraiseLaw;
                foreach ($laws as $law) {
                    $result = [];
                    if (!empty($law->description))
                        $result['title'] = $law->description;
                    else
                        $result['title'] = $law->law->content?: '';
                    $result['title'] = CommonService::nl2br($result['title']);
                    $result['document_num'] = $law->date?:'';
                    $result['document_date'] = !empty($law->law_date) ?  ' ngày '. date_format(date_create($law->law_date), "d/m/Y") : '';
                    $result['content'] = !empty($law->content) ? CommonService::nl2br($law->content) : '';
                    $result['certifying_agency'] = !empty($law->certifying_agency) ? CommonService::nl2br($law->certifying_agency) : '';
                    array_push($results, $result);
                }
            }
        }
        return $results;
    }
    //IV
    protected function step4(Section $section, $certificate)
    {
        $section->addTitle('ĐẶC ĐIỂM TÀI SẢN THẨM ĐỊNH GIÁ', 1);
        foreach ($this->realEstates as $stt =>  $realEstate) {
            if (!$this->isOnlyAsset)
                $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1) . ':', 2);
            else
                $section->addTitle('Tài sản thẩm định giá:', 2);
            if ($realEstate->assetType->acronym == 'CC')
                $this->assetCharacteristicsApartment($section, $realEstate);
            else {
                $appraise = $realEstate->appraises;
                $section->addTitle('Quyền sử dụng đất:', 3);
                if ((!isset($appraise->appraiseLaw)) || (!isset($appraise->appraiseLaw[0]))) continue;
                $this->assetCharacteristicsAppraise($section, $appraise);
            }
            $this->assetCharacteristicsFooter($section);
        }
    }
    protected function assetCharacteristicsApartment(Section $section, $realEstate)
    {
        $section->addTitle('Quyền sở hữu căn hộ chung cư:', 3);
        $table = $section->addTable($this->styleTable);
        $this->assetCharacteristicsHeader($table);
        $table->addRow(400, $this->cantSplit);
        $apartment = $realEstate->apartment;
        //1
        $address = $apartment->full_address?: '';
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('1', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Pháp lý');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Địa chỉ:');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText($address);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Diện tích sàn');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText(number_format(floatval($realEstate->total_area), 2, ',', '.') . ' '. $this->m2);
        //2
        $coordinateArr = explode(',', $realEstate->coordinates);
        $fullName = $apartment->appraise_asset ?: '';
        $assetName = $fullName . ' tọa lạc tại '. $address;
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('2', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Vị trí');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ X');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText($coordinateArr[0]?: '');
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ Y');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText($coordinateArr[1]?: '');
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Khả năng tiếp cận');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText($assetName );
        //3
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('3', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Số tầng');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Độ cao');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText('Tầng '. $apartment->apartmentAssetProperties->floor->name);
        //4
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('4', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Hướng nhìn');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Hướng chính');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText(CommonService::mbCaseTitle($apartment->apartmentAssetProperties->direction->description));
        //5
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('5', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Nội thất');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tình trạng');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText(CommonService::mbCaseTitle($apartment->apartmentAssetProperties->furnitureQuality->description));
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Mô tả');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText($apartment->apartmentAssetProperties->description?: '' );
        //6
        $utiDescriptionArr = CommonService::getUtilitiesDescription($apartment->apartmentAssetProperties->utilities);
        $utiDescriptionStr = implode(', ', $utiDescriptionArr);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('6', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Tiện ích');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tiện ích nội khu');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText($utiDescriptionStr);
    }

    protected function getToThua($appraise)
    {
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
    protected function getAppraisePropertyData($appraise)
    {
        $data = [];
        $totalArea = 0;
        $totalViolateArea = 0.00;
        $desciptionZoning = 'Không.';
        $typeZoningDefault = str_replace(' ', '', mb_strtolower($appraise->properties[0]->propertyDetail[0]->type_zoning));
        $existLandTypePurpose = [];
        $mdsd = "";
        $sttTmp = 0;
        $isZoning = false;
        $countZoning = 0;
        foreach ($appraise->properties as $property) {
            foreach ($property->propertyDetail as $item) {
                $totalArea += floatval($item->total_area);
                if (isset($item->is_zoning) && $item->is_zoning) {
                    if ($countZoning == 0) {
                        $totalViolateArea = $item->planning_area;
                        $isZoning = true;
                        $desciptionZoning = 'Thửa đất có ' . number_format(floatval($totalViolateArea), 2, ',', '.') . $this->m2  . ' thuộc ' . $item->type_zoning;
                    } else {
                        if ($isZoning == true) {
                            $typeZoning = str_replace(' ', '', mb_strtolower($item->type_zoning));
                            if (strcmp($typeZoningDefault, $typeZoning) == 0) {
                                $totalViolateArea = $totalViolateArea + $item->planning_area;
                                $desciptionZoning = 'Thửa đất có ' .  number_format(floatval($totalViolateArea), 2, ',', '.') . $this->m2  . ' thuộc ' . $item->type_zoning;
                            } else {
                                $totalViolateArea = $item->planning_area;
                                $desciptionZoning = $desciptionZoning . ', ' . number_format(floatval($totalViolateArea), 2, ',', '.') . $this->m2  . ' thuộc ' . $item->type_zoning;
                            }
                        } else {
                            $totalViolateArea = $item->planning_area;
                            $desciptionZoning = 'Thửa đất có ' . number_format(floatval($totalViolateArea), 2, ',', '.') . $this->m2  . ' thuộc ' . $item->type_zoning;
                        }
                    }
                }
                if (isset($item->landTypePurpose->description) && !isset($existLandTypePurpose[$item->landTypePurpose->acronym])) {
                    $existLandTypePurpose[$item->landTypePurpose->acronym] = 1;
                    $mdsd .= ($sttTmp) ? ', ' : '';
                    $mdsd .= CommonService::mbUcfirst($item->landTypePurpose->description) . ' (' . $item->landTypePurpose->acronym . ')';
                    $sttTmp++;
                }
                $countZoning += 1;
            }
        }
        $data['total_area'] = $totalArea;
        $data['mdsd'] = $mdsd;
        $data['desciptionZoning'] = $desciptionZoning;
        $data['totalViolateArea'] = $totalViolateArea;
        return $data;
    }
    protected function assetCharacteristicsAppraiseLegal($table, $appraise)
    {
        $strToThua = $this->getToThua($appraise);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('1', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Pháp lý');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Số địa chính');

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
        ->addText($strToThua . ', ' . $appraise->ward->name . ', ' . $appraise->district->name . ', ' . $appraise->province->name . '.', null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);

        $propertyData = $this->getAppraisePropertyData($appraise);

        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Diện tích lô đất', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(number_format($propertyData['total_area'], 2, ',', '.') . $this->m2 . '.', null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Mục đích sử dụng', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($propertyData['mdsd'], null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $thsd = "";
        $stt = 0;
        $existLandTypePurpose = [];
        if (isset($appraise->appraiseLaw[0])) {
            $appraiseLaw = $appraise->appraiseLaw[0];
            $thsd = $appraiseLaw->duration;
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Thời hạn sử dụng', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($thsd, null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Nguồn gốc sử dụng', null, ['align' => 'left']);
        $cell = $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none']);

        if (isset($appraise->appraiseLaw[0])) {
            $appraiseLaw = $appraise->appraiseLaw[0];
            $textRun = $cell->addTextRun(['align' => 'left']);
            $textRun->addText($appraiseLaw->origin_of_use, null, ['align' => 'left']);
        }
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $zoning = "";
        $stt = 0;
        $existLandTypePurpose = [];
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Quy hoạch', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($propertyData['desciptionZoning'], null, ['align' => 'left']);
    }
    protected function assetCharacteristicsAppraiseLocation($table, $appraise)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('2', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Vị trí', null, ['align' => 'left']);
        $coordinates = explode(",", $appraise->coordinates);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ X', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($coordinates[0], null, ['align' => 'left']);

        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ Y', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($coordinates[1], null, ['align' => 'left']);

        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $positionType = "";
        foreach ($appraise->properties as $index => $property) {
            $positionType .= ($index) ? ', ' : '';
            $positionType .= (isset($property->description) ? $property->description : '');
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText($this->locationDescription, null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(htmlspecialchars($positionType), null, ['align' => 'left']);
    }
    protected function assetCharacteristicsAppraiseTraffic($table, $appraise)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('3', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Giao thông', null, ['align' => 'left']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Kết cấu, bề rộng', null, ['align' => 'left']);

        $roadCellTmp = $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none']);

        foreach ($appraise->properties as $index => $property) {
            if (isset($property->propertyTurningTime) && count($property->propertyTurningTime)) {
                $mainRoad = "";
                $itemTmp = $property->propertyTurningTime->last();
                $mainRoad = (isset($itemTmp->material->description) ? CommonService::mbUcfirst($itemTmp->material->description) : '') . ' rộng khoảng ' . number_format($itemTmp->main_road_length, 2, ',', '.') . ' m ';
                $mainRoad = ($index) ? $mainRoad : '' . $mainRoad;
                $roadCellTmp->addText($mainRoad, ['bold' => false], ['align' => 'left']);
            } else {
                $mainRoad = "";
                $mainRoad .= (isset($property->material->description) ? CommonService::mbUcfirst($property->material->description) : '') . ' rộng khoảng ' . (isset($property->main_road_length) ? number_format($property->main_road_length, 2, ',', '.') . 'm' : '');
                if (!empty($mainRoad)) {
                    $roadCellTmp->addText('' . CommonService::mbUcfirst($mainRoad), ['bold' => false], ['align' => 'left']);
                }
            }
        }
    }

    protected function assetCharacteristicsAppraiseBusiness($table, $appraise)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('4', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Kinh doanh, hạ tầng', null, ['align' => 'left']);
        $business = "";
        foreach ($appraise->properties as $index => $property) {
            $business .= ($index) ? ', ' : '';
            $business .= (isset($property->business) && isset($property->business->description)) ? $property->business->description : '';
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Kinh doanh', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(CommonService::mbUcfirst($business), null, ['align' => 'left']);

        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $conditions = "";
        foreach ($appraise->properties as $index => $property) {
            $conditions .= ($index) ? ', ' : '';
            $conditions .= (isset($property->conditions) && isset($property->conditions->description)) ? $property->conditions->description : '';
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Hạ tầng', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(CommonService::mbUcfirst($conditions), null, ['align' => 'left']);
    }

    protected function assetCharacteristicsAppraiseShape($table, $appraise)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('5', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Hình dáng, kích thước', null, ['align' => 'left']);
        $landShape = "";
        foreach ($appraise->properties as $index => $property) {
            $landShape .= ($index) ? ', ' : '';
            $landShape .= (isset($property->landShape) && isset($property->landShape->description)) ? $property->landShape->description : '';
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Hình dáng', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(CommonService::mbUcfirst($landShape), null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $front_side_width = "";
        foreach ($appraise->properties as $index => $property) {
            $front_side_width .= ($index) ? ', ' : '';
            $front_side_width .= (isset($property->front_side_width)) ? $property->front_side_width : '';
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Chiều rộng mặt tiền', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText('Mặt tiền rộng ' . number_format($front_side_width, 2, ',', '.') . ' m.', null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $insight_width = "";
        foreach ($appraise->properties as $index => $property) {
            $insight_width .= ($index) ? ', ' : '';
            $insight_width .= (isset($property->insight_width)) ? $property->insight_width : '';
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Chiều sâu', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText('Chiều sâu ' . number_format($insight_width, 2, ',', '.') . ' m.', null, ['align' => 'left']);
    }
    protected function assetCharacteristicsAppraiseStatus($section, $table, $appraise)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('6', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Hiện trạng', null, ['align' => 'left']);
        $tangible = (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) ? "Có" : "Không có";
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText($this->statusDescription, null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($tangible . ' công trình xây dựng trên đất.', null, ['align' => 'left']);
        if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
            $section->addTitle('Nhà cửa, vật kiến trúc:', 3);
            $table = $section->addTable($this->styleTable);
            $table->addRow(400, $this->cantSplit);
            $table->addCell(2000, $this->cellVCentered)->addText('Tên tài sản', ['bold' => true], array_merge($this->cellHCentered, $this->keepNext));
            $table->addCell(8000, $this->cellVCentered)->addText('Đặc điểm kinh tế - kỹ thuật', ['bold' => true], $this->cellHCentered);
            $table->addCell(2000, $this->cellVCentered)->addText('Số lượng ', ['bold' => true], $this->cellHCentered);
            foreach ($appraise->tangibleAssets as $index => $tangibleAsset) {
                $table->addRow(400, $this->cantSplit);
                $structure = "";
                $rate = "";
                if (isset($tangibleAsset->buildingType->description)) {
                    $buildingType = $tangibleAsset->buildingType->description;
                    if ($buildingType == "NHÀ Ở RIÊNG LẺ") {
                        $structure .= "Nhà ở riêng lẻ - " . $tangibleAsset->floor . " tầng";
                        if (isset($tangibleAsset->buildingCategory->description))
                            $rate .= $tangibleAsset->buildingCategory->description;
                    } else if ($buildingType == "BIỆT THỰ") {
                        $structure .= "Biệt thự - " . $tangibleAsset->floor . " tầng";
                        if (isset($tangibleAsset->buildingCategory->description))
                            $rate .= $tangibleAsset->buildingCategory->description;
                    } else if ($buildingType == "NHÀ XƯỞNG (KHO)") {
                        $structure .= "Nhà xưởng(kho)";
                    } else {
                        $structure .= "Công trình khác";
                    }
                }
                $buildingType =  isset($tangibleAsset->tangible_name) ? CommonService::mbUcfirst($tangibleAsset->tangible_name) : '';
                $table->addCell(2000, $this->cellVCentered)->addText($buildingType, null, $this->cellVCentered);
                $cellTmp = $table->addCell(8000, ['valign' => 'center', 'align' => 'left']);
                $cellTmp->addListItem('Cấu trúc: ' . $structure, 0, null, 'bullets');
                if (!empty($rate)) $cellTmp->addListItem('Cấp công trình: ' . htmlspecialchars($rate), 0, null, 'bullets');
                $cellTmp->addListItem('Kết cấu:', 0, null, 'bullets');
                $cellTmp->addText('   ' . str_replace("\n", '<w:br/>   ', $tangibleAsset->contruction_description), null, ['valign' => 'center', 'align' => 'left']);
                $table->addCell(2000, $this->cellVCentered)->addText(number_format(floatval($tangibleAsset->total_construction_base), 2, ',', '.') . $this->m2, null, ['valign' => 'center', 'align' => 'center']);
            }
        }
    }
    protected function assetCharacteristicsAppraise(Section $section, $appraise)
    {
        $table = $section->addTable($this->styleTable);
        $this->assetCharacteristicsHeader($table);
        $this->assetCharacteristicsAppraiseLegal($table, $appraise);
        $this->assetCharacteristicsAppraiseLocation($table, $appraise);
        $this->assetCharacteristicsAppraiseTraffic($table, $appraise);
        $this->assetCharacteristicsAppraiseBusiness($table, $appraise);
        $this->assetCharacteristicsAppraiseShape($table, $appraise);
        $this->assetCharacteristicsAppraiseStatus($section, $table, $appraise);

    }
    protected function assetCharacteristicsFooter(Section $section)
    {
        $section->addTitle('Sơ đồ vị trí và hình ảnh tài sản thẩm định giá:', 3);
        $section->addListItem('Chi tiết xem phụ luc ảnh kèm theo.', 0, null, 'bullets');
    }
    protected function assetCharacteristicsHeader(Table $table)
    {
        $table->addRow(400, $this->rowHeader);
        $table->addCell(600, $this->cellVCentered)->addText('Stt', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2000, $this->cellVCentered)->addText('Chỉ tiêu', ['bold' => true], $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'gridSpan' => 2])->addText('Đặc điểm kinh tế - kỹ thuật', ['bold' => true], $this->cellHCentered);
    }
    //V
    protected function step5(Section $section, $certificate)
    {
        $section->addTitle('CƠ SỞ HÌNH THÀNH GIÁ TRỊ TÀI SẢN THẨM ĐỊNH GIÁ:', 1);
        $section->addTitle('Cơ sở giá trị của tài sản thẩm định giá:', 2);
        $basicProperty = '';
        $basicPropertyDescription = '';
        $properties = $this->getBasicProperties();
        $count = count($properties);
        foreach ($properties as $item) {
            $basicProperty .= ($count > 1) ? ' và ' : '';
            $basicProperty .= (isset($item->name)) ? $item->name : '';
            $basicPropertyDescription .= ($count > 1) ? ' và ' : '';
            $basicPropertyDescription .= (isset($item->description)) ? $item->description : '';
        }
        $section->addListItem('Căn cứ vào mục đích, tính chất đặc điểm của tài sản thẩm định giá, ' . $this->acronym . ' chọn ' . $basicProperty . ' làm cơ sở thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem($basicPropertyDescription, 0, null, 'bullets', $this->indentFistLine);
        $section->addTitle('Nguyên tắc thẩm định giá:', 2);
        $this->getPrincipleOfValuationDescription($certificate->certificatePrinciple, $section);
    }
    protected function getPrincipleOfValuationDescription($certificatePrinciple, $section)
    {
        foreach ($certificatePrinciple as $index => $item) {
            $section->addListItem($item->name, 0, null, 'bullets', $this->indentFistLine);
        }
        $section->addListItem('Các nguyên tắc khác (TĐGVN 04 Ban hành kèm theo thông tư số 158/2014/TT-BTC ngày 27/10/2014 của Bộ trưởng Bộ Tài Chính).', 0, null, 'bullets', $this->indentFistLine);
    }
    protected function getBasicProperties ()
    {
        $basicProperty = [];
        foreach ($this->realEstates as $realEstate) {
            if ($realEstate->assetType->acronym == 'CC') {
                $apartment = $realEstate->apartment;
                $basicProperty[$apartment->apartmentAppraisalBase->basicProperty->id] = $apartment->apartmentAppraisalBase->basicProperty;
            } else {
                $appraise = $realEstate->appraises;
                $basicProperty[$appraise->appraiseBasisProperty->id] = $appraise->appraiseBasisProperty;
            }
        }
        return $basicProperty;
    }
    //VI
    protected function step6(Section $section, $certificate)
    {
        $section->addTitle('PHƯƠNG THỨC TIẾN HÀNH THẨM ĐỊNH GIÁ:', 1);
        $section->addText('Toàn bộ công việc thẩm định giá được tiến hành theo quy trình thẩm định giá Việt Nam bao gồm 6 bước (TĐGVN 05 Ban hành kèm theo thông tư số 28/2015/TT-BTC ngày 06/03/2015 của Bộ trưởng Bộ Tài Chính). ', null, [ 'indentation' => ['firstLine' => Converter::inchToTwip(0.2)]]);
        $listTmp = $section->addListItemRun(0, 'bullets', $this->indentFistLine);
        $listTmp->addText('Bước 1: ', ['bold' => true]);
        $listTmp->addText('Tiếp nhận hồ sơ, hướng dẫn khách hàng viết yêu cầu thẩm định giá, giải thích quy trình, các thủ tục hồ sơ, tài liệu, chứng từ, ký kết hợp đồng. Nghiên cứu các tài liệu, hồ sơ do khách hàng cung cấp.');
        $listTmp = $section->addListItemRun(0, 'bullets', $this->indentFistLine);
        $listTmp->addText('Bước 2: ', ['bold' => true]);
        $listTmp->addText('Lập kế hoạch Thẩm định giá.');
        $listTmp = $section->addListItemRun(0, 'bullets', $this->indentFistLine);
        $listTmp->addText('Bước 3: ', ['bold' => true]);
        $listTmp->addText('Tiến hành Thẩm định hiện trạng tài sản theo hướng dẫn của khách hàng. Tổ Thẩm định giá đã kiểm tra, đối chiếu giữa thông tin về tài sản TĐG trong các chứng từ, hồ sơ pháp lý với thực tế tại hiện trường, ghi nhận những thông tin do khách hàng cung cấp.');
        $listTmp = $section->addListItemRun(0, 'bullets', $this->indentFistLine);
        $listTmp->addText('Bước 4: ', ['bold' => true]);
        $listTmp->addText('Phân tích về tài sản, các tài sản so sánh, vận dụng các tài liệu, kiểm tra, đối chiếu các thông tin thu thập tại hiện trường với các tài liệu.');
        $listTmp = $section->addListItemRun(0, 'bullets', $this->indentFistLine);
        $listTmp->addText('Bước 5: ', ['bold' => true]);
        $listTmp->addText('Ứng dụng nguyên tắc và phương pháp thẩm định giá để ước tính giá trị của tài sản thẩm định giá tại thời điểm và địa điểm thẩm định giá.');
        $listTmp = $section->addListItemRun(0, 'bullets', $this->indentFistLine);
        $listTmp->addText('Bước 6: ', ['bold' => true]);
        $listTmp->addText('Hoàn chỉnh báo cáo, cấp chứng thư, thanh lý hợp đồng.');
    }
    //VII
    protected function step7(Section $section, $certificate)
    {
        $section->addTitle('CÁC GIẢ THIẾT VÀ GIẢ THIẾT ĐẶC BIỆT:', 1);
        $section->addListItem($certificate->document_description, 0, null, 'bullets');
    }
    //VIII
    protected function step8(Section $section, $certificate)
    {
        $appraiseApproaches = [];
        $appraiseMethodUsed = [];
        if (!$this->isApartment) {
            foreach ($this->realEstates as $realEstate) {
                $appraise = $realEstate->appraises;
                $appraiseApproaches[$appraise->appraiseApproach->id] = $appraise;
            }
        } else {
            foreach ($this->realEstates as $realEstate) {
                $apartment = $realEstate->apartment;
                $appraiseApproaches[$apartment->apartmentAppraisalBase->approach->id] = $apartment->apartmentAppraisalBase;
            }
        }
        foreach ($appraiseApproaches as $item) {
            array_push($appraiseMethodUsed, $item->appraiseMethodUsed->name);
        }
        $appraiseMethodUsedStr = implode(', ', $appraiseMethodUsed);
        $appraiseMethodUsedStr = mb_strtolower($appraiseMethodUsedStr, 'utf8');

        $section->addTitle('CÁCH TIẾP CẬN VÀ PHƯƠNG PHÁP THẨM ĐỊNH GIÁ:', 1);
        $section->addTitle('Thông tin tổng quan về thị trường, các thông tin về thị trường giao dịch của tài sản thẩm định giá.', 2);
        $section->addListItem('Thông tin tổng quan về thị trường: Tại thời điểm thẩm định giá, thị trường bất động sản tại khu vực không có nhiều biến động.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Thực trạng và triển vọng cung cầu của nhóm (loại) tài sản thẩm định giá: Thị trường cung – cầu nhóm (loại) tài sản đang ở mức cân bằng.', 0, null, 'bullets', $this->indentFistLine);
        $section->addTitle('Căn cứ lựa chọn phương pháp:', 2);
        $certificatePrinciple = "";
        foreach ($certificate->certificatePrinciple as $index => $item) {
            $certificatePrinciple .= ($index) ? ' và ' : '';
            $certificatePrinciple .= (isset($item->name)) ? $item->name : '';
        }
        $section->addListItem('Tài sản thẩm định giá có đầy đủ giấy tờ pháp lý, phù hợp quy hoạch và sử dụng đúng mục đích công năng đem lại giá trị lớn nhất cho tài sản. Tài sản thẩm định đáp ứng với các yêu cầu theo ' . mb_strtolower($certificatePrinciple) . '.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Điều kiện, tính chất thông tin thị trường: Dữ liệu thị trường về các tài sản giao dịch có đặc điểm tương đồng với TSTĐG tương đối phổ biến và đầy đủ nên việc sử dụng ' . $appraiseMethodUsedStr . ' để xác định giá trị tài sản thẩm định giá là phù hợp và đáng tin cậy.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Ngoài ra nguồn dữ liệu và thông tin có thể sử dụng để xác định giá trị tài sản thẩm định giá theo phương pháp khác là rất hạn chế. Vì vậy, căn cứ mục đích thẩm định giá của tài sản ' . $this->acronym . ' sử dụng ' . $appraiseMethodUsedStr . ' là phù hợp.', 0, null, 'bullets', $this->indentFistLine);
        $section->addTitle('Cách tiếp cận, phương pháp thẩm định giá áp dụng:', 2);
        if ($this->isApartment) {
            $this->step8sub3Apartment($section);
        } else {
            $this->step8sub3Appraise($section);
        }
        $section->addTitle('Xác định giá trị tài sản cần thẩm định giá:', 2);
        foreach ($this->realEstates as $stt =>  $realEstate) {
            if ($realEstate->assetType->acronym == 'CC') {
               $this->step8Sub4Apartment($section, $stt);
            } else {
                $this->step8Sub4Appraise($section, $stt);
            }
        }
    }
    protected function step8sub3Appraise (Section $section)
    {
        $appraiseApproaches = [];
        foreach ($this->realEstates as $realEstate) {
            $appraise = $realEstate->appraises;
            $appraiseApproaches[$appraise->appraiseApproach->id] = $appraise;
        }
        if ($this->isTangibleAsset)
            $section->addTitle('Đối với quyền sử dụng đất', 3);
        foreach ($appraiseApproaches as $item) {
            $section->addListItem("Cách tiếp cận: " . $item->appraiseApproach->name, 0, ['bold' => true, 'italic' => true], 'bullets');
            $section->addText($item->appraiseApproach->description, null, $this->indentFistLine);
            $section->addListItem("Phương pháp sử dụng: " . $item->appraiseMethodUsed->name, 0, ['bold' => true, 'italic' => true], 'bullets');
            $section->addText($item->appraiseMethodUsed->description, null, $this->indentFistLine);
        }
        if ($this->isTangibleAsset) {
            $this->tangibleAsset($section);
        }
    }
    protected function step8sub3Apartment (Section $section)
    {
        $approaches = [];
        foreach ($this->realEstates as $realEstate) {
            $apartment = $realEstate->apartment;
            $approaches[$apartment->apartmentAppraisalBase->approach->id] = $apartment->apartmentAppraisalBase;
        }
        foreach ($approaches as $item) {
            $section->addListItem("Cách tiếp cận: " . $item->approach->name, 0, ['bold' => true, 'italic' => true], 'bullets');
            $section->addText($item->approach->description, null, $this->indentFistLine);
            $section->addListItem("Phương pháp sử dụng: " . $item->methodUsed->name, 0, ['bold' => true, 'italic' => true], 'bullets');
            $section->addText($item->methodUsed->description, null, $this->indentFistLine);
        }
    }
    protected function step8Sub4Apartment(Section $section, $stt)
    {
        if (!$this->isOnlyAsset) {
            $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1), 3);
        }
        $section->addText('- Chi tiết xem Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
    }
    protected function step8Sub4Appraise(Section $section, $stt)
    {
        if ($this->isOnlyAsset) {
            if ($this->isTangibleAsset) {
                $section->addTitle('Quyền sử dụng đất:', 3);
                $section->addText('- Chi tiết xem Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
                $section->addTitle('Nhà cửa, vật kiến trúc:', 3);
                $section->addText('- Chi tiết xem Phụ lục 2 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            } else {
                $section->addText('- Chi tiết xem Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            }
        } else {
            $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1), 3);
            if ($this->isTangibleAsset) {
                $section->addText('a) Quyền sử dụng đất:', ['italic' => true], $this->indentFistLine);
                $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
                $section->addText('b) Nhà cửa, vật kiến trúc:', ['italic' => true], $this->indentFistLine);
                $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 2 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            } else {
                $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            }
        }
    }
    protected function tangibleAsset(Section $section)
    {
        $section->addTitle('Nhà cửa, vật kiến trúc:', 3);
        $section->addText('a) Về nguyên giá', ['italic' => true], $this->indentFistLine);
        $section->addText('- Cách tiếp cận thị trường.', ['italic' => true], $this->indentFistLine);
        $section->addText('Cách tiếp cận từ thị trường là cách thức xác định giá trị của tài sản thẩm định giá thông qua việc so sánh tài sản thẩm định giá với các tài sản giống hệt hoặc tương tự đã có các thông tin về giá trên thị trường.', null,  $this->indentFistLine);
        $section->addText('- Phương pháp sử dụng: Phương pháp so sánh.', ['italic' => true], $this->indentFistLine);
        $section->addText('Phương pháp so sánh là phương pháp thẩm định giá, xác định giá trị của tài sản thẩm định giá dựa trên cơ sở phân tích mức giá của các tài sản so sánh để ước tính, xác định giá trị của tài sản thẩm định giá.', null,  $this->indentFistLine);
        $section->addText('- Kết quả điều tra thu thập dữ liệu thị trường, phân tích đánh giá và ước tính giá trị nhà cửa, vật kiến trúc:', ['italic' => true],  $this->indentFistLine);
        $section->addText('+ ' . $this->acronym . ' căn cứ theo Quyết định số 09-11/2019/QĐ-UBND ngày 11/3/2019 về việc Ban hành Quy định về giá nhà, giá vật kiến trúc trên địa bàn tỉnh Đồng Nai kết hợp khảo sát đơn giá xây dựng thực tế tại thì trường tỉnh Đồng Nai và tìm hiểu thông tin đơn giá xây dựng của các công ty xây dựng trên địa bàn tỉnh Đồng Nai. ', null, [ 'indentation' => ['firstLine' => Converter::inchToTwip(0)]]);
        $section->addText('1. Quyết định số: 11/2019/ QĐ- UBND ngày 15/03/2019 của UBND tỉnh Đồng Nai V/v Ban hành qui định về giá bồi thường, hỗ trợ tài sản khi nhà nước thu hồi đất trên địa bàn tỉnh Đồng Nai.', null,  $this->indentFistLine);
        $constructionCompanies = [];
        //echo '<pre>';
        foreach ($this->realEstates as $realEstate) {
            $appraise = $realEstate->appraises;
            foreach ($appraise->constructionCompany as $index => $item) {
                if (!isset($item->constructionCompany->company_id)) continue;
                $constructionCompanies[$item->constructionCompany->company_id] = $item;
            }
        }
        //exit;
        $stt = 1;
        foreach ($constructionCompanies as $index => $item) {
            $stt++;
            $section->addText(($stt) . '. ' . $item->constructionCompany->name . '.', null, $this->indentFistLine);
            $section->addText('- Địa chỉ: ' . $item->constructionCompany->address . '.', null, $this->indentFistLine);
            $section->addText('- Điện thoại: ' . $item->constructionCompany->phone_number, null, $this->indentFistLine);
            $section->addText('- Giám đốc: ' . $item->constructionCompany->manager_name . '.', null, $this->indentFistLine);
        }
        $section->addText(($stt + 1) . '. Các tài liệu lưu trữ của ' .$this->acronym . '.', null, $this->indentFistLine);
        $section->addText('b) Về đánh giá chất lượng còn lại', ['bold' => true], $this->indentFistLine);
        $section->addText('- Căn cứ theo biên bản kiểm kê và kết quả khảo sát hiện trạng của Tổ thẩm định giá. ' .$this->acronym . ' đánh giá nguyên giá, chất lượng còn lại của công trình xây dựng:', null, $this->indentFistLine);
        $section->addText('❖ Phương pháp đánh giá chất lượng còn lại nhà cửa, vật kiến trúc: ' .$this->acronym . ' lựa chọn 2 phương pháp:', ['bold' => true], $this->indentFistLine);
        $section->addText('✔ Phương pháp 1: Phương pháp tuổi đời (PP1): ', ['italic' => true, 'bold' => true, 'size' => 13], $this->indentFistLine);
        $section->addText('- Vận dụng thông tư 45/2013/TT-BTC ngày 25 tháng 4 năm 2013 kết hợp khảo sát hiện trường tại thời điểm thẩm định giá. ', null, $this->indentFistLine);
        $table = $section->addTable(array_merge($this->tableBasicStyle));
        $table->addRow();
        $table->addCell(2500, $this->cellRowSpan)->addText("Tỷ lệ hao mòn vật lý (%)", [], $this->cellHCentered);
        $table->addCell(800, $this->cellRowSpan)->addText("=", [], $this->cellHCentered);
        $table->addCell(4500)->addText("Tuổi đời hiệu quả", [], ['borderBottomSize' => 6, 'underline' => 'dash', 'align' => 'center']);
        $table->addCell(800, $this->cellRowSpan)->addText("x", [], $this->cellHCentered);
        $table->addCell(800, $this->cellRowSpan)->addText("100%", [], $this->cellHCentered);
        $table->addRow();
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $table->addCell(4000)->addText("Tuổi đời vật lý", [], $this->cellHCentered);
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $section->addText('Tuổi đời vật lý = tuổi đời hiệu quả + Tuổi đời vật lý còn lại', null, $this->indentFistLine);
        $section->addText('Tuổi đời vật lý còn lại là thời gian ước tính còn lại mà tài sản có thể tiếp tục sử dụng trước khi chuyển sang trạng thái không còn sử dụng được do hư hỏng hoặc bào mòn vì các nguyên nhân vật lý.', null, $this->indentFistLine);
        $section->addText('✔ Phương pháp 2: Phương pháp chuyên gia (PP2): ', ['italic' => true, 'bold' => true, 'size' => 13], $this->indentFistLine);
        $section->addText('- Vận dụng thông tư liên tịch số 13/LB-TT ngày 18/8/1994 và thông tư 12/2012/TT-BXD ngày 28/12/2012 của BXD ban hành Quy chuẩn kỹ thuật quốc gia nguyên tắc phân loại, phân cấp công trình dân dụng, công nghiệp và hạ tầng kỹ thuật đô thị. ', null, $this->indentFistLine);
        $table = $section->addTable(array_merge($this->tableBasicStyle));
        $table->addRow();
        $table->addCell(2500, $this->cellRowSpan)->addText("Chất lượng còn lại của CTXD (%)", [], $this->cellHCentered);
        $table->addCell(800, $this->cellRowSpan)->addText("=", [], $this->cellHCentered);
        $table->addCell(4500)->addText("Σ (Tỷ lệ CLCL của kết cấu chính x tỷ lệ giá trị kết cấu chính)", [], ['borderBottomSize' => 6, 'underline' => 'dash', 'align' => 'center']);
        $table->addCell(800, $this->cellRowSpan)->addText("x", [], $this->cellHCentered);
        $table->addCell(800, $this->cellRowSpan)->addText("100%", [], $this->cellHCentered);
        $table->addRow();
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $table->addCell(4000)->addText("Σ (Tỷ trọng kết cấu chính)", [], $this->cellHCentered);
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
        $section->addText('Ghi chú:', ['bold' => true], $this->indentFistLine);
        $section->addText('p: Tỷ trọng của các kết cấu chính (%)', null, $this->indentFistLine);
        $section->addText('H = Σ ph / Σ p; Tỷ lệ chất lượng còn lại (%)', null, $this->indentFistLine);
    }
    //IX
    protected function step9(Section $section, $certificate)
    {
        $section->addTitle('KẾT QUẢ THẨM ĐỊNH GIÁ:', 1);
        $section->addText('Trên cơ sở các tài liệu do ' . $certificate->petitioner_name . ' cung cấp, với phương pháp thẩm định giá như trên được áp dụng trong tính toán, '. $this->companyName .' thông báo kết quả thẩm định giá như sau:', null, $this->indentFistLine);
        if ($this->isApartment) {
            $this->step9Apartment($section);
        } else {
            $this->step9Appraise($section, $certificate);
        }
    }
    protected function step9Appraise (Section $section, $certificate)
    {
        foreach ($this->realEstates as $stt=> $realEstate) {
            $appraise = $realEstate->appraises;
            $sttLevel = 2;
            if ($this->isOnlyAsset) {
                $sttLevel = 1;
            } else {
                $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1), 2);
            }
            $onlyLandAsset = 1;
            if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                $onlyLandAsset = 0;
            }
            if (isset($appraise->otherAssets) && count($appraise->otherAssets)) {
                $onlyLandAsset = 0;
            }
            if ($onlyLandAsset) {
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($this->styleTable);
                $table->addRow(400, $this->rowHeader);
                $table->addCell(4000, $this->cellVCentered)->addText('Quyền sử dụng đất', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('MĐSD', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('Diện tích (' . $this->m2 . ')', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Đơn giá ', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Thành tiền', ['bold' => true], $this->cellHCenteredKeepNext);
                $propertyDetailTotal = 0;
                $zoningAppraise = [];
                $noZoningAppraise = [];
                $totalArea = 0;

                foreach ($appraise->properties as $property) {
                    foreach ($property->propertyDetail as $item) {
                        if ($item->is_zoning) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
                            // $donGiaDatRound = CommonService::roundViolationAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            // if (!$item->is_transfer_facility) {
                            //     $donGiaDatRound = CommonService::roundViolationCompositeAssetPrice($appraise, $donGiaDat);
                            // }
                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $zoningAppraise[] = $rowTmp;
                        }
                        if (!$item->is_zoning || $item->total_area -  $item->planning_area > 0) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_round');
                            // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            if ($item->is_transfer_facility) {
                                if ($appraise->layer_cutting_procedure) {
                                    // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $appraise->layer_cutting_price);
                                    $donGiaDatRound = CommonService::roundPrice($appraise->layer_cutting_price, $round);
                                }
                            } else {
                                // $donGiaDatRound = CommonService::roundCompositeAssetPrice($appraise, $donGiaDat);
                                $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            }

                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $noZoningAppraise[] = $rowTmp;
                        }
                    }
                }
                $isFirst = 0;
                foreach ($noZoningAppraise as $item) {
                    $table->addRow(400, $this->cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $this->cellRowSpan)->addText('Phần diện tích đất phù hợp quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered)->addText(null, null, ['keepNext' => true]);
                    }
                    $table->addCell(1500, $this->cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $this->cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }
                $isFirst = 0;
                foreach ($zoningAppraise as $item) {
                    $table->addRow(400, $this->cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $this->cellRowSpan)->addText('Phần diện tích đất vi phạm quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered);
                    }
                    $table->addCell(1500, $this->cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $this->cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }

                //Sumary table
                // $totalAll = $propertyDetailTotal;
                $totalAll = CommonService::getCertificateAssetPrice($appraise, 'total_asset_price');
                $totalAllRound = $realEstate->total_price;

                $table->addRow(400, $this->cantSplit);
                $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1500, $this->cellVCentered)->addText(number_format($totalArea, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1500, $this->cellVCentered)->addText('', null, ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($propertyDetailTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);

                if (!$this->isOnlyAsset) {
                    $table->addRow(400, $this->cantSplit);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left']);
                    $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
                } else {
                    $table->addRow(400, $this->cantSplit);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
                    $table->addRow(400, $this->cantSplit);
                    $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 5))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($totalAllRound) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center', 'keepNext' => true]);
                }
            } else {
                $section->addTitle('Quyền sử dụng đất:', $sttLevel + 1);
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($this->styleTable);
                $table->addRow(400, $this->rowHeader);
                $table->addCell(4000, $this->cellVCentered)->addText('Quyền sử dụng đất', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('MĐSD', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('Diện tích (' . $this->m2 . ')', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Đơn giá ', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Thành tiền', ['bold' => true], $this->cellHCenteredKeepNext);
                $propertyDetailTotal = 0;
                $zoningAppraise = [];
                $noZoningAppraise = [];
                $totalArea = 0;
                // dd($appraise->properties);
                foreach ($appraise->properties as $property) {
                    foreach ($property->propertyDetail as $item) {
                        if ($item->is_zoning) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
                            // $donGiaDatRound = CommonService::roundViolationAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            // if (!$item->is_transfer_facility) {
                            //     $donGiaDatRound = CommonService::roundViolationCompositeAssetPrice($appraise, $donGiaDat);
                            // }
                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $zoningAppraise[] = $rowTmp;
                        }
                        if (!$item->is_zoning || $item->total_area -  $item->planning_area > 0) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_round');
                            // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            if ($item->is_transfer_facility) {
                                if ($appraise->layer_cutting_procedure) {
                                    // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $appraise->layer_cutting_price);
                                    $donGiaDatRound = CommonService::roundPrice($appraise->layer_cutting_price, $round);
                                }
                            } else {
                                // $donGiaDatRound = CommonService::roundCompositeAssetPrice($appraise, $donGiaDat);
                                $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            }

                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $noZoningAppraise[] = $rowTmp;
                        }
                    }
                }
                $isFirst = 0;
                foreach ($noZoningAppraise as $item) {
                    $table->addRow(400, $this->cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $this->cellRowSpan)->addText('Phần diện tích đất phù hợp quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered)->addText(null,null,['keepNext'=> true]);
                    }
                    $table->addCell(1500, $this->cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $this->cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }
                $isFirst = 0;
                foreach ($zoningAppraise as $item) {
                    $table->addRow(400, $this->cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $this->cellRowSpan)->addText('Phần diện tích đất vi phạm quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered)->addText(null,null,['keepNext'=> true]);
                    }
                    $table->addCell(1500, $this->cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $this->cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $this->cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }

                $table->addRow(400, $this->cantSplit);
                // $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);

                $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($totalArea, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText('', ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($propertyDetailTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);

                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $section->addTitle('Nhà cửa, vật kiến trúc:', $sttLevel + 1);
                    $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                    $table = $section->addTable($this->styleTable);
                    $table->addRow(400, $this->rowHeader);
                    $table->addCell(4000, $this->cellVCentered)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
                    $table->addCell(1000, $this->cellVCentered)->addText('Đvt', ['bold' => true], $this->cellHCentered);
                    $table->addCell(1000, $this->cellVCentered)->addText('Số lượng', ['bold' => true], $this->cellHCentered);
                    $table->addCell(1000, $this->cellVCentered)->addText('CLCL (%)', ['bold' => true], $this->cellHCentered);
                    $table->addCell(2000, $this->cellVCentered)->addText('Đơn giá', ['bold' => true], $this->cellHCentered);
                    $table->addCell(2000, $this->cellVCentered)->addText('Thành tiền', ['bold' => true], $this->cellHCentered);
                    /*
                    $unitPrice =  0;
                    foreach($appraise->constructionCompany as $index=>$constructionCompany) {
                        $unitPrice += (isset($constructionCompany->constructionCompany)&&isset($constructionCompany->constructionCompany->unit_price_m</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/></w:rPr><w:t xml:space="preserve">)) ? $constructionCompany->constructionCompany->unit_price_m</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/></w:rPr><w:t xml:space="preserve"> : 0;
                    }
                    */

                    //if(!empty($appraise->constructionCompany)) $unitPrice =  round($unitPrice/count($appraise->constructionCompany));
                    $appraisalCLCL = $appraise->appraisal_clcl;
                    $appraisalDgxd = $appraise->appraisal_dgxd;
                    foreach ($appraise->tangibleAssets as $index => $tangibleAsset) {
                        $table->addRow(400, $this->cantSplit);
                        $building_type =  isset($tangibleAsset->tangible_name) ? $tangibleAsset->tangible_name : '';
                        //$total = (round($tangibleAsset->total_construction_area*$tangibleAsset->remaining_quality/100*$unitPrice));
                        $unitPrice =  CommonService::getDgxdChoosed($tangibleAsset, $appraisalDgxd);
                        $clcl =  CommonService::getClclChoosed($tangibleAsset, $appraisalCLCL);
                        $total = (round($tangibleAsset->total_construction_base * $clcl / 100 * $unitPrice));
                        $table->addCell(1000, $this->cellVCentered)->addText(CommonService::mbUcfirst($building_type), null, ['align' => 'left']);
                        $table->addCell(1000, $this->cellVCentered)->addText($this->m2, null, $this->cellHCentered);
                        $table->addCell(1000, $this->cellVCentered)->addText(number_format($tangibleAsset->total_construction_base, 2, ',', '.'), null, ['align' => 'right']);
                        //$table->addCell(1000, $this->cellVCentered)->addText($tangibleAsset->remaining_quality, null, $this->cellHCentered);
                        $table->addCell(1000, $this->cellVCentered)->addText($clcl, null, ['align' => 'right']);
                        if ($unitPrice) {
                            $table->addCell(1000, $this->cellVCentered)->addText(number_format($unitPrice, 0, ',', '.'), null, ['align' => 'right']);
                        } else {
                            $table->addCell(1000, $this->cellVCentered)->addText("Không biết", null, ['align' => 'right']);
                        }

                        $table->addCell(1000, $this->cellVCentered)->addText(number_format($total, 0, ',', '.'), null, ['align' => 'right', 'keepNext' => true]);
                    }
                    //get from database
                    $tangibleAssetTotal = CommonService::getCertificateAssetPrice($appraise, 'tangible_asset_price');
                    $table->addRow(400, $this->cantSplit);
                    //$table->addCell(1000, $this->cellVCentered)->addText('', null, $this->cellHCentered);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 5))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);
                    $table->addCell(1000, array('align' => 'right'))->addText(number_format($tangibleAssetTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
                }
                if (isset($appraise->otherAssets) && count($appraise->otherAssets)) {
                    $section->addTitle('Tài sản khác', $sttLevel + 1);
                    $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                    $table = $section->addTable($this->styleTable);
                    $table->addRow(400, $this->rowHeader);
                    $table->addCell(4000, $this->cellVCentered)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
                    $table->addCell(1000, $this->cellVCentered)->addText('Đvt', ['bold' => true], $this->cellHCentered);
                    $table->addCell(1000, $this->cellVCentered)->addText('Số lượng', ['bold' => true], $this->cellHCentered);
                    $table->addCell(2000, $this->cellVCentered)->addText('Đơn giá', ['bold' => true], $this->cellHCentered);
                    $table->addCell(2000, $this->cellVCentered)->addText('Thành tiền', ['bold' => true], $this->cellHCentered);

                    foreach ($appraise->otherAssets as $index => $otherAsset) {
                        $table->addRow(400, $this->cantSplit);
                        $table->addCell(1000, $this->cellVCentered)->addText($otherAsset->name, null, ['align' => 'left']);
                        $table->addCell(1000, $this->cellVCentered)->addText(strtolower($otherAsset->dvt), null, $this->cellHCentered);
                        $table->addCell(1000, $this->cellVCentered)->addText(number_format($otherAsset->total, 2, ',', '.'), null, ['align' => 'right']);
                        $table->addCell(1000, $this->cellVCentered)->addText(number_format($otherAsset->unit_price, 0, ',', '.'), null, ['align' => 'right']);
                        $table->addCell(1000, $this->cellVCentered)->addText(number_format($otherAsset->total_price, 0, ',', '.'), null, ['align' => 'right', 'keepNext' => true]);
                    }
                    //get from database
                    $otherAssetTotal = CommonService::getCertificateAssetPrice($appraise, 'other_asset_price');
                    $table->addRow(400, $this->cantSplit);
                    //$table->addCell(1000, $this->cellVCentered)->addText('', null, $this->cellHCentered);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);
                    $table->addCell(1000, array('align' => 'right'))->addText(number_format($otherAssetTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
                }

                $section->addTitle('Tổng giá trị tài sản thẩm định giá:', $sttLevel + 1);
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($this->styleTable);
                $table->addRow(400, $this->rowHeader);
                //$table->addCell(1000, $this->cellVCentered)->addText('Stt', ['bold' => true], $this->cellHCentered);
                $table->addCell(6000, $this->cellVCentered)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(4000, $this->cellVCentered)->addText('Giá trị thẩm định', ['bold' => true], $this->cellHCentered);
                $table->addRow(400, $this->cantSplit);
                //$table->addCell(1000, $this->cellVCentered)->addText('1', null, $this->cellHCentered);
                $table->addCell(1000, $this->cellVCentered)->addText('Quyền sử dụng đất', null, ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($propertyDetailTotal, 0, ',', '.'), null, array('align' => 'right'));
                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $table->addRow(400, $this->cantSplit);
                    //$table->addCell(1000, $this->cellVCentered)->addText('2', null, $this->cellHCentered);
                    $table->addCell(1000, $this->cellVCentered)->addText('Nhà cửa, vật kiến trúc', null, ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $this->cellVCentered)->addText(number_format($tangibleAssetTotal, 0, ',', '.'), null, ['align' => 'right']);
                }
                if (isset($appraise->otherAssets) && count($appraise->otherAssets)) {
                    $table->addRow(400, $this->cantSplit);
                    //$table->addCell(1000, $this->cellVCentered)->addText('2', null, $this->cellHCentered);
                    $table->addCell(1000, $this->cellVCentered)->addText('Tài sản khác', null, ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $this->cellVCentered)->addText(number_format($otherAssetTotal, 0, ',', '.'), null, ['align' => 'right']);
                }

                //Sumary table
                // $totalAll = $propertyDetailTotal + $tangibleAssetTotal + $otherAssetTotal;
                $totalAll = CommonService::getCertificateAssetPrice($appraise, 'total_asset_price');

                $totalAllRound = $realEstate->total_price;

                $table->addRow(400, $this->cantSplit);
                $table->addCell(1000, $this->cellVCentered)->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAll, 0, ',', '.'), ['bold' => true], ['align' => 'right']);

                $table->addRow(400, $this->cantSplit);
                if ($this->isOnlyAsset) {
                    $table->addCell(1000, $this->cellVCentered)->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
                } else {
                    $table->addCell(1000, $this->cellVCentered)->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left']);
                }
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);

                if ($this->isOnlyAsset) {
                    $table->addRow(400, $this->cantSplit);
                    $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 2))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($totalAllRound) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center']);
                }
            }
        }
        if (isset($this->realEstates) && (count($this->realEstates) > 1)) {
            $section->addTitle('Tổng giá trị tài sản thẩm định giá:', 2);
            $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
            $table = $section->addTable($this->styleTable);
            $table->addRow(400, $this->rowHeader);
            //$table->addCell(1000, $this->cellVCentered)->addText('Stt', ['bold' => true], $cellHCentered);
            $table->addCell(6000, $this->cellVCentered)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
            $table->addCell(4000, $this->cellVCentered)->addText('Giá trị thẩm định', ['bold' => true], $this->cellHCentered);
            $totalAll = 0;
            foreach ($this->realEstates as $stt => $realEstate) {
                $table->addRow(400, $this->cantSplit);
                //$table->addCell(1000, $this->cellVCentered)->addText($stt + 1, null, $this->);
                if ($this->isOnlyAsset) {
                    $table->addCell(1000, array('align' => 'left'))->addText('Tài sản thẩm định giá ', null, ['align' => 'left']);
                } else {
                    $table->addCell(1000, array('align' => 'left'))->addText('Tài sản thẩm định giá ' . ($stt + 1), null, ['align' => 'left', 'keepNext' => true]);
                }
                $totalAll += $realEstate->total_price;
                $table->addCell(1000, array('align' => 'right'))->addText(number_format($realEstate->total_price, 0, ',', '.'), null, array('align' => 'right', 'keepNext' => true));
            }

            // Disable round total amount due to Front-End having a summary table which is showing the sum amount of multiple assets.
            // If enabled, it will raise confusion for the client as they may not be able to understand the exact amount

            // $totalAllRound = CommonService::roundCertificatePrice($certificate, $totalAll);

            $totalAllRound = $totalAll;

            $table->addRow(400, $this->cantSplit);
            $table->addCell(1000, $this->cellVCentered)->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
            $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAll, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
            $table->addRow(400, $this->cantSplit);
            //$table->addCell(1000, $this->cellVCentered)->addText('', null, $this->);
            $table->addCell(1000, $this->cellVCentered)->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
            //PHP_ROUND_HALF_DOWN
            $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
            $table->addRow(400, $this->cantSplit);
            $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 2))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($totalAllRound) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center']);
        }
    }
    protected function step9Apartment (Section $section)
    {
        $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(4000, $this->cellVCentered)->addText('Tên tài sản', ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $table->addCell(1500, $this->cellVCentered)->addText('Diện tích (' . $this->m2 . ')', ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $table->addCell(1500, $this->cellVCentered)->addText('Đơn giá', ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $table->addCell(5000, $this->cellVCentered)->addText('Thành tiền', ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $totalAmnt = 0;
        $avgRound = 0;
        foreach ($this->realEstates as $realEstate) {
            $apartment = $realEstate->apartment;
            $price = 0;
            $totalPrice = 0;
            $roundPrice = 0;
            $priceData = $apartment->price->where('slug', 'apartment_asset_price')->first();
            $roundData = $apartment->price->where('slug', 'round_total')->first();
            $totalPriceData = $apartment->price->where('slug', 'apartment_total_price')->first();
            if (!empty($roundData)) {
                $roundPrice = $roundData->value;
            }
            if (!empty($priceData)) {
                $price = CommonService::roundPrice($priceData->value, $roundPrice);
            }
            if (!empty($totalPriceData)) {
                $totalPrice = CommonService::roundPrice($totalPriceData->value, 0);
            }
            $round = $realEstate->round_total ?: 0;
            $total = CommonService::roundPrice($realEstate->total_price ?: 0 , $round);
            $area = $realEstate->total_area ?: 0;
            $name = $apartment->appraise_asset ?:'';
            $totalAmnt += $total;
            $table->addRow(400, $this->rowHeader);
            $table->addCell(4000, $this->cellVCentered)->addText($name, null, ['align' => 'center', 'keepNext' => true]);
            $table->addCell(1500, $this->cellVCentered)->addText(number_format($area, 2, ',', '.'), null, ['align' => 'right', 'keepNext' => true]);
            $table->addCell(2000, $this->cellVCentered)->addText(number_format($price, 0, ',', '.'), null, ['align' => 'right', 'keepNext' => true]);
            $table->addCell(4500, $this->cellVCentered)->addText(number_format($totalPrice, 0, ',', '.'), null, ['align' => 'right', 'keepNext' => true]);
        }
        $this->step9RoundTotal($table, $totalAmnt);
    }
    protected function step9RoundTotal(Table $table, $total)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(1000, array('align' => 'left', 'gridSpan' => 3))->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
        $table->addCell(1000, $this->cellVCentered)->addText(number_format($total, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right', 'keepNext' => true]);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 4))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($total) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center', 'keepNext' => true]);
    }
    //X
    protected function step10(Section $section, $certificate)
    {
        $section->addTitle('NHỮNG ĐIỀU KHOẢN LOẠI TRỪ VÀ HẠN CHẾ CỦA KẾT QUẢ THẨM ĐỊNH GIÁ:', 1);
        $section->addListItem('Kết quả thẩm định giá trên chỉ có giá trị cho tài sản có đặc điểm pháp lý, đặc điểm kỹ thuật được mô tả tại mục IV của báo cáo này, theo yêu cầu thẩm định giá của ' . (isset($certificate->petitioner_name) ? $certificate->petitioner_name : '') . ' tại thời điểm và địa điểm thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Các số liệu về tài sản '. $this->companyName .' căn cứ vào hồ sơ do khách hàng cung cấp và kết hợp khảo sát thực tế tại hiện trường dưới sự hướng dẫn của khách hàng và các bên có liên quan.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Báo cáo chỉ có hiệu lực trong phạm vi số lượng và giá trị tài sản ghi tại mục IX của báo cáo này.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Kết quả thẩm định giá trên chỉ được sử dụng cho một “mục đích thẩm định giá” duy nhất theo yêu cầu của khách hàng. Khách hàng phải hoàn toàn chịu trách nhiệm khi sử dụng sai mục đích đã yêu cầu.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Khách hàng là chủ tài sản hoặc bên thứ ba yêu cầu thẩm định giá phải chịu hoàn toàn trách nhiệm về tính chính xác, hợp pháp các thông tin liên quan đến đặc điểm kỹ thuật, tính năng và tính pháp lý của tài sản thẩm định giá đã cung cấp cho '. $this->companyName .' tại thời điểm và địa điểm thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('' . $this->companyName . ' không có trách nhiệm kiểm tra thông tin của những bản sao các giấy tờ liên quan đến tính chất pháp lý của tài sản yêu cầu thẩm định giá so với bản gốc. ', 0, null, 'bullets', array_merge($this->indentFistLine, $this->keepNext));
    }
    //X
    protected function printAppendix(Section $section, $certificate)
    {
    }
    protected function signature(Section $section, $certificate)
    {
        $section->addTextBreak(null, null, $this->keepNext);
        $table3 = $section->addTable($this->tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), $this->cantSplit);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("THẨM ĐỊNH VIÊN VỀ GIÁ", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        if(isset($certificate->appraiserConfirm->name)) {
            $cell32->addText("KT. TỔNG GIÁM ĐỐC", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $cell32->addText( $certificate->appraiserConfirm->appraisePosition->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        } else {
            $cell32->addText("TỔNG GIÁM ĐỐC", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        }
        $table3->addRow(Converter::inchToTwip(1.5), $this->cantSplit);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);;
        $table3->addRow(Converter::inchToTwip(.1));
        $cell33 = $table3->addCell(Converter::inchToTwip(4));
        $bien171 = (isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : '';
        $cell33->addText($bien171, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $appraiserNumber =   isset($certificate->appraiser) ? $certificate->appraiser->appraiser_number : '';
        $cell33->addText("Số thẻ TĐV về giá: " . $appraiserNumber, ['bold' => true], ['align' => 'center']);
        $cell34 = $table3->addCell(Converter::inchToTwip(4));
        $appraiserManager = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->name : $certificate->appraiserManager->name;
        $appraiserManagerNumber = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->appraiser_number : $certificate->appraiserManager->appraiser_number;
        $bien172 = $appraiserManager;
        $cell34->addText($bien172, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell34->addText("Số thẻ TĐV về giá: " . $appraiserManagerNumber, ['bold' => true], ['align' => 'center']);
    }
}

