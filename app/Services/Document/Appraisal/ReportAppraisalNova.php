<?php

namespace App\Services\Document\Appraisal;

use App\Services\CommonService;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use App\Models\CertificateHasRealEstate;
use App\Models\CertificateApartment;
use App\Models\CertificateApartmentAppraisalBase;
use App\Models\Appraise;
use App\Models\Street;
use PhpOffice\PhpWord\PhpWord;

class ReportAppraisalNova extends ReportAppraisal
{
    protected $gridSpan2 = ['gridSpan' => 2];
    protected $locationDescription = '- Mô tả vị trí';
    protected $statusDescription = '';

    // I
    protected function step1Sub1($section, $certificate)
    {
        $section->addTitle('Thông tin về khách hàng thẩm định giá:', 2);
        $section->addListItem('Khách hàng: ' .  htmlspecialchars($certificate->petitioner_name), 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' .  htmlspecialchars($certificate->petitioner_address), 0, null, 'bullets');
        $section->addListItem('Bên sử dụng kết quả thẩm định giá: Khách hàng yêu cầu thẩm định giá', 0, null, 'bullets');
    }

    protected function step1Sub2($section, $certificate)
    {
        $comAcronym = !empty($this->companyAcronym) ?  ' (' . mb_strtoupper($this->companyAcronym) . ')' : '';
        $section->addTitle('Thông tin về doanh nghiệp thẩm định giá:', 2);
        $section->addListItem('Doanh nghiệp: ' .  htmlspecialchars($this->companyName), 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' .  htmlspecialchars($this->companyAddress), 0, null, 'bullets');
        $section->addListItem("Điện thoại: " . $this->companyPhone . "\tFax: " . $this->companyFax, 0, null, 'bullets', 'leftTab');
        $section->addListItem('Họ và tên người Đại diện pháp luật: ' . ((isset($certificate->appraiserManager) && isset($certificate->appraiserManager->name)) ? $certificate->appraiserManager->name : ''), 0, null, 'bullets');
        if (isset($certificate->appraiserConfirm->name)) {
            $section->addListItem('Họ và tên người được uỷ quyền Đại diện pháp luật: ' . $certificate->appraiserConfirm->name, 0, null, 'bullets');
        }
        $section->addListItem('Họ và tên Thẩm định viên về giá: ' . ((isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : ''), 0, null, 'bullets');
        // $section->addListItem('Người lập báo cáo: ' . (isset($certificate->createdBy->name) ? $certificate->createdBy->name : ''), 0, null, 'bullets');
        $section->addListItem('Người lập báo cáo giá: ' . (isset($certificate->appraiserPerform)  && isset($certificate->appraiserPerform->name) ? $certificate->appraiserPerform->name : ''), 0, null, 'bullets');

        // appraiserPerform
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
        // if ($type1 && $type2 && $type3) {
        //     $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc và căn hộ chung cư";
        // } else if ($type1 && $type3) {
        //     $appraiseAssetType = "Quyền sử dụng đất và căn hộ chung cư";
        // } else if (($type1 && $type2) || ($type2)) {
        //     $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc";
        // } else if ($type3) {
        //     $appraiseAssetType = 'Quyền sở hữu căn hộ chung cư';
        // }

        if ($type1) {
            $appraiseAssetType = 'Quyền sử dụng đất';
        } else if ($type2) {
            $appraiseAssetType = 'Quyền sử dụng đất và sở hữu công trình xây dựng trên đất';
        } else if ($type3) {
            $appraiseAssetType = 'Quyền sở hữu căn hộ chung cư';
        }
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Loại tài sản: ', ['bold' => true]);
        $listTmp->addText($appraiseAssetType);
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Tên tài sản: ', ['bold' => true]);
        $listTmp->addText($this->getAssetName($certificate) . '.');
        $address = $this->getAssetAddress($certificate);
        if (!empty($address)) {
            $listTmp = $section->addListItemRun(0, 'bullets');
            $listTmp->addText('Địa chỉ: ', ['bold' => true]);
            $listTmp->addText(htmlspecialchars($address) . '.');
        }
    }
    protected function step1Sub4($section, $certificate)
    {
        $section->addTitle('Thông tin về cuộc thẩm định giá:', 2);
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Hợp đồng thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText($this->contractCode . ' ' . $this->documentLongDateText . " giữa " . $this->companyName . " và " . $certificate->petitioner_name . '.');
        $appraiseDate = date_create($certificate->appraise_date);
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Thời điểm thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText('Tháng ' . date_format($appraiseDate, "m/Y") . '.', null, []);
        $appraisePurpose = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        if ($appraisePurpose === 'Vay vốn ngân hàng')
            $appraisePurpose = 'Tư vấn giá trị tài sản để ngân hàng tham khảo và xem xét quyết định hạn mức để cấp tín dụng';
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Mục đích thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText($appraisePurpose . '.', null, []);
        $this->step1Sub4NovaInfo($section);
    }
    private function step1Sub4NovaInfo($section)
    {
        $textRun = $section->addListItemRun(0, 'bullets');
        $textRun->addText('Các nguồn thông tin được sử dụng trong quá trình thẩm định giá và mức độ kiểm tra, thẩm định các nguồn thông tin đó:', ['bold' => true], $this->cellVCenteredKeepNext);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(4000, $this->cellVCentered)->addText('Các nguồn thông tin thu thập', ['bold' => true], $this->cellVCenteredKeepNext);
        $table->addCell(6000, $this->cellVCenteredKeepNext)->addText('Mức độ kiểm tra, thẩm định các nguồn thông tin', ['bold' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(10000, $this->gridSpan2)->addText(' 1. Đối với tài sản thẩm định giá', ['bold' => true, 'italic' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $cell =  $table->addCell(4000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Hồ sơ pháp lý của tài sản thẩm định giá do khách hàng cung cấp.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Tổ thẩm định giá tiến hành khảo sát hiện trạng tài sản thẩm định giá dưới sự hướng dẫn của khách hàng yêu cầu thẩm định giá hoặc người được khách hàng yêu cầu thẩm định giá ủy quyền.' . '.', null, $this->cellVCentered);
        $cell =  $table->addCell(6000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Hồ sơ pháp lý do khách hàng cung cấp bản photocopy được đính kèm báo cáo kết quả thẩm định giá này.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Diễn biến và kết quả khảo sát hiện trường được ghi nhận tại Biên bản thẩm định hiện trạng tài sản của ' . $this->companyName . ' được lưu trữ trong hồ sơ thẩm định giá. ' . '.', null, $this->cellVCentered);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(10000, $this->gridSpan2)->addText(' 2. Đối với các giao dịch chào mua / chào bán / thành công của các tài sản tương tự trên thị trường', ['bold' => true, 'italic' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $cell =  $table->addCell(4000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Nguồn cung cấp thông tin là những người tham gia giao dịch mua bán tài sản, người được ủy quyền chào mua / chào bán tài sản, các lực lượng trung gian trên thị trường có thông tin về giao dịch mua – bán tài sản trên thị trường (môi giới, văn phòng công chứng, văn phòng đăng ký quyền sử dụng đất)', null, null);
        $cell =  $table->addCell(6000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Chuyên viên thẩm định thu thập thông tin bằng cách phỏng vấn trực tiếp hoặc qua điện thoại người có thông tin hoặc có khả năng cung cấp thông tin kết hợp với khảo sát thực tế tài sản.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thẩm định viên kiểm tra đối với thông tin thu thập tại thời điểm thẩm định giá.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Bộ phận kiểm soát định kỳ kiểm tra thông tin thu thập tại thời điểm gần với thời điểm thẩm định giá.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Kết quả thu thập thông tin thông qua phỏng vấn trực tiếp được ghi nhận tại phiếu thu thập thông tin bất động sản được lưu trữ trong hồ sơ thẩm định giá.', null, null);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(10000, $this->gridSpan2)->addText(' 3. Đối với các thông tin khác', ['bold' => true, 'italic' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $cell =  $table->addCell(4000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin đăng tải trên các phương tiện truyền thông', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin trên các văn bản quy phạm pháp quy của nhà nước', null, null);
        $cell =  $table->addCell(6000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin đăng tải trên các phương tiện truyền thông được chuyên viên thẩm định thu thập và kiểm tra qua nhiều nguồn đăng tải được nêu rõ trong báo cáo thẩm định giá. Thẩm định viên kiểm tra thông tin thu thập tại thời điểm thẩm định giá.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin trích dẫn từ các văn bản quy phạm pháp quy của nhà nước được sử dụng trên cơ sở trích dẫn trực tiếp nội dung cụ thể trong báo cáo thẩm định giá.', null, null);
    }
    //IV
    protected function assetCharacteristicsAppraiseLegal($table, $appraise)
    {
        $strToThua = $this->getToThua($appraise);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('1', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Pháp lý', null, $this->styleAlignLeft);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Số địa chính', null, $this->styleAlignLeft);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($strToThua . ', ' . $appraise->ward->name . ', ' . $appraise->district->name . ', ' . $appraise->province->name . '.', null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Vị trí hành chính', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(htmlspecialchars($appraise->full_address), null, ['align' => 'left']);

        $positionType1 = "";
        foreach ($appraise->properties as $index => $property) {
            $positionType1 .= ($index) ? ', ' : '';
            $positionType1 .= (isset($property->geographical_location) ? $property->geographical_location : '');
        }
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Vị trí địa lý', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText('   ' . str_replace("\n", '<w:br/>   ', $positionType1), null, ['align' => 'left']);

        $propertyData = $this->getAppraisePropertyData($appraise);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);

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
        // $table->addRow(400, $this->cantSplit);
        // $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        // $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $zoning = "";
        $stt = 0;
        $existLandTypePurpose = [];
        // $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Quy hoạch', null, ['align' => 'left']);

        // $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
        //         ->addText($propertyData['desciptionZoning'], null, ['align' => 'left']);
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
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Mô tả vị trí', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(htmlspecialchars($positionType), null, ['align' => 'left']);
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

        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('7', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Vị trí, đơn giá đất theo Quyết định của UBND', null, ['align' => 'left']);
        $testtt = json_decode($appraise->properties[0])->property_detail;
        foreach ($testtt as $index => $mucdich) {
            $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Vị trí', null, ['align' => 'left']);
            $vitri_id = $mucdich->position_type_id;
            $dongiaUBND = $mucdich->circular_unit_price;
            $loaidat = $mucdich->land_type_purpose->description;
            $street_full = Street::where('id', $appraise->street_id)->first();
            // $section->addText(json_encode($street_full));
            if ($street_full) {
                $street = $street_full->name;
            }

            $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(CommonService::mbUcfirst(CommonService::getViTri($vitri_id)) . ' ' . $street, null, ['align' => 'left']);

            $table->addRow(400, $this->cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Đơn giá đất', null, ['align' => 'left']);

            $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(CommonService::mbUcfirst($loaidat) . ': ' . number_format($dongiaUBND, 0, ',', '.') . ' đồng/' . $this->m2, null, ['align' => 'left']);
            if ($index + 1 < count($testtt)) {
                $table->addRow(400, $this->cantSplit);
                $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
                $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            }
        }

        if (CommonService::getPlaningInfo($appraise->appraise_id)) {
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('8', null, $this->cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Thông tin quy hoạch', null, ['align' => 'left']);
            $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('', null, ['align' => 'left']);
            $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(str_replace("\n", '<w:br/>   ', CommonService::getPlaningInfo($appraise->appraise_id)), null, ['align' => 'left']);
        }

        if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
            $section->addTitle('Công trình xây dựng:', 3);
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
                $c1 = $table->addCell(2000, $this->cellVCentered);
                $c1->addText('- Diện tích xây dựng: ' . number_format(floatval($tangibleAsset->total_construction_area), 2, ',', '.') . $this->m2, null, ['valign' => 'center', 'align' => 'left']);
                $c1->addText('- Diện tích sàn: ' . number_format(floatval($tangibleAsset->total_construction_base), 2, ',', '.') . $this->m2, null, ['valign' => 'center', 'align' => 'left']);
            }
        }
    }
    //V
    protected function getPrincipleOfValuationDescription($certificatePrinciple, $section)
    {
        $section->addListItem('Tổ thẩm định giá vận dụng các nguyên tắc sử dụng tốt nhất và hiệu quả nhất, nguyên tắc dự kiến lợi ích trong tương lai, nguyên tắc tăng và giảm thu nhập, nguyên tắc cung cầu và cạnh tranh, nguyên tắc thay thế, nguyên tắc thay đổi, nguyên tắc phù hợp, nguyên tắc cân bằng.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Phân tích sử dụng tốt nhất và hiệu quả nhất của tài sản thẩm định: Căn cứ vào đặc điểm pháp lý, đặc điểm kinh tế kỹ thuật và hiện trạng của tài sản thẩm định. Tổ thẩm định nhận thấy phương án sử dụng hiện tại là phương án sử dụng tốt nhất và có hiệu quả nhất.', 0, null, 'bullets', $this->indentFistLine);
    }
    //VII
    // protected function step7(Section $section, $certificate)
    // {
    //     $section->addTitle('CÁC GIẢ THIẾT VÀ GIẢ THIẾT ĐẶC BIỆT:', 1);
    //     $section->addListItem('Do đặc điểm giao dịch thị trường bất động sản tại địa phương, Công ty TNHH Thẩm định giá NOVA nhận định rằng mức giá rao bán của tài sản ở địa phương thường cao hơn giá giao dịch thành công một khoảng nhất định. Tổ thẩm định giá tiến hành thương lượng để tìm khoảng giá bán hợp lý nhất và giả định rằng giá thương lượng là mức giá có khả năng xảy ra giao dịch cao nhất.', 0, null, 'bullets');
    //     $section->addListItem('Tổ thẩm định giá xác định diện tích đất và công trình xây dựng của tài sản thẩm định giá dựa trên hồ sơ khách hàng cung cấp, diện tích đất và công trình xây dựng của tài sản so sánh dựa trên thông tin thu thập được qua phỏng vấn trực tiếp chủ tài sản hoặc người chào bán. Việc thực hiện báo cáo này kèm theo giả định các thông tin thu thập trên là đúng và phù hợp với hiện trạng tài sản tại thời điểm thẩm định giá.', 0, null, 'bullets');
    // }
    protected function step7(Section $section, $certificate)
    {
        $section->addTitle('CÁC GIẢ THIẾT VÀ GIẢ THIẾT ĐẶC BIỆT:', 1);
        if ($this->isApartment) {
            $apartment = CertificateHasRealEstate::where('certificate_id', $certificate->id)->first();
            if ($apartment) {
                $asset = CertificateApartment::where('real_estate_id', $apartment->real_estate_id)->first();
                if ($asset) {
                    $description = CertificateApartmentAppraisalBase::where('apartment_asset_id', $asset->id)->first();
                    if ($description) {
                        $giathiet = $description->description;
                    }
                }
            } else {
                $giathiet = $certificate->document_description;
            }
            $section->addText('    ' . str_replace("\n", '<w:br/>    ', $giathiet), null, ['valign' => 'center', 'align' => 'left']);
        } else {
            $section->addText('    ' . str_replace("\n", '<w:br/>    ', json_decode($certificate)->real_estate[0]->appraises->document_description), null, ['valign' => 'center', 'align' => 'left']);
        }
        // $section->addText('    '.str_replace("\n", '<w:br/>    ', json_encode(json_decode($certificate)->real_estate)), null, ['valign' => 'center', 'align' => 'left']);
        // $section->addText('    '.str_replace("\n", '<w:br/>    ', json_encode(json_decode($certificate)->real_estate[0])), null, ['valign' => 'center', 'align' => 'left']);

        // $section->addListItem(json_encode($certificate->real_estate), 0, null, 'bullets');
        // $section->addListItem(json_encode(json_decode($certificate)->real_estate), 0, null, 'bullets');
    }
    //VIII
    protected function step8(Section $section, $certificate)
    {
        $section->addTitle('CÁCH TIẾP CẬN VÀ PHƯƠNG PHÁP THẨM ĐỊNH GIÁ:', 1);
        $section->addTitle('Thông tin tổng quan về thị trường', 2);
        $section->addListItem('Tại thời điểm thẩm định giá, tình hình thị trường bất động sản tại khu vực tài sản thẩm định giá tọa lạc hoạt động tương đối ổn định. Giao dịch mua bán chủ yếu thực hiện trực tiếp giữa người mua và người bán qua kênh thông tin quảng cáo, rao vặt trên báo giấy và internet. Thông tin về tài sản được niêm yết trên các trang quảng cáo, rao vặt tương đối chính xác.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Giá giao dịch được hình thành trên cơ sở thỏa thuận trực tiếp giữa người mua và người bán. Do đó, giá giao dịch thành công của từng bất động sản phần nào chịu tác động bởi tính chủ quan của người tham gia giao dịch.', 0, null, 'bullets', $this->indentFistLine);
        // $section->addListItem('Giá chào bán bất động sản phổ biến cao hơn giá giao dịch thành công từ 5% - 10%. Thời gian chào bán và giao dịch mua bán bất động sản trên thị trường dao động phổ biến từ 03 đến 10 tháng.', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Thông tin về thị trường giao dịch của nhóm (loại) TSTĐG', 2);
        $section->addListItem('Tại thời điểm thẩm định giá, tình hình giao dịch tại khu vực chủ yếu là đất nền, nhà phố với quy mô diện tích đa dạng, khu vực dân cư tập trung đông, điều kiện giao thông thuận lợi nên việc mua bán bất động sản diễn ra tương đối sôi nổi.', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Thực trạng và triển vọng cung cầu của nhóm (loại) tài sản thẩm định giá', 2);
        $section->addListItem('Thực trạng cung - cầu:', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Thực trạng cung: Chủ yếu từ người dân tại khu vực có nhà cần bán', 1, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Thực trạng cầu: Nguồn cầu chủ yếu từ người dân tại khu vực và các vùng lân cận', 1, null, 'bullets', $this->indentFistLine);

        $section->addListItem('Triển vọng cung - cầu:', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Triển vọng cung: Điều kiện giao thông thuận lợi, khu dân cư hiện hữu, gần trường học, chợ nên giá bất động sản có chiều hướng tăng.', 1, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Triển vọng cầu: Chủ yếu từ người dân khu vực và các vùng lân cận', 1, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Phân tích về các tài sản thay thế hoặc cạnh tranh', 2);
        $section->addListItem('Tài sản thẩm định giá là bất động sản có thể thay thế bởi các bất động sản khác trên cùng tuyến đường tại khu vực và các tuyến đường khác trong khu vực có cùng các điều kiện tự nhiên, kinh tế, hạ tầng và các tiện ích chung trong khu vực.', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Thông tin về các yếu tố kinh tế, xã hội và các yếu tố khác có ảnh hưởng đến giá trị tài sản thẩm định giá', 2);

        $table = $section->addTable($this->styleTable);
        $table->addRow(400, array_merge($this->cantSplit, $this->rowHeader));
        $table->addCell(2000, $this->cellVCentered)->addText('Các yếu tố', null, $this->keepNext);
        $table->addCell(8000, $this->cellVCentered)->addText('Nội dung thông tin', null, $this->keepNext);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(2000, $this->cellVCentered)->addText('Kinh tế', null, $this->keepNext);
        $table->addCell(8000, $this->cellVCentered)->addText('- Tài sản nằm trong khu vực dân cư sinh sống đông đúc, gần trường học, chợ.', null, $this->keepNext);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(2000, $this->cellVCentered)->addText('Xã hội', null, $this->keepNext);

        $cell = $table->addCell(8000, $this->cellVCentered);
        $cell->addText('- Hạ tầng kỹ thuật: Hạ tầng kỹ thuật hiện hữu, hoàn thiện.');
        $cell->addText('- Hệ thống cấp nước: Hoàn chỉnh.');
        $cell->addText('- Hệ thống thoát nước: Hoàn chỉnh.');
        $cell->addText('- Hệ thống điện: Hoàn chỉnh.');
        $cell->addText('- Dịch vụ bưu chính viễn thông: Khu có dịch vụ viễn thông hoàn chỉnh.');
        $cell->addText('- Môi trường dân cư: Khu vực dân cư sinh sống đông đúc.');

        $table->addRow(400, $this->cantSplit);
        $table->addCell(2000, $this->cellVCentered)->addText('Các yếu tố khác', null, $this->keepNext);
        $table->addCell(8000, $this->cellVCentered)->addText('- Quy mô diện tích: Có ảnh hưởng đến tính thanh khoản của tài sản.', null, $this->keepNext);

        // $section->addTitle('Căn cứ lựa chọn phương pháp', 2);
        // $certificatePrinciple = "";
        // foreach ($certificate->certificatePrinciple as $index => $item) {
        //     $certificatePrinciple .= ($index) ? ' và ' : '';
        //     $certificatePrinciple .= (isset($item->name)) ? $item->name : '';
        // }
        // $section->addListItem('Tài sản thẩm định giá có đầy đủ giấy tờ pháp lý, phù hợp quy hoạch và sử dụng đúng mục đích công năng đem lại giá trị lớn nhất cho tài sản. Tài sản thẩm định đáp ứng với các yêu cầu theo ' . $certificatePrinciple . '.', 0, null, 'bullets', $this->indentFistLine);
        // $section->addListItem('Điều kiện, tính chất thông tin thị trường: Dữ liệu thị trường về các tài sản giao dịch có đặc điểm tương đồng với TSTĐG tương đối phổ biến và đầy đủ nên việc sử dụng phương pháp so sánh để xác định giá trị tài sản thẩm định giá là phù hợp và đáng tin cậy.', 0, null, 'bullets', $this->indentFistLine);
        // $section->addListItem('Ngoài ra nguồn dữ liệu và thông tin có thể sử dụng để xác định giá trị tài sản thẩm định giá theo phương pháp khác là rất hạn chế. Vì vậy, căn cứ mục đích thẩm định giá của tài sản ' . $this->acronym . ' sử dụng phương pháp so sánh là phù hợp.', 0, null, 'bullets', $this->indentFistLine);
        $section->addTitle('Cách tiếp cận, phương pháp thẩm định giá áp dụng:', 2);
        // $section->addText(json_encode($certificate));
        // if ($this->isApartment) {
        //     $this->step8sub3Apartment($section);
        // } else {
        //     $this->step8sub3Appraise($section);
        // }
        if ($this->isApartment) {
            $this->DCC($section);
        } else {
            if ($this->isTangibleAsset) {
                $this->DCN($section);
            } else {
                $this->DTCC($section);
            }
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

    protected function DCN(Section $section)
    {
        $section->addText('   Căn cứ vào các phương pháp thẩm định giá theo Tiêu chuẩn TĐGVN, Tổ thẩm định nhận thấy:');
        $section->addText('   - Đối với phương pháp so sánh: Tài sản thẩm định gồm quyền sử dụng đất và công trình xây dựng trên đất. Tại thời điểm thẩm định, khu vực thẩm định không có các giao dịch trong đó các tài sản so sánh có quyền sử dụng đất và công trình xây dựng trên đất tương đồng với tài sản thẩm định. Do đó, trong trường hợp này tổ thẩm định không áp dụng được phương pháp so sánh để ước tính giá trị của tài sản thẩm định.');
        $section->addText('   - Đối với phương pháp vốn hóa trực tiếp và dòng tiền chiết khấu: Do khách hàng không cung cấp được thông tin dòng thu nhập do bất động sản mang lại. Vì vậy, trong trường hợp này chưa đủ điều kiện áp dụng phương pháp vốn hóa trực tiếp để ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   - Đối với phương pháp thặng dư: Phương pháp này áp dụng đối với trường hợp thửa đất trống có tiềm năng phát triển hoặc đất có CTXD có thể cải tạo, sửa chữa để khai thác có hiệu quả nhất. Do thửa đất hiện tại đã có CTXD phục vụ vào mục đích để ở đang khai thác tốt nhất và hiệu quả nhất và trong tương lai cũng chưa có thông tin quy hoạch phát triển và phương án sử dụng tối ưu hơn. Vì vậy, chưa đủ điều kiện áp dụng được phương pháp thặng dư trong trường hợp này.');
        $section->addText('   - Đối với phương pháp chi phí thay thế: Tổ thẩm định xác định giá trị công trình xây dựng của tài sản thẩm định giá dựa trên cơ sở chênh lệch giữa chi phí thay thế để tạo ra một tài sản công trình xây dựng tương tự tài sản thẩm định giá có cùng chức năng, công dụng theo giá thị trường hiện hành và giá trị hao mòn của công trình xây dựng tài sản thẩm định giá. Do đó, Tổ thẩm định nhận thấy đủ điều kiện để áp dụng phương pháp chi phí thay thế để tiến hành ước tính giá trị tài sản cần thẩm định giá là công trình xây dựng trên đất.');
        $section->addText('   Từ các nội dung trên, Tổ thẩm định nhận thấy chỉ áp dụng được phương pháp chi phí thay thế làm phương pháp chính để ước tính giá trị tài sản thẩm định. Không đủ điều kiện để áp dụng các phương pháp khác làm phương pháp kiểm tra, đối chiếu.');
        $section->addText('   “Phương pháp chi phí thay thế là phương pháp thẩm định giá xác định giá trị của tài sản thẩm định giá dựa trên cơ sở chênh lệch giữa chi phí thay thế để tạo ra một tài sản tương tự tài sản thẩm định giá có cùng chức năng, công dụng theo giá thị trường hiện hành và giá trị hao mòn của tài sản thẩm định giá. Phương pháp chi phí thay thế thuộc cách tiếp cận từ chi phí”.', ['italic' => true]);
    }

    protected function DTCC(Section $section)
    {
        $section->addText('   Căn cứ vào các phương pháp thẩm định giá theo Tiêu chuẩn TĐGVN, Tổ thẩm định nhận thấy:');
        $section->addText('   - Đối với phương pháp so sánh: Tổ thẩm định đã thu thập được các thông tin giao dịch (tối thiểu 3 giao dịch) trên thị trường của các thửa đất, BĐS tương tự. Do đó, Tổ thẩm định nhận thấy đủ điều kiện để áp dụng phương pháp so sánh để tiến hành ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   - Đối với phương pháp vốn hóa trực tiếp và dòng tiền chiết khấu: Do khách hàng không cung cấp được thông tin dòng thu nhập do bất động sản mang lại. Vì vậy, trong trường hợp này chưa đủ điều kiện áp dụng được phương pháp vốn hóa trực tiếp và dòng tiền chiết khấu để ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   - Đối với phương pháp thặng dư: Phương pháp này áp dụng đối với trường hợp thửa đất trống có tiềm năng phát triển hoặc đất có CTXD có thể cải tạo, sửa chữa để khai thác có hiệu quả nhất. Khu vực này có nhiều loại hình kinh doanh, dịch vụ khác nhau, khách hàng cũng chưa có phương án hoạt động kinh doanh trong tương lai, do đó không thể lên phương án hoạt động kinh doanh tối ưu. Vì vậy, chưa đủ điều kiện áp dụng được phương pháp thặng dư trong trường hợp này.');
        $section->addText('   - Đối với phương pháp chi phí thay thế: Tài sản thẩm định không có công trình xây dựng trên đất nên không ước tính giá trị công trình xây dựng. Do đó, tổ thẩm định giá nhận thấy chưa đủ điều kiện để áp dụng phương pháp chi phí để tiến hành ước tính giá trị tài sản cần thẩm định giá.');
        // $section->addText('   - Đối với phương pháp chi phí thay thế: Tài sản thẩm định là quyền sở hữu căn hộ nên không ước tính giá trị công trình xây dựng. Do đó, tổ thẩm định giá nhận thấy chưa đủ điều kiện để áp dụng phương pháp chi phí để tiến hành ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   Từ các nội dung trên, Tổ thẩm định nhận thấy chỉ áp dụng được phương pháp so sánh làm phương pháp chính để ước tính giá trị tài sản thẩm định. Không đủ điều kiện để áp dụng các phương pháp khác làm phương pháp kiểm tra, đối chiếu.');
        $section->addText('   “Phương pháp so sánh là phương pháp thẩm định giá, xác định giá trị của tài sản thẩm định giá dựa trên cơ sở phân tích mức giá của các tài sản so sánh để ước tính, xác định giá trị của tài sản thẩm định giá. Phương pháp so sánh thuộc cách tiếp cận từ thị trường”.', ['italic' => true]);
    }

    protected function DCC(Section $section)
    {
        $section->addText('   Căn cứ vào các phương pháp thẩm định giá theo Tiêu chuẩn TĐGVN, Tổ thẩm định nhận thấy:');
        $section->addText('   - Đối với phương pháp so sánh: Tổ thẩm định đã thu thập được các thông tin giao dịch (tối thiểu 3 giao dịch) trên thị trường của các thửa đất, BĐS tương tự. Do đó, Tổ thẩm định nhận thấy đủ điều kiện để áp dụng phương pháp so sánh để tiến hành ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   - Đối với phương pháp vốn hóa trực tiếp và dòng tiền chiết khấu: Do khách hàng không cung cấp được thông tin dòng thu nhập do bất động sản mang lại. Vì vậy, trong trường hợp này chưa đủ điều kiện áp dụng được phương pháp vốn hóa trực tiếp và dòng tiền chiết khấu để ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   - Đối với phương pháp thặng dư: Phương pháp này áp dụng đối với trường hợp thửa đất trống có tiềm năng phát triển hoặc đất có CTXD có thể cải tạo, sửa chữa để khai thác có hiệu quả nhất. Khu vực này có nhiều loại hình kinh doanh, dịch vụ khác nhau, khách hàng cũng chưa có phương án hoạt động kinh doanh trong tương lai, do đó không thể lên phương án hoạt động kinh doanh tối ưu. Vì vậy, chưa đủ điều kiện áp dụng được phương pháp thặng dư trong trường hợp này.');
        // $section->addText('   - Đối với phương pháp chi phí thay thế: Tài sản thẩm định không có công trình xây dựng trên đất nên không ước tính giá trị công trình xây dựng. Do đó, tổ thẩm định giá nhận thấy chưa đủ điều kiện để áp dụng phương pháp chi phí để tiến hành ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   - Đối với phương pháp chi phí thay thế: Tài sản thẩm định là quyền sở hữu căn hộ nên không ước tính giá trị công trình xây dựng. Do đó, tổ thẩm định giá nhận thấy chưa đủ điều kiện để áp dụng phương pháp chi phí để tiến hành ước tính giá trị tài sản cần thẩm định giá.');
        $section->addText('   Từ các nội dung trên, Tổ thẩm định nhận thấy chỉ áp dụng được phương pháp so sánh làm phương pháp chính để ước tính giá trị tài sản thẩm định. Không đủ điều kiện để áp dụng các phương pháp khác làm phương pháp kiểm tra, đối chiếu.');
        $section->addText('   “Phương pháp so sánh là phương pháp thẩm định giá, xác định giá trị của tài sản thẩm định giá dựa trên cơ sở phân tích mức giá của các tài sản so sánh để ước tính, xác định giá trị của tài sản thẩm định giá. Phương pháp so sánh thuộc cách tiếp cận từ thị trường”.', ['italic' => true]);
    }

    protected function step8Sub4Appraise(Section $section, $stt)
    {
        if ($this->isOnlyAsset) {
            if ($this->isTangibleAsset) {
                $section->addTitle('Quyền sử dụng đất:', 3);
                $section->addText('- Chi tiết xem Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
                $section->addTitle('Công trình xây dựng:', 3);
                $section->addText('- Chi tiết xem Phụ lục 2 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            } else {
                $section->addText('- Chi tiết xem Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            }
        } else {
            $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1), 3);
            if ($this->isTangibleAsset) {
                $section->addText('a) Quyền sử dụng đất:', ['italic' => true], $this->indentFistLine);
                $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
                $section->addText('b) Công trình xây dựng:', ['italic' => true], $this->indentFistLine);
                $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 2 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            } else {
                $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $this->indentFistLine);
            }
        }
    }
    protected function step8sub3Appraise(Section $section)
    {
        $section->addText('Căn cứ vào các phương pháp thẩm định giá theo Tiêu chuẩn TĐGVN, Tổ thẩm định nhận thấy:', null, $this->indentFistLine);
        $section->addListItem('Đối với phương pháp so sánh: Tài sản thẩm định giá gồm quyền sử dụng đất, công trình xây dựng trên đất. Tại thời điểm thẩm định, khu vực thẩm định không có các giao dịch trong đó các tài sản so sánh có quyền sử dụng đất, công trình xây dựng trên đất tương đồng với tài sản thẩm định. Trong trường hợp này tổ thẩm định nhận định không đủ điều kiện áp dụng được phương pháp so sánh để ước tính giá trị của tài sản thẩm định là quyền sử dụng đất và công trình xây dựng trên đất. Do đó, tổ thẩm định chỉ áp dụng phương pháp so sánh để ước tính giá trị quyền sử dụng đất.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Đối với phương pháp chiết trừ: Tổ thẩm định có thu thập được thông tin giao dịch của thửa đất có tài sản gắn liền với đất tương tự với quyền sử dụng đất cần định giá. Do đó, tổ thẩm định nhận thấy đủ điều kiện để áp dụng phương pháp chiết trừ để tiến hành ước tính giá trị quyền sử dụng đất của tài sản cần thẩm định giá bằng cách loại trừ phần giá trị tài sản gắn liền với đất ra khỏi tổng giá trị bất động sản.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Đối với phương pháp vốn hóa trực tiếp và dòng tiền chiết khấu: Do khách hàng không cung cấp được thông tin dòng thu nhập do bất động sản mang lại. Vì vậy, trong trường hợp này chưa đủ điều kiện áp dụng phương pháp vốn hóa trực tiếp để ước tính giá trị tài sản cần thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Đối với phương pháp thặng dư: Phương pháp này áp dụng đối với trường hợp thửa đất trống có tiềm năng phát triển hoặc đất có CTXD có thể cải tạo, sửa chữa để khai thác có hiệu quả nhất. Do thửa đất hiện tại đã có CTXD phục vụ vào mục đích để ở đang khai thác tốt nhất và hiệu quả nhất và trong tương lai cũng chưa có thông tin quy hoạch phát triển và phương án sửa dụng tối ưu hơn. Vì vậy, chưa đủ điều kiện áp dụng được phương pháp thặng dư trong trường hợp này.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Phương pháp chi phí thay thế: Tổ thẩm định xác định giá trị công trình xây dựng của tài sản thẩm định giá dựa trên cơ sở chênh lệch giữa chi phí thay thế để tạo ra một tài sản công trình xây dựng tương tự tài sản thẩm định giá có cùng chức năng, công dụng theo giá thị trường hiện hành và giá trị hao mòn của công trình xây dựng tài sản thẩm định giá. Do đó, Tổ thẩm định nhận thấy đủ điều kiện để áp dụng phương pháp chi phí thay thế để tiến hành ước tính giá trị tài sản cần thẩm định giá là công trình xây dựng trên đất.', 0, null, 'bullets', $this->indentFistLine);

        $section->addText('Từ các nội dung trên, Tổ thẩm định nhận thấy áp dụng được phương pháp so sánh làm phương pháp chính để ước tính giá trị tài sản thẩm định là quyền sử dụng đất và phương pháp chi phí thay thế làm phương pháp chính để ước tính giá trị tài sản thẩm định là công trình xây dựng trên đất.', ['bold' => true], $this->indentFistLine);
        $section->addText('“Phương pháp so sánh là phương pháp thẩm định giá, xác định giá trị của tài sản thẩm định giá dựa trên cơ sở phân tích mức giá của các tài sản so sánh để ước tính, xác định giá trị của tài sản thẩm định giá. Phương pháp so sánh thuộc cách tiếp cận từ thị trường”.', ['italic' => true], $this->indentFistLine);
        $section->addText('“Phương pháp chi phí thay thế là phương pháp thẩm định giá xác định giá trị của tài sản thẩm định giá dựa trên cơ sở chênh lệch giữa chi phí thay thế để tạo ra một tài sản tương tự tài sản thẩm định giá có cùng chức năng, công dụng theo giá thị trường hiện hành và giá trị hao mòn của tài sản thẩm định giá. Phương pháp chi phí thay thế thuộc cách tiếp cận từ chi phí”.', ['italic' => true], $this->indentFistLine);
    }

    protected function step9(Section $section, $certificate)
    {
        $section->addTitle('KẾT QUẢ THẨM ĐỊNH GIÁ:', 1);
        $section->addText('Trên cơ sở các tài liệu do ' . $certificate->petitioner_name . ' cung cấp, với phương pháp thẩm định giá như trên được áp dụng trong tính toán, ' . $this->companyName . ' thông báo kết quả thẩm định giá như sau:', null, $this->indentFistLine);
        if ($this->isApartment) {
            $this->step9Apartment($section);
        } else {
            if (isset($certificate->document_alter_by_bank) && $certificate->document_alter_by_bank != 0) {
                // Run another method
                $this->step9AppraiseShinnhan($section, $certificate);
            } else {
                // Run step9Appraise
                $this->step9Appraise($section, $certificate);
            }
        }
        $section->addText('Căn cứ Tiêu chuẩn thẩm định giá 05 Ban hành kèm theo Thông tư số 28/2015/TT-BTC ngày 06/3/2015 của Bộ Tài chính: Thời hạn sử dụng kết quả thẩm định giá trong chứng thư tính từ ngày phát hành là 06 tháng kể từ ngày phát hành chứng thư thẩm định giá.', null, $this->indentFistLine);
    }

    protected function printAppendix(Section $section, $certificate)
    {
        $section->addListItem('Kết quả thẩm định giá trên chỉ xác nhận giá trị thị trường cho tài sản thẩm định có đặc điểm pháp lý và đặc điểm kinh tế - kỹ thuật và hiện trạng được mô tả chi tiết tại thời điểm thẩm định được ghi trong báo cáo thẩm định giá này.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Công ty TNHH Thẩm định giá ' . $this->acronym . ' mô tả đặc điểm pháp lý và đặc điểm kinh tế - kỹ thuật của tài sản thẩm định giá dựa trên các tài liệu, chứng từ, hồ sơ pháp lý liên quan đến tài sản thẩm định giá do khách hàng cung cấp và ghi nhận hiện trạng tài sản theo sự hướng dẫn của khách hàng hoặc người hướng dẫn do khách hàng chỉ định. Trong trường hợp có sự sai khác về các đặc điểm đã mô tả có khả năng dẫn đến sự thay đổi của kết quả thẩm định giá. Khách hàng và các bên liên quan khi sử dụng kết quả thẩm định giá trên xem như đã hiểu rõ về vấn đề này và đưa ra quyết định phù hợp với mục đích thẩm định giá đã ghi trong báo cáo này', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Kết quả thẩm định giá trên được tính toán trong điều kiện thị trường bình thường tại thời điểm thẩm định giá. Những biến động bất thường của thị trường hay chính sách trong tương lai có ảnh hưởng đến giá trị của tài sản không được xem xét trong báo cáo này.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Kết quả thẩm định giá phải được sử dụng đúng đối tượng và mục đích đã ghi trong báo cáo này. Công ty TNHH Thẩm định giá ' . $this->acronym . ' không chịu trách nhiệm trong mọi trường hợp khách hàng hoặc bên thứ ba không đúng đối tượng sử dụng kết quả thẩm định giá sai mục đích.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Kết quả thẩm định giá trên chỉ có giá trị khi và chỉ khi các bên tham gia tuân thủ và hoàn thành các điều khoản trong hợp đồng cung cấp dịch vụ thẩm định giá. Trong trường hợp khách hàng không thực hiện đầy đủ các nghĩa vụ (bao gồm nghĩa vụ thanh toán) được ghi trong hợp đồng thì Công ty TNHH Thẩm định giá ' . $this->acronym . ' mặc nhiên coi là hợp đồng vô hiệu và toàn bộ Chứng thư / Báo cáo thẩm định giá đã thỏa thuận giao trước cho khách hàng (nếu có) sẽ không có giá trị pháp lý.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Chứng thư này phát hành chỉ để tư vấn giá trị tài sản cho khách hàng làm cơ sở tham khảo để xem xét cân nhắc và tự quyết định theo mục đích đã yêu cầu. Công ty TNHH Thẩm định giá ' . $this->acronym . ' không đi sâu tìm hiểu kỹ nguồn gốc chủ quyền. Chủ quyền sở hữu tài sản là do các cơ quan có thẩm quyền cấp hoặc được xác định theo quy định của pháp luật hiện hành.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Về pháp lý tài sản thẩm định giá: Khách hàng cung cấp các tài liệu, chứng từ, hồ sơ pháp lý liên quan đến tài sản thẩm định giá như trên bằng bản sao (bản photo). Khách hàng chịu trách nhiệm về tính chính xác của hồ sơ đã cung cấp. Công ty TNHH Thẩm định giá ' . $this->acronym . ' không kiểm tra sự phù hợp giữa bản chính và bản chụp hồ sơ pháp lý khách hàng cung cấp. Kết quả thẩm định giá được nêu trong báo cáo này dựa trên giả định các tài liệu, chứng từ, hồ sơ pháp lý khách hàng cung cấp là trung thực và đúng với hiện trạng pháp lý của tài sản tại thời điểm thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Khách hàng yêu cầu thẩm định giá hoặc người hướng dẫn được khách hàng chỉ định đã hướng dẫn thẩm định viên/ chuyên viên Công ty TNHH Thẩm định giá '  . $this->acronym . ' thực hiện khảo sát / thẩm định hiện trạng tài sản phải chịu hoàn toàn trách nhiệm về thông tin liên quan đến đặc điểm kinh tế - kỹ thuật, tính năng và tính pháp lý của tài sản thẩm định giá đã cung cấp cho Công ty TNHH Thẩm định giá ' . $this->acronym . ' tại thời điểm và địa điểm thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Hiện trạng của tài sản thẩm định giá được ghi nhận tại thời điểm khảo sát hiện trạng tài sản. Công ty TNHH Thẩm định giá ' . $this->acronym . ' không chịu trách nhiệm nếu có phát sinh các hư hỏng, phá bỏ, thay đổi kết cấu hiện trạng của nó hay thay đổi chủ sở hữu trong quá trình sử dụng sau thời điểm khảo sát hiện trạng tài sản thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Thẩm định viên và những người tham gia trực tiếp không có quan hệ kinh tế hoặc quyền lợi kinh tế như góp vốn cổ phần, cho vay hoặc vay vốn từ khách hàng, không là cổ đông chi phối của khách hàng hoặc ký kết hợp đồng gia công dịch vụ, đại lý tiêu thụ hàng hóa và không có xảy ra bất kỳ xung đột lợi ích nào', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('CÁC TÀI LIỆU KÈM THEO:', 1);
        $section->addListItem('Hồ sơ pháp lý tài sản thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addText('Báo cáo kết quả thẩm định giá được phát hành 03 bản chính Tiếng Việt, kèm theo Chứng thư thẩm định giá số ' . $this->certificateCode . ' ngày ' . $this->certificateShortDateText . ' tại ' . $this->companyName, ['italic' => true], array_merge($this->indentFistLine, $this->keepNext));
    }
    protected function signature(Section $section, $certificate)
    {
        $section->addTextBreak(null, null, $this->keepNext);
        $table3 = $section->addTable($this->tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), $this->cantSplit);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("Chuyên Viên Thẩm Định", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("Thẩm Định Viên Về Giá", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        if (isset($certificate->appraiserConfirm->name)) {
            $cell32->addText("KT. Tổng Giám Đốc", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $cell32->addText($certificate->appraiserConfirm->appraisePosition->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        } else {
            $cell32->addText("Tổng Giám Đốc", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        }
        $table3->addRow(Converter::inchToTwip(1.5), $this->cantSplit);
        $table3->addCell(Converter::inchToTwip(4))->addText("", null,  $this->keepNext);
        $table3->addCell(Converter::inchToTwip(4))->addText("", null,  $this->keepNext);
        $table3->addCell(Converter::inchToTwip(4))->addText("", null,  $this->keepNext);

        $table3->addRow(Converter::inchToTwip(.1));
        $cell33 = $table3->addCell(Converter::inchToTwip(4));
        $bien170 = (isset($certificate->appraiserPerform) && isset($certificate->appraiserPerform->name)) ? $certificate->appraiserPerform->name : '';
        $cell33->addText($bien170, ['bold' => true], ['align' => 'center', 'keepNext' => true]);

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

    protected function step3(Section $section, $certificate)
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
            // $split = explode("|",$law['document_num']);
            // if (count($split) > 1){
            //     $c1 = $table->addCell(2000, $this->cellVCentered);
            //     $c1->addText('Số bìa: '.$split[0], null, ['keepNext' => true]);
            //     $c1->addText('Số vào sổ cấp GCN: '.$split[1], null, ['keepNext' => true]);
            //     $split_date = explode("ngày",$law['document_date']);
            //     // $ngay = preg_replace('/\s+/', '', $split_date[0]);
            //     // $date = preg_replace('/\s+/', '', $split_date[1]);
            //     // $c1->addText(CommonService::mbUcfirst($ngay).': '.$date, null, ['keepNext' => true]);
            //     $c1->addText('Ngày: '.$split_date[1], null, ['keepNext' => true]);
            // } else {
            //     $table->addCell(2000, $this->cellVCentered)->addText($law['document_num']. $law['document_date'], null, ['keepNext' => true]);
            // }
            $table->addCell(2000, $this->cellVCentered)->addText(str_replace("\n", '<w:br/>', $law['document_num']) . $law['document_date'], null, ['keepNext' => true, 'align' => 'left']);
            $table->addCell(5000, $this->cellVCentered)->addText($law['content'], null, ['keepNext' => true]);
            $table->addCell(2000, $this->cellVCentered)->addText($law['certifying_agency']);
        }
    }

    protected function step9Appraise(Section $section, $certificate)
    {
        foreach ($this->realEstates as $stt => $realEstate) {
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
                        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered)->addText(null, null, ['keepNext' => true]);
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
                    $section->addTitle('Công trình xây dựng:', $sttLevel + 1);
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
                    $table->addCell(1000, $this->cellVCentered)->addText('Công trình xây dựng', null, ['align' => 'left', 'keepNext' => true]);
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

    protected function assetCharacteristicsApartment(Section $section, $realEstate)
    {
        $section->addTitle('Quyền sở hữu căn hộ chung cư:', 3);
        $table = $section->addTable($this->styleTable);
        $this->assetCharacteristicsHeader($table);
        $table->addRow(400, $this->cantSplit);
        $apartment = $realEstate->apartment;
        //1
        $address = $apartment->full_address ?: '';
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('1', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Pháp lý');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Địa chỉ:');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText(htmlspecialchars($address));
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Diện tích sàn');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText(number_format(floatval($realEstate->total_area), 2, ',', '.') . ' ' . $this->m2);
        //2
        $coordinateArr = explode(',', $realEstate->coordinates);
        $fullName = $apartment->appraise_asset ?: '';
        $assetName = $fullName . ' tọa lạc tại ' . htmlspecialchars($address);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('2', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Vị trí');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ X');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText($coordinateArr[0] ?: '');
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ Y');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText($coordinateArr[1] ?: '');
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Khả năng tiếp cận');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText($assetName);
        //3
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('3', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Số tầng');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Độ cao');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText('Tầng ' . $apartment->apartmentAssetProperties->floor->name);

        //4
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('4', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Số phòng ngủ ');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText($apartment->apartmentAssetProperties->wc_num ? $apartment->apartmentAssetProperties->wc_num . ' phòng' : '');


        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('5', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Số phòng WC ');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText($apartment->apartmentAssetProperties->bedroom_num ? $apartment->apartmentAssetProperties->bedroom_num . ' phòng' : '');
        //4
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('6', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Hướng nhìn');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Hướng chính');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText(CommonService::mbCaseTitle($apartment->apartmentAssetProperties->direction->description));
        //5
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('7', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Nội thất');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tình trạng');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText(CommonService::mbCaseTitle($apartment->apartmentAssetProperties->furnitureQuality->description));
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Mô tả');
        $table->addCell($this->rowFourthWidth, ['borderRightSize' => 'none'])->addText(str_replace("\n", '<w:br/>   ', $apartment->apartmentAssetProperties->description) ?: '');
        //6
        $utiDescriptionArr = CommonService::getUtilitiesDescription($apartment->apartmentAssetProperties->utilities);
        $utiDescriptionStr = implode(', ', $utiDescriptionArr);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('8', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Tiện ích');
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tiện ích nội khu');
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])->addText($utiDescriptionStr);
        //7
        if (CommonService::getPlaningInfo($realEstate->real_estate_id)) {
            $table->addRow(400, $this->cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('7', null, $this->cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Thông tin quy hoạch', null, ['align' => 'left']);
            $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('', null, ['align' => 'left']);
            $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(str_replace("\n", '<w:br/>   ', CommonService::getPlaningInfo($realEstate->real_estate_id)), null, ['align' => 'left']);
        }
    }
    protected function nationalName(PhpWord $phpWord, $data)
    {
        if ($this->isPrintNational) {
            $section = $phpWord->addSection($this->styleNationalSection);
            $table1 = $section->addTable($this->tableBasicStyle);
            $table1->addRow(1000);
            // $cell11 = $table1->addCell(Converter::cmToTwip(1), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $imgName = env('STORAGE_IMAGES', 'images') . '/' . 'company_logo.png';
            // $cell11->addImage(storage_path('app/public/'.$imgName), $this->styleImageLogo);
            $cell12 = $table1->addCell(Converter::inchToTwip(3), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell12->addText(CommonService::downLineCompanyName($this->companyName, $this->companyDownLine), ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            $cell12->addImage(storage_path('app/public/' . $imgName), $this->styleImageHeader1);
            // $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell13 = $table1->addCell(Converter::inchToTwip(5), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell13->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            $cell13->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true], $this->styleAlignCenter);
            $cell13->addText("-----o0o-----", ['bold' => true], $this->styleAlignCenter);
            $indentLeft = $this->marginLeftContent - $this->marginLeftNational;
            $indentRight = $this->marginRightContent - $this->marginRightNational;
            $this->printFooter($section, $data, $indentLeft, $indentRight);
        }
    }

    protected function step9AppraiseShinnhan(Section $section, $certificate)
    {
        $propertyDetailtotalZoningAll = 0;
        foreach ($this->realEstates as $stt => $realEstate) {
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
                $propertyDetailTotalZoning = 0;
                $propertyDetailTotalNoZoning = 0;
                $zoningAppraise = [];
                $noZoningAppraise = [];
                $totalArea = 0;
                $totalAreaZoning = 0;
                $totalAreaNoZoning = 0;

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
                            $propertyDetailTotalZoning += $total;
                            $propertyDetailtotalZoningAll += $total;
                            $totalArea += $dientich;
                            $totalAreaZoning += $dientich;
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
                            $propertyDetailTotalNoZoning += $total;
                            $totalArea += $dientich;
                            $totalAreaNoZoning += $dientich;
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
                $table->addRow(400, $this->cantSplit);
                $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1500, $this->cellVCentered)->addText(number_format($totalAreaNoZoning, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1500, $this->cellVCentered)->addText('', null, ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($propertyDetailTotalNoZoning, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                //Sumary table
                // $totalAll = $propertyDetailTotal;
                $totalAll = CommonService::getCertificateAssetPrice($appraise, 'total_asset_price');
                $totalAllRound = $realEstate->total_price;


                if (!$this->isOnlyAsset) {
                    $table->addRow(400, $this->cantSplit);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left']);
                    $table->addCell(1000, $this->cellVCentered)->addText(number_format($propertyDetailTotalNoZoning, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
                } else {
                    $table->addRow(400, $this->cantSplit);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $this->cellVCentered)->addText(number_format($propertyDetailTotalNoZoning, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
                    $table->addRow(400, $this->cantSplit);
                    $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 5))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($propertyDetailTotalNoZoning) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center', 'keepNext' => true]);
                }

                $section->addText('', ['italic' => false], ['align' => 'left', 'keepNext' => true]);
                $section->addText('Giá trị quyền sử dụng đất phần diện tích đất không phù hợp quy hoạch của bất động sản thẩm định giá (mang tính chất tham khảo) là:', ['italic' => true], ['align' => 'left', 'keepNext' => true]);
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($this->styleTable);
                $table->addRow(400, $this->rowHeader);
                $table->addCell(4000, $this->cellVCentered)->addText('Quyền sử dụng đất', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('MĐSD', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('Diện tích (' . $this->m2 . ')', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Đơn giá ', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Thành tiền', ['bold' => true], $this->cellHCenteredKeepNext);
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
                $table->addRow(400, $this->cantSplit);
                $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1500, $this->cellVCentered)->addText(number_format($totalAreaZoning, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1500, $this->cellVCentered)->addText('', null, ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($propertyDetailTotalZoning, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
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
                $propertyDetailTotalNoZoning = 0;
                $propertyDetailTotalZoning = 0;
                $zoningAppraise = [];
                $noZoningAppraise = [];
                $totalArea = 0;
                $totalAreaNoZoning = 0;
                $totalAreaZoning = 0;
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
                            // $propertyDetailTotal += $total;
                            $propertyDetailTotalZoning += $total;
                            $propertyDetailtotalZoningAll += $total;
                            $totalArea += $dientich;
                            $totalAreaZoning += $dientich;
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
                            $propertyDetailTotalNoZoning += $total;
                            $totalArea += $dientich;
                            $totalAreaNoZoning += $dientich;
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
                $table->addRow(400, $this->cantSplit);
                // $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);

                $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($totalAreaNoZoning, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText('', ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($propertyDetailTotalNoZoning, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);

                $section->addText('', ['italic' => false], ['align' => 'left', 'keepNext' => true]);
                $section->addText('Giá trị quyền sử dụng đất phần diện tích đất không phù hợp quy hoạch của bất động sản thẩm định giá (mang tính chất tham khảo) là:', ['italic' => true], ['align' => 'left', 'keepNext' => true]);
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($this->styleTable);
                $table->addRow(400, $this->rowHeader);
                $table->addCell(4000, $this->cellVCentered)->addText('Quyền sử dụng đất', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('MĐSD', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(1500, $this->cellVCentered)->addText('Diện tích (' . $this->m2 . ')', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Đơn giá ', ['bold' => true], $this->cellHCenteredKeepNext);
                $table->addCell(2500, $this->cellVCentered)->addText('Thành tiền', ['bold' => true], $this->cellHCenteredKeepNext);
                $isFirst = 0;
                foreach ($zoningAppraise as $item) {
                    $table->addRow(400, $this->cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $this->cellRowSpan)->addText('Phần diện tích đất vi phạm quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $this->cellRowContinue, [], $this->cellHCentered)->addText(null, null, ['keepNext' => true]);
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
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($totalAreaZoning, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText('', ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($propertyDetailTotalZoning, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);

                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $section->addTitle('Công trình xây dựng:', $sttLevel + 1);
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
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($propertyDetailTotalNoZoning, 0, ',', '.'), null, array('align' => 'right'));
                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $table->addRow(400, $this->cantSplit);
                    //$table->addCell(1000, $this->cellVCentered)->addText('2', null, $this->cellHCentered);
                    $table->addCell(1000, $this->cellVCentered)->addText('Công trình xây dựng', null, ['align' => 'left', 'keepNext' => true]);
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

                $totalAllRound = $realEstate->total_price - $propertyDetailTotalZoning;

                $table->addRow(400, $this->cantSplit);
                $table->addCell(1000, $this->cellVCentered)->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true], ['align' => 'right']);

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

            $totalAllRound = $totalAll - $propertyDetailtotalZoningAll;

            $table->addRow(400, $this->cantSplit);
            $table->addCell(1000, $this->cellVCentered)->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
            $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
            $table->addRow(400, $this->cantSplit);
            //$table->addCell(1000, $this->cellVCentered)->addText('', null, $this->);
            $table->addCell(1000, $this->cellVCentered)->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
            //PHP_ROUND_HALF_DOWN
            $table->addCell(1000, $this->cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
            $table->addRow(400, $this->cantSplit);
            $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 2))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($totalAllRound) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center']);
        }
    }
}
