<?php
namespace App\Services\Document\Certificate;

use App\Services\CommonService;
use App\Services\Document\DocumentInterface\Report;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\PhpWord;

class ReportCertificate extends Report
{
    protected function nationalName(PhpWord $phpWord, $data)
    {
        if ($this->isPrintNational) {
            $section = $phpWord->addSection($this->styleNationalSection);
            $table1 = $section->addTable($this->tableBasicStyle);
            $table1->addRow(1000);
            $cell11 = $table1->addCell(Converter::cmToTwip(1), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $imgName = env('STORAGE_IMAGES','images').'/'.'company_logo.png';
            $cell11->addImage(storage_path('app/public/'.$imgName), $this->styleImageLogo);
            $cell12 = $table1->addCell(Converter::inchToTwip(3), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell12->addText(CommonService::downLineCompanyName($this->companyName, $this->companyDownLine), ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            // $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell13 = $table1->addCell(Converter::inchToTwip(4), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell13->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            $cell13->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true], $this->styleAlignCenter);
            $indentLeft = $this->marginLeftContent - $this->marginLeftNational;
            $indentRight = $this->marginRightContent - $this->marginRightNational;
            $this->printFooter($section, $data, $indentLeft, $indentRight);
        }
    }
    public function getFooterString($data)
    {
        $data = (Object)$data;
        $createdName =  $this->createdName;
        if(isset($data->document_date)&&!empty(trim($data->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $data->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        $reportID = 'HSTD_'. $data->id;
        // return mb_strtoupper($this->envDocument)  .'/'. $createdName.'/'.$yearCVD.'/'.$reportID;
        return mb_strtoupper($this->acronym)  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID;
    }
    public function getReportName()
    {
        return '1_CT';
    }
    public function printContent(Section $section, $data)
    {
        $this->content1($section, $data);
        // $this->signature($section, $data);
    }
    public function printTitle(Section $section, $data)
    {
        $table2 = $section->addTable($this->tableBasicStyle);
        $table2->addRow(Converter::inchToTwip(.1));
        $cell21 = $table2->addCell(Converter::inchToTwip(2));
        $cell21->addText("Số: ". $this->certificateCode, null, array_merge($this->styleAlignCenter, ['spaceBefore' => 200]));
        $cell22 = $table2->addCell(Converter::inchToTwip(5));
        $cell22->addText(ucfirst($this->certificateLongDateText), null, array_merge($this->styleAlignRight, ['spaceBefore' => 200]));

        $section->addText("CHỨNG THƯ THẨM ĐỊNH GIÁ", ['bold' => true, 'size' => '18'], array_merge($this->styleAlignCenter, ['spaceBefore' => 320]));
        $section->addText("Kính gửi: " . $data->petitioner_name, ['bold' => true, 'size' => '14'], array_merge($this->styleAlignCenter, ['spaceAfter' => 300]));
    }
    protected function getAssetName($certificate)
    {
        $name_assets = '';
        $assets = $certificate->realEstate;
        foreach ($assets as $index => $item) {
            $name_assets .= ($index > 0) ? " và " : "";
            $name_assets .= $item->appraise_asset;
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
    protected function content1(Section $section, $certificate)
    {
        $section->addListItem("Căn cứ Hợp đồng thẩm định giá số " . $this->contractCode . ' ' . $this->documentLongDateText . " giữa " . $this->companyName . " và " . $certificate->petitioner_name . '.', 0 , [], 'bullets', $this->indentFistLine);
        $section->addListItem("Căn cứ Báo cáo kết quả thẩm định giá, " . $this->companyName . " cung cấp Chứng thư thẩm định giá với các nội dung sau đây:",0 , [], 'bullets', $this->indentFistLine);
        $section->addTitle("Khách hàng thẩm định giá:", 2);
        $section->addListItem("Khách hàng: " . $certificate->petitioner_name,0 , [], 'bullets', $this->indentFistLine);
        $section->addListItem("Địa chỉ: " . $certificate->petitioner_address,0 , [], 'bullets', $this->indentFistLine);
        $section->addTitle("Thông tin về tài sản thẩm định giá:", 2);
        $section->addListItem("Tên tài sản: " . $this->getAssetName($certificate),0 , [], 'bullets', $this->indentFistLine);
        // $section->addListItem("Địa chỉ: " . $this->getAssetAddress($certificate),0 , [], 'bullets', $this->indentFistLine);
        $address = $this->getAssetAddress($certificate);
        if (!empty($address)) {
            $listTmp = $section->addListItemRun(0, 'bullets');
            $listTmp->addText('Địa chỉ: ', ['bold' => true], []);
            $listTmp->addText($address . '.');
        }
        $section->addListItem("Nội dung chi tiết xem tại Mục IV, Báo cáo kết quả thẩm định giá.",0 , [], 'bullets', $this->indentFistLine);
        $appraise_date = date_create($certificate->appraise_date);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Thời điểm thẩm định giá: ", ['bold' => true]);
        $textRun->addText('Tháng ' . date_format($appraise_date, "m/Y") . '.');
        $bien101 = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Mục đích thẩm định giá: ", ['bold' => true]);
        $textRun->addText($bien101);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Căn cứ pháp lý: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục II, Báo cáo kết quả thẩm định giá.");
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Cơ sở giá trị của tài sản thẩm định giá: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục V, Báo cáo kết quả thẩm định giá.");
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Giả thiết và giả thiết đặc biệt: ", ['bold' => true]);
        $textRun->addText($certificate->document_description);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Cách tiếp cận, phương pháp thẩm định giá: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục VIII, Báo cáo kết quả thẩm định giá.", null, ['keepNext' => false]);
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
