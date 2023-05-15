<?php
namespace App\Services\Document\Certificate;

use App\Services\CommonService;

class ReportCertificateDonava extends ReportCertificate
{

    protected $marginTopNational = 1080;
    protected $marginBottomNational = 600;
    protected $marginRightNational = 700;
    protected $marginLeftNational = 900;

    protected $marginTopContent = 1151;
    protected $marginBottomContent = 1151;
    protected $marginRightContent = 1151;
    protected $marginLeftContent = 1729;

    protected function content1($section, $certificate)
    {
        $section->addListItem("Căn cứ Hợp đồng thẩm định giá số " . $this->contractCode . ' ' . $this->documentLongDateText . " giữa " . $this->companyName . " và " . $certificate->petitioner_name . '.', 0 , [], 'bullets', $this->indentFistLine);
        $section->addListItem("Căn cứ Báo cáo kết quả thẩm định giá, " . $this->companyName . " cung cấp Chứng thư thẩm định giá với các nội dung sau đây:",0 , [], 'bullets', $this->indentFistLine);
        $section->addTitle("Khách hàng thẩm định giá:", 2);
        $section->addListItem("Khách hàng: " . $certificate->petitioner_name,0 , [], 'bullets', $this->indentFistLine);
        $section->addListItem("Địa chỉ: " . $certificate->petitioner_address,0 , [], 'bullets', $this->indentFistLine);
        $section->addTitle("Thông tin về tài sản thẩm định giá:", 2);
        $section->addListItem("Tên tài sản: " . $this->getAssetName($certificate),0 , [], 'bullets', $this->indentFistLine);
        $section->addListItem("Nội dung chi tiết xem tại Mục IV, Báo cáo kết quả thẩm định giá.",0 , [], 'bullets', $this->indentFistLine);
        $appraise_date = date_create($certificate->appraise_date);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Thời điểm thẩm định giá: ", ['bold' => true]);
        $textRun->addText('Tháng ' . date_format($appraise_date, "m/Y") . '.', ['bold' => false]);
        $bien101 = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Mục đích thẩm định giá: ", ['bold' => true]);
        $textRun->addText($bien101, ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Căn cứ pháp lý: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục II, Báo cáo kết quả thẩm định giá.", ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Cơ sở giá trị của tài sản thẩm định giá: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục V, Báo cáo kết quả thẩm định giá.", ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Giả thiết và giả thiết đặc biệt: ", ['bold' => true]);
        $textRun->addText($certificate->document_description, ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Cách tiếp cận, phương pháp thẩm định giá: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục VIII, Báo cáo kết quả thẩm định giá.", ['bold' => false]);
        $section->addTitle("Kết quả thẩm định giá: ", 2);
        $section->addText("Trên cơ sở các tài liệu do khách hàng cung cấp, dựa trên cách tiếp cận và phương pháp thẩm định giá được áp dụng trong tính toán, " . $this->companyName . " ước tính giá trị tài sản như sau:", [], array_merge($this->indentFistLine, $this->keepNext));
        $totalAll = CommonService::getTotalRealEstatePrice($certificate->realEstate);
        $section->addText(number_format($totalAll, 0, ',', '.') . " đồng", ['bold' => true], array_merge($this->keepNext, $this->styleAlignCenter));
        $section->addText("(Bằng chữ: " . ucfirst(CommonService::convertNumberToWords($totalAll)) . " đồng)", ['italic' => true, 'bold' => true], $this->styleAlignCenter);
        $section->addText($this->companyName . " thông báo kết quả thẩm định giá đến " . $certificate->petitioner_name . " để thực hiện theo mục đích thẩm định giá và thời điểm thẩm định giá.", [], $this->indentFistLine);
        $section->addTitle("Những điều khoản loại trừ và hạn chế của kết quả thẩm định giá:", 2);
        $section->addListItem("Nội dung chi tiết xem tại Mục X, Báo cáo kết quả thẩm định giá.",0 , [], 'bullets', $this->indentFistLine);
        $section->addTitle("Thời hạn có hiệu lực của kết quả thẩm định giá:", 2);
        $section->addListItem("Kết quả thẩm định giá có hiệu lực trong thời hạn 06 tháng kể từ ngày phát hành chứng thư (nếu thị trường không có biến động nhiều).",0 , [], 'bullets', $this->indentFistLine);
        $section->addText('', [], ['borderBottomSize' => 6, 'underline' => 'dash']);
        $section->addListItem("Chứng thư phát hành có kèm theo Báo cáo kết quả TĐG và các phụ lục.",0 , ['italic' => true], 'bullets', $this->indentFistLine);
        $section->addListItem("Chứng thư thẩm định giá được phát hành 03 bản chính tiếng Việt, cấp cho khách hàng 02 bản, lưu tại " . $this->companyName . " 01 bản.",0 , ['italic' => true], 'bullets', $this->indentFistLine);
        $section->addListItem("Mọi hình thức sao chép chứng thư thẩm định giá không có sự đồng ý bằng văn bản của " . $this->companyName . " đều là hành vi vi phạm pháp luật.",0 , ['italic' => true], 'bullets', array_merge($this->indentFistLine, $this->keepNext));

        $section->addTextBreak(null, null, $this->keepNext);
    }
}
