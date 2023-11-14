<?php
namespace App\Services\Document\Certificate;

use App\Services\CommonService;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\PhpWord;

class ReportCertificateVavc extends ReportCertificate
{
    public function printTitle(Section $section, $data)
    {
        $table2 = $section->addTable($this->tableBasicStyle);
        $table2->addRow(Converter::inchToTwip(.1));
        $cell21 = $table2->addCell(Converter::inchToTwip(2));
        $cell21->addText("Số: ". $this->certificateCode, ["name" => "Cambria", 'size' => '12'], array_merge($this->styleAlignCenter, ['spaceBefore' => 200]));
        $cell22 = $table2->addCell(Converter::inchToTwip(5));
        $cell22->addText(ucfirst('Hà Nội ,' . $this->certificateLongDateText), ["name" => "Cambria", 'italic' => true, 'size' => '12'], array_merge($this->styleAlignRight, ['spaceBefore' => 200]));

        $section->addText("CHỨNG THƯ THẨM ĐỊNH GIÁ", ["name" => "Cambria", 'bold' => true, 'size' => '18', 'color' => '1f497d'], array_merge($this->styleAlignCenter, ['spaceBefore' => 320]));
        $section->addText("Kính gửi: " . $data->petitioner_name, ['bold' => true, 'size' => '13'], array_merge($this->styleAlignCenter, ['spaceAfter' => 300]));
    }
        protected function processData ($data, $documentConfig)
    {
        $this->envDocument = config('services.document_service.document_module');
        $this->createdName = !empty($data->createdBy) ? CommonService::withoutAccents($data->createdBy->name) : '';
        $this->logoUrl = storage_path('app/public/' . env('STORAGE_IMAGES','images').'/'.'company_logo.png');
        if (!empty($documentConfig)) {
            $this->documentConfig = $documentConfig;
            $this->certificateNumberSuffix = $documentConfig->where('slug', 'certificatte_number_suffix')->first()->value ?: '';
            $this->certificateNumberPrefix = $documentConfig->where('slug', 'certificatte_number_prefix')->first()->value ?: '';
            $this->documentNumberSuffix = $documentConfig->where('slug', 'document_number_suffix')->first()->value ?: '';
            $this->documentNumberPrefix = $documentConfig->where('slug', 'document_number_prefix')->first()->value ?: '';
            $this->contractCodeSuffix = $documentConfig->where('slug', 'contract_code_suffix')->first()->value ?: '';
            $this->contractCodePrefix = $documentConfig->where('slug', 'contract_code_prefix')->first()->value ?: '';
            $this->documentWatermask = $documentConfig->where('slug', 'print_watermask')->first()->value ?: '';

        }
        // Report code
        if(isset($data->certificate_num) && !empty(trim($data->certificate_num))) {
            $this->reportCode = $this->documentNumberPrefix . $data->certificate_num . $this->documentNumberSuffix;
        } else {
            $this->reportCode = $this->documentNumberPrefix . '            ' . $this->documentNumberSuffix;
        }
        // Certificate code
        if(isset($data->certificate_num) && !empty(trim($data->certificate_num))) {
            $this->certificateCode = $this->certificateNumberPrefix . $data->certificate_num . $this->certificateNumberSuffix;
        } else {
            $this->certificateCode = $this->certificateNumberPrefix . '            ' . $this->certificateNumberSuffix;
        }
        //Contract code
        if(isset($data->document_num) && !empty(trim($data->document_num))) {
            $this->contractCode = $this->contractCodePrefix . $data->document_num . $this->contractCodeSuffix;
        } else {
            $this->contractCode = $this->contractCodePrefix . '            ' . $this->contractCodeSuffix;
        }
        if(!empty($data->certificate_date)) {
            $certificateDate = date_create($data->certificate_date);
            $this->certificateShortDateText = $certificateDate->format("d/m/Y");
            $this->certificateLongDateText = "ngày " . $certificateDate->format('d') . " tháng " . $certificateDate->format('m') . " năm " . $certificateDate->format('Y');
        }
        if(!empty($data->document_date)) {
            $documentDate = date_create($data->document_date);
            $this->documentShortDateText = $documentDate->format("d/m/Y");
            $this->documentLongDateText =  "ngày " . $documentDate->format('d') . " tháng " . $documentDate->format('m') . " năm " . $documentDate->format('Y');
        }
    }
    protected function nationalName(PhpWord $phpWord, $data)
    {
        if ($this->isPrintNational) {
            $section = $phpWord->addSection($this->styleNationalSection);
            // $table1 = $section->addTable($this->tableBasicStyle);
            // $table1->addRow(1000);
            // $cell11 = $table1->addCell(Converter::cmToTwip(5), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $imgName = env('STORAGE_IMAGES','images').'/'.'company_header.png';
            $section->addImage(storage_path('app/public/'.$imgName), $this->styleImageHeader);
            // $cell12 = $table1->addCell(Converter::inchToTwip(3), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            // $cell12->addText(CommonService::downLineCompanyName($this->companyName, $this->companyDownLine), ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            // // $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            // $cell13 = $table1->addCell(Converter::inchToTwip(4), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            // $cell13->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            // $cell13->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true], $this->styleAlignCenter);
            $indentLeft = $this->marginLeftContent - $this->marginLeftNational;
            $indentRight = $this->marginRightContent - $this->marginRightNational;
            $this->printFooter($section, $data, $indentLeft, $indentRight);
        }
    }
    protected function content1(Section $section, $certificate)
    {
        $section->addListItem("Căn cứ Hợp đồng thẩm định giá/Tờ trình thuê ngoài định giá số " . $this->contractCode . ' ' . $this->documentLongDateText . " về nội dung " . $certificate->petitioner_name . ' đề nghị ' . $this->companyName . ' thực hiện việc thẩm định giá giá trị tài sản.', 0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addListItem("Căn cứ Báo cáo kết quả thẩm định giá số " . $this->reportCode . ' ' . $this->documentLongDateText . " của " . $this->companyName . ' .', 0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addListItem($this->companyName . " cung cấp Chứng thư thẩm định giá số " . $this->certificateCode .' ' . $this->documentLongDateText . " với các nội dung sau đây:", 0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addTitle("Khách hàng thẩm định giá:", 2);
        $section->addListItem("Khách hàng yêu cầu: " . $certificate->petitioner_name,0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addListItem("Ngày sinh: " . $certificate->note,0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addListItem("Địa chỉ: " . $certificate->petitioner_address,0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addListItem("Số CCCD: " . $certificate->petitioner_identity_card,0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addTitle("Thông tin về tài sản thẩm định giá:", 2);
        $section->addListItem("Tài sản thẩm định giá: " . $this->getAssetName($certificate),0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addListItem("Địa chỉ: " . $this->getAssetAddress($certificate),0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addListItem("Đặc điểm pháp lý và kinh tế kỹ thuật của tài sản: ",0 , ['size' => '13'], 'bullets', $this->indentFistLine);
        $section->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['italic' => true, 'size' => '13']);
        $appraise_date = date_create($certificate->appraise_date);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Thời điểm thẩm định giá: ", ['bold' => true]);
        $textRun->addText('Tháng ' . date_format($appraise_date, "m/Y") . '.', ['bold' => false]);
        $appraisePurpose = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Mục đích thẩm định giá: ", ['bold' => true]);
        if ($appraisePurpose === 'Vay vốn ngân hàng')
            $appraisePurpose = 'Tư vấn giá trị tài sản để ngân hàng tham khảo và xem xét quyết định hạn mức để cấp tín dụng';
        $textRun->addText($appraisePurpose, ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Căn cứ pháp lý: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục II, Báo cáo kết quả thẩm định giá.", ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Cơ sở giá trị của tài sản thẩm định giá: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục V, Báo cáo kết quả thẩm định giá.", ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Giả thiết và giả thiết đặc biệt: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục VII, Báo cáo kết quả thẩm định giá.", ['bold' => false]);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Cách tiếp cận, phương pháp thẩm định giá: ", ['bold' => true]);
        $textRun->addText("Chi tiết xem tại Mục VIII, Báo cáo kết quả thẩm định giá.", ['bold' => false]);
        $section->addTitle("Kết quả thẩm định giá: ", 2);
        $section->addText("Với thông tin như trên, " . $this->companyName . " thông báo kết quả ước tính giá trị tài sản như sau:", [], array_merge($this->indentFistLine, $this->keepNext));
        $totalAll = CommonService::getTotalRealEstatePrice($certificate->realEstate);
        $section->addText(number_format($totalAll, 0, ',', '.') . " đồng", ['bold' => true], array_merge($this->keepNext, $this->styleAlignCenter));
        $section->addText("(Bằng chữ: " . ucfirst(CommonService::convertNumberToWords($totalAll)) . " đồng)", ['italic' => true, 'bold' => true], $this->styleAlignCenter);
        $section->addText("(Chi tiết xem tại phần IX, Báo cáo kết quả thẩm định giá kèm theo.)", ['italic' => true], $this->styleAlignCenter);
        $section->addTitle("Những điều khoản loại trừ và hạn chế của kết quả thẩm định giá:", 2);
        $section->addListItem("Nội dung chi tiết xem tại Mục X, Báo cáo kết quả thẩm định giá.",0 , [], 'bullets', $this->indentFistLine);
        $section->addTitle("Thời hạn có hiệu lực của kết quả thẩm định giá:", 2);
        $section->addListItem("Kết quả thẩm định giá có hiệu lực trong thời hạn 06 tháng kể từ ngày phát hành chứng thư (nếu thị trường không có biến động nhiều).",0 , [], 'bullets', $this->indentFistLine);
        $section->addTitle("Các tài liệu kèm theo:", 2);
        $section->addListItem("Báo cáo kết quả thẩm định giá số ". $this->reportCode . ' ngày ' . $this->certificateShortDateText ,0 , [], 'bullets', $this->indentFistLine);
        $section->addText('', [], ['borderBottomSize' => 6, 'underline' => 'dash']);
        $section->addListItem("Chứng thư phát hành có kèm theo Báo cáo kết quả TĐG và các phụ lục.",0 , ['italic' => true], 'bullets', $this->indentFistLine);
        $section->addListItem("Chứng thư thẩm định giá được phát hành 03 bản chính tiếng Việt, cấp cho khách hàng 02 bản, lưu tại " . $this->companyName . " 01 bản và có giá trị pháp lý như nhau.",0 , ['italic' => true], 'bullets', $this->indentFistLine);
        $section->addListItem("Mọi hình thức sao chép chứng thư thẩm định giá không có sự đồng ý bằng văn bản của " . $this->companyName . " đều là hành vi vi phạm pháp luật.",0 , ['italic' => true], 'bullets', array_merge($this->indentFistLine, $this->keepNext));

        $section->addTextBreak(null, null, $this->keepNext);
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
}
