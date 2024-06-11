<?php

namespace App\Services\Document\Certificate;

use App\Services\CommonService;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\PhpWord;

class ReportCertificateNova extends ReportCertificate
{
    protected function content1(Section $section, $certificate)
    {
        $section->addListItem("Căn cứ Hợp đồng thẩm định giá số " . $this->contractCode . ' ' . $this->documentLongDateText . " giữa " . $this->companyName . " và " . $certificate->petitioner_name . '.', 0, [], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addListItem("Căn cứ Báo cáo kết quả thẩm định giá, " . $this->companyName . " cung cấp Chứng thư thẩm định giá với các nội dung sau đây:", 0, [], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addTitle("Khách hàng thẩm định giá:", 2);
        $section->addListItem("Khách hàng: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets',  array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addListItem("Địa chỉ: " . htmlspecialchars($certificate->petitioner_address), 0, [], 'bullets',  array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addTitle("Thông tin về tài sản thẩm định giá:", 2);
        $section->addListItem("Tên tài sản: " . $this->getAssetName($certificate), 0, [], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addListItem("Địa chỉ: " . $this->getAssetAddress($certificate), 0, [], 'bullets',  array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addListItem("Nội dung chi tiết xem tại Mục IV, Báo cáo kết quả thẩm định giá.", 0, [], 'bullets', array_merge(['keepNext' => false]));
        $appraise_date = date_create($certificate->appraise_date);
        $textRun = $section->addTextRun(array(
            'styleName' => 'Heading2',
            'keepNext' => false
        ));
        $textRun->addText("Thời điểm thẩm định giá: ", ['bold' => true], ['keepNext' => false]);
        $textRun->addText('Tháng ' . date_format($appraise_date, "m/Y") . '.', ['bold' => false], ['keepNext' => false]);
        $appraisePurpose = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        $textRun = $section->addTextRun(array(
            'styleName' => 'Heading2',
            'keepNext' => false
        ));
        $textRun->addText("Mục đích thẩm định giá: ", ['bold' => true], ['keepNext' => false]);
        if ($appraisePurpose === 'Vay vốn ngân hàng')
            $appraisePurpose = 'Tư vấn giá trị tài sản để ngân hàng tham khảo và xem xét quyết định hạn mức để cấp tín dụng';
        $textRun->addText($appraisePurpose, ['bold' => false], ['keepNext' => false]);
        $textRun = $section->addTextRun(array(
            'styleName' => 'Heading2',
            'keepNext' => false
        ));
        $textRun->addText("Căn cứ pháp lý: ", ['bold' => true], ['keepNext' => false]);
        $textRun->addText("Chi tiết xem tại Mục II, Báo cáo kết quả thẩm định giá.", ['bold' => false], ['keepNext' => false]);
        $textRun = $section->addTextRun(array(
            'styleName' => 'Heading2',
            'keepNext' => false
        ));
        $textRun->addText("Cơ sở giá trị của tài sản thẩm định giá: ", ['bold' => true], ['keepNext' => false]);
        $textRun->addText("Chi tiết xem tại Mục V, Báo cáo kết quả thẩm định giá.", ['bold' => false], ['keepNext' => false]);
        $textRun = $section->addTextRun(array(
            'styleName' => 'Heading2',
            'keepNext' => false
        ));
        $textRun->addText("Giả thiết và giả thiết đặc biệt: ", ['bold' => true], ['keepNext' => false]);
        $textRun->addText("Chi tiết xem tại Mục VII, Báo cáo kết quả thẩm định giá.", ['bold' => false], ['keepNext' => false]);
        $textRun = $section->addTextRun(array(
            'styleName' => 'Heading2',
            'keepNext' => false
        ));
        $textRun->addText("Cách tiếp cận, phương pháp thẩm định giá: ", ['bold' => true], ['keepNext' => false]);
        $textRun->addText("Chi tiết xem tại Mục VIII, Báo cáo kết quả thẩm định giá.", ['bold' => false], ['keepNext' => false]);
        $section->addTitle("Kết quả thẩm định giá: ", 2);
        $section->addText("Với thông tin như trên, " . $this->companyName . " thông báo kết quả ước tính giá trị tài sản như sau:", [], array_merge($this->indentFistLine, ['keepNext' => false]));
        $totalAll = CommonService::getTotalRealEstatePrice($certificate->realEstate);
        $section->addText(number_format($totalAll, 0, ',', '.') . " đồng", ['bold' => true], array_merge($this->styleAlignCenter, ['keepNext' => false]));
        $section->addText("(Bằng chữ: " . ucfirst(CommonService::convertNumberToWords($totalAll)) . " đồng)", ['italic' => true, 'bold' => true], array_merge($this->styleAlignCenter, ['keepNext' => false]));
        $section->addText("(Chi tiết xem tại phần IX, Báo cáo kết quả thẩm định giá kèm theo.)", ['italic' => true], array_merge($this->styleAlignCenter, ['keepNext' => false]));
        $section->addTitle("Những điều khoản loại trừ và hạn chế của kết quả thẩm định giá:", 2);
        $section->addListItem("Nội dung chi tiết xem tại Mục X, Báo cáo kết quả thẩm định giá.", 0, [], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addTitle("Thời hạn có hiệu lực của kết quả thẩm định giá:", 2);
        $section->addListItem("Kết quả thẩm định giá có hiệu lực trong thời hạn 06 tháng kể từ ngày phát hành chứng thư (nếu thị trường không có biến động nhiều).", 0, [], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addTitle("Các tài liệu kèm theo:", 2);
        $section->addListItem("Báo cáo kết quả thẩm định giá số " . $this->reportCode . ' ngày ' . $this->certificateShortDateText, 0, [], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addText('', [], ['borderBottomSize' => 6, 'underline' => 'dash']);
        $section->addListItem("Chứng thư phát hành có kèm theo Báo cáo kết quả TĐG và các phụ lục.", 0, ['italic' => true], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addListItem("Chứng thư thẩm định giá được phát hành 03 bản chính tiếng Việt, cấp cho khách hàng 02 bản, lưu tại " . $this->companyName . " 01 bản và có giá trị pháp lý như nhau.", 0, ['italic' => true], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));
        $section->addListItem("Mọi hình thức sao chép chứng thư thẩm định giá không có sự đồng ý bằng văn bản của " . $this->companyName . " đều là hành vi vi phạm pháp luật.", 0, ['italic' => true], 'bullets', array_merge($this->indentFistLine, ['keepNext' => false]));

        $section->addTextBreak(null, null);
    }
    // protected function signature(Section $section, $certificate)
    // {
    //     $section->addTextBreak(null, null, $this->keepNext);
    //     $table3 = $section->addTable($this->tableBasicStyle);
    //     $table3->addRow(Converter::inchToTwip(.1), $this->cantSplit);
    //     $cell31 = $table3->addCell(Converter::inchToTwip(4));
    //     $cell31->addText("Chuyên Viên Thẩm Định", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //     $cell31 = $table3->addCell(Converter::inchToTwip(4));
    //     $cell31->addText("Thẩm Định Viên Về Giá", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //     $cell32 = $table3->addCell(Converter::inchToTwip(4));
    //     if(isset($certificate->appraiserConfirm->name)) {
    //         $cell32->addText("KT. Tổng Giám Đốc", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //         $cell32->addText( $certificate->appraiserConfirm->appraisePosition->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //     } else {
    //         $cell32->addText("Tổng Giám Đốc", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //     }
    //     $table3->addRow(Converter::inchToTwip(1.5), $this->cantSplit);
    //     $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);
    //     $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);
    //     $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);

    //     $table3->addRow(Converter::inchToTwip(.1));
    //     $cell33 = $table3->addCell(Converter::inchToTwip(4));
    //     $bien170 = (isset($certificate->appraiserPerform) && isset($certificate->appraiserPerform->name)) ? $certificate->appraiserPerform->name : '';
    //     $cell33->addText($bien170, ['bold' => true], ['align' => 'center', 'keepNext' => true]);

    //     $cell33 = $table3->addCell(Converter::inchToTwip(4));
    //     $bien171 = (isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : '';
    //     $cell33->addText($bien171, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //     $appraiserNumber =   isset($certificate->appraiser) ? $certificate->appraiser->appraiser_number : '';
    //     $cell33->addText("Số thẻ TĐV về giá: " . $appraiserNumber, ['bold' => true], ['align' => 'center']);
    //     $cell34 = $table3->addCell(Converter::inchToTwip(4));

    //     $appraiserManager = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->name : $certificate->appraiserManager->name;
    //     $appraiserManagerNumber = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->appraiser_number : $certificate->appraiserManager->appraiser_number;
    //     $bien172 = $appraiserManager;
    //     $cell34->addText($bien172, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //     $cell34->addText("Số thẻ TĐV về giá: " . $appraiserManagerNumber, ['bold' => true], ['align' => 'center']);
    // }
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
}
