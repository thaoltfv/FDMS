<?php
namespace App\Services\Document\Appraisal;

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
        $section->addListItem('Địa chỉ: ' . $this->companyAddress, 0, null, 'bullets');
        $section->addListItem("Điện thoại: " . $this->companyPhone . "\tFax: " . $this->companyFax, 0, null, 'bullets', 'leftTab');
        $section->addListItem('Họ và tên Tổng Giám đốc: ' . ((isset($certificate->appraiserManager) && isset($certificate->appraiserManager->name)) ? $certificate->appraiserManager->name : ''), 0, null, 'bullets');
        $section->addListItem('Họ và tên Thẩm định viên: ' . ((isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : ''), 0, null, 'bullets');
        $section->addListItem('Kiểm soát viên: Trần Văn Luân', 0, null, 'bullets');
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
    }
    protected function printAppendix($section, $certificate)
    {
        $section->addListItem('Thông tin quy hoạch được DONAVA tham chiếu tại ứng dụng DNAILIS của Trung tâm Công nghệ thông tin Sở Tài nguyên và Môi trường Đồng Nai tại thời điểm Thẩm định giá, ' . $this->acronym . ' loại trừ trong trường hợp có cập nhật hoặc thay đổi sau thời điểm phát hành chứng thư.', 0, null, 'bullets', array_merge($this->indentFistLine, $this->keepNext));
    }
}
