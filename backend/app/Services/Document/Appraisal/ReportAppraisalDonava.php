<?php
namespace App\Services\Document\Appraisal;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use App\Models\Appraiser;
use App\Models\Dictionary;
use PhpOffice\PhpWord\Element\Table;
use Carbon\Carbon;

class ReportAppraisalDonava extends ReportAppraisal
{
    protected $marginTopNational = 1080;
    protected $marginBottomNational = 600;
    protected $marginRightNational = 700;
    protected $marginLeftNational = 900;

    protected $marginTopContent = 1151;
    protected $marginBottomContent = 1151;
    protected $marginRightContent = 1151;
    protected $marginLeftContent = 1729;

    protected function step1Sub2($section, $certificate)
    {
        $comAcronym = !empty($this->companyAcronym) ?  ' (' . mb_strtoupper($this->companyAcronym) . ')' : '';
        $section->addTitle('Thông tin về doanh nghiệp thẩm định giá:', 2);
        $section->addListItem('Doanh nghiệp: ' . $this->companyName .  $comAcronym, 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' .  htmlspecialchars($this->companyAddress), 0, null, 'bullets');
        $section->addListItem("Điện thoại: " . $this->companyPhone . "\tFax: " . $this->companyFax, 0, null, 'bullets', 'leftTab');
        $section->addListItem('Họ và tên Tổng Giám đốc: ' . ((isset($certificate->appraiserManager) && isset($certificate->appraiserManager->name)) ? $certificate->appraiserManager->name : ''), 0, null, 'bullets');
        $section->addListItem('Họ và tên Thẩm định viên: ' . ((isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : ''), 0, null, 'bullets');
        $section->addListItem('Kiểm soát viên: Trần Văn Luân', 0, null, 'bullets');
        $section->addListItem('Người lập báo cáo: ' . (isset($certificate->createdBy->name) ? $certificate->createdBy->name : ''), 0, null, 'bullets');

    }

    protected function step7(Section $section, $certificate)
    {
        $section->addTitle('CÁC GIẢ THIẾT VÀ GIẢ THIẾT ĐẶC BIỆT:', 1);
        $section->addListItem('Các hồ sơ, tài liệu về tài sản do khách hàng cung cấp là đầy đủ và tin cậy', 0, null, 'bullets');
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
    }
    protected function printAppendix($section, $certificate)
    {
        $section->addListItem('Thông tin quy hoạch được DONAVA tham chiếu tại ứng dụng DNAILIS của Trung tâm Công nghệ thông tin Sở Tài nguyên và Môi trường Đồng Nai tại thời điểm Thẩm định giá, ' . $this->acronym . ' loại trừ trong trường hợp có cập nhật hoặc thay đổi sau thời điểm phát hành chứng thư.', 0, null, 'bullets', array_merge($this->indentFistLine, $this->keepNext));
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
            $cell32->addText("KT. ĐẠI DIỆN PHÁP LUẬT", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $cell32->addText( $certificate->appraiserConfirm->appraisePosition->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        } else {
            // $cell32->addText("TỔNG GIÁM ĐỐC", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $appraise_manager_id = $certificate->appraiser_manager_id;
            $appraiser = Appraiser::where('id', $appraise_manager_id)->first();
            $position = Dictionary::where('id', $appraiser->appraise_position_id)->first();
            $cell32->addText("ĐẠI DIỆN PHÁP LUẬT", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $cell32->addText( $position->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
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
