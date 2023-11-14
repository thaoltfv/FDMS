<?php
namespace App\Services\Document\Certificate;

use App\Services\CommonService;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\PhpWord;

class ReportCertificateVavc extends ReportCertificate
{
    public function printFooter(Section $section, $data, $indentLeft = 0, $indentRight = 0)
    {
        $footer = $section->addFooter();
        // $strFooter = $this->getFooterString($data);
        // $table = $footer->addTable();
        // $table->addRow();
        // $table->addCell(4500)->addText($strFooter, array('size' => 8), array('align' => 'left', 'indentation' => array('left' => $indentLeft)));
        // $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right',  'indentation' => array('right' => $indentRight)));
        $footer->addPreserveText('{PAGE}',['size' => '12'], $this->styleAlignRight);
        $imgName = env('STORAGE_IMAGES','images').'/'.'company_footer.png';
        $footer->addImage(storage_path('app/public/'.$imgName), $this->styleImageFooter);
    }
    public function printTitle(Section $section, $data)
    {
        $table2 = $section->addTable($this->tableBasicStyle);
        $table2->addRow(Converter::inchToTwip(.1));
        $cell21 = $table2->addCell(Converter::inchToTwip(2));
        $cell21->addText("Số: ". $this->certificateCode, ["name" => "Cambria", 'size' => '12'], array_merge($this->styleAlignCenter, ['spaceBefore' => 0]));
        $cell22 = $table2->addCell(Converter::inchToTwip(4));
        $cell22->addText(ucfirst('Hà Nội, ' . $this->certificateLongDateText), ["name" => "Cambria", 'italic' => true, 'size' => '12'], array_merge($this->styleAlignRight, ['spaceBefore' => 0]));

        $section->addText("CHỨNG THƯ THẨM ĐỊNH GIÁ", ["name" => "Cambria", 'bold' => true, 'size' => '18', 'color' => '1f497d'], array_merge($this->styleAlignCenter, ['spaceBefore' => 320]));
        $section->addText("Kính gửi: " . $data->petitioner_name, ['bold' => true, 'size' => '12.5'], array_merge($this->styleAlignCenter, ['spaceAfter' => 300]));
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
            $section = $phpWord->addSection($this->styleSection);
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
        $section->addListItem("Căn cứ Hợp đồng thẩm định giá/Tờ trình thuê ngoài định giá số " . $this->contractCode . ' ' . $this->documentLongDateText . " về nội dung " . $certificate->petitioner_name . ' đề nghị ' . $this->companyName . ' thực hiện việc thẩm định giá giá trị tài sản.', 0 , ['size' => '12.5'], 'bullets');
        $section->addListItem("Căn cứ Báo cáo kết quả thẩm định giá số " . $this->reportCode . ' ' . $this->documentLongDateText . " của " . $this->companyName . ' .', 0 , ['size' => '12.5'], 'bullets');
        $section->addListItem($this->companyName . " cung cấp Chứng thư thẩm định giá số " . $this->certificateCode .' ' . $this->documentLongDateText . " với các nội dung sau đây:", 0 , ['size' => '12.5'], 'bullets');
        $section->addTitle("Khách hàng thẩm định giá:", 2);
        $listItemRun1 = $section->addListItemRun(0,'bullets');
        $listItemRun1->addText("Khách hàng yêu cầu: ", ['size' => '12.5']);
        $listItemRun1->addText($certificate->petitioner_name, ['size' => '12.5', 'bold' => true]);
        // $section->addListItem("Khách hàng yêu cầu: " . $certificate->petitioner_name,0 , ['size' => '12.5'], 'bullets', $this->indentFistLine);
        $certificate->petitioner_birthday = '01/01/1970';
        $section->addListItem("Ngày sinh: " . $certificate->petitioner_birthday,0 , ['size' => '12.5'], 'bullets');
        $section->addListItem("Địa chỉ: " . $certificate->petitioner_address,0 , ['size' => '12.5'], 'bullets');
        $section->addListItem("Số CCCD: " . $certificate->petitioner_identity_card,0 , ['size' => '12.5'], 'bullets');
        $section->addTitle("Thông tin về tài sản thẩm định giá:", 2);
        $section->addListItem("Tài sản thẩm định giá: " . $this->getAssetName($certificate),0 , ['size' => '12.5'], 'bullets');
        $section->addListItem("Địa chỉ: " . $this->getAssetAddress($certificate),0 , ['size' => '12.5'], 'bullets');
        $listItemRun2 = $section->addListItemRun(0,'bullets',['spaceAfter' => 0]);
        $listItemRun2->addText("Đặc điểm pháp lý và kinh tế kỹ thuật của tài sản: ", ['size' => '12.5']);
        $listItemRun2->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['size' => '12.5', 'italic' => true]);
        // $section->addListItem("Đặc điểm pháp lý và kinh tế kỹ thuật của tài sản: ",0 , ['size' => '12.5'], 'bullets', $this->indentFistLine);
        // $list1->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['italic' => true, 'size' => '12.5']);
        // $section->addTextBreak(2);
        $appraise_date = date_create($certificate->appraise_date);
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Thời điểm thẩm định giá: ", ['size' => '12.5','bold' => true]);
        $textRun->addText('Tháng ' . date_format($appraise_date, "m/Y") . '.', ['size' => '12.5','bold' => false]);
        $appraisePurpose = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        $textRun1 = $section->addTextRun('Heading2');
        $textRun1->addText("Mục đích thẩm định giá: ", ['size' => '12.5','bold' => true]);
        if ($appraisePurpose === 'Vay vốn ngân hàng')
            $appraisePurpose = 'Tư vấn giá trị tài sản để ngân hàng tham khảo và xem xét quyết định hạn mức để cấp tín dụng';
        $textRun1->addText($appraisePurpose, ['size' => '12.5','bold' => false]);
        $textRun2 = $section->addTextRun('Heading2');
        $textRun2->addText("Căn cứ pháp lý: ", ['size' => '12.5','bold' => true]);
        $textRun2->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['size' => '12.5','bold' => false, 'italic' => true]);
        $textRun3 = $section->addTextRun('Heading2');
        $textRun3->addText("Cơ sở giá trị của tài sản thẩm định giá: ", ['size' => '12.5','bold' => true]);
        $textRun3->addText("Cơ sở giá trị thị trường và phi thị trường.", ['size' => '12.5','bold' => false]);
        $textRun4 = $section->addTextRun('Heading2');
        $textRun4->addText("Giả thiết và giả thiết đặc biệt: ", ['size' => '12.5','bold' => true]);
        $textRun4->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['size' => '12.5','bold' => false, 'italic' => true]);
        $textRun5 = $section->addTextRun('Heading2');
        $textRun5->addText("Cách tiếp cận, phương pháp thẩm định giá: ", ['size' => '12.5','bold' => true]);
        $textRun5->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['size' => '12.5','bold' => false, 'italic' => true]);
        $section->addTitle("Kết quả thẩm định giá: ", 2);
        $section->addListItem("Trên cơ sở kết hợp các thông tin hồ sơ của tài sản do khách hàng cung cấp, khảo sát, xem xét tình hình giao dịch trên thị trường mua bán các tài sản tương tự để ứng dụng các phương pháp trong tính toán, " . $this->companyName . " thông báo kết quả thẩm định giá tài sản tại thời điểm thẩm định giá như sau:",0 , [], 'bullets', $this->indentFistLine);
        $totalAll = CommonService::getTotalRealEstatePrice($certificate->realEstate);
        $section->addText(number_format($totalAll, 0, ',', '.') . " đồng", ['size' => '12.5','bold' => true], array_merge($this->keepNext, $this->styleAlignCenter));
        $section->addText("(Bằng chữ: " . ucfirst(CommonService::convertNumberToWords($totalAll)) . " đồng)", ['size' => '12.5','italic' => true, 'bold' => true], $this->styleAlignCenter);
        $section->addText("(Chi tiết xem tại Báo cáo kết quả thẩm định giá kèm theo)", ['size' => '12.5','italic' => true], $this->styleAlignCenter);
        $textRun6 = $section->addTextRun('Heading2');
        $textRun6->addText("Những điều khoản loại trừ và hạn chế kèm theo kết quả thẩm định giá: ", ['size' => '12.5','bold' => true]);
        $textRun6->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['size' => '12.5','bold' => false, 'italic' => true]);
        $textRun7 = $section->addTextRun('Heading2');
        $textRun7->addText("Thời hạn có hiệu lực của kết quả thẩm định giá: ", ['size' => '12.5','bold' => true]);
        $textRun7->addText("Chi tiết tại Báo cáo kết quả thẩm định giá kèm theo.", ['size' => '12.5','bold' => false, 'italic' => true]);
        $section->addTitle("Các tài liệu kèm theo:", 2);
        $section->addListItem("Báo cáo kết quả thẩm định giá.",0 , ['size' => '12.5',], 'bullets', $this->indentFistLine);
        $section->addListItem("Các phụ lục chi tiết kèm theo.",0 , ['size' => '12.5',], 'bullets', $this->indentFistLine);
        $section->addListItem("Hồ sơ pháp lý của tài sản thẩm định giá.",0 , ['size' => '12.5',], 'bullets', $this->indentFistLine);
        $section->addTitle("Những lưu ý về Chứng thư thẩm định giá:", 2);
        $section->addListItem($certificate->note,0 , ['size' => '12.5',], 'bullets', $this->indentFistLine);
        $section->addListItem("Khách hàng có trách nhiệm sử dụng Chứng thư thẩm định giá đúng quy định của Pháp luật.",0 , ['size' => '12.5',], 'bullets', $this->indentFistLine);
        $section->addListItem("Chứng thư thẩm định giá được phát hành 03 (ba) bản chính bằng tiếng Việt, " . $this->companyName . " giữ 01 (một) bản, Khách hàng thẩm định giá giữ 02 (hai) bản, có giá trị như nhau",0 , ['size' => '12.5',], 'bullets', $this->indentFistLine);
        $section->addListItem("Chứng thư thẩm định giá thuộc quyền sở hữu trí tuệ của " . $this->companyName . " và không được sao chép, bán, xuất bản hoặc phân phát dưới bất kỳ hình thức nào khi không có sự đồng ý trước bằng văn bản của " . $this->acronym . ". " . $this->acronym . " chỉ chịu trách nhiệm về số lượng văn bản (bản chính và bản sao) do Công ty phát hành. Mọi hình thức sao chép Chứng thư thẩm định giá không có sự đồng ý bằng văn bản của " . $this->companyName . " đều là hành vi vi phạm pháp luật và không có giá trị.",0 , ['size' => '12.5',], 'bullets', array_merge($this->indentFistLine, $this->keepNext));

        $section->addTextBreak(null, null, $this->keepNext);
    }
    protected function signature(Section $section, $certificate)
    {
        $section->addTextBreak(null, null, $this->keepNext);
        $section->addText(mb_strtoupper($this->companyName), ["name" => "Cambria", 'size' => '12.5', 'bold' => true ], $this->styleAlignCenter);
        $table3 = $section->addTable($this->tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), $this->cantSplit);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("THẨM ĐỊNH VIÊN VỀ GIÁ", ["name" => "Cambria", 'size' => '12.5','bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        $cell32->addText("ĐẠI DIỆN PHÁP LUẬT", ["name" => "Cambria", 'size' => '12.5','bold' => true], ['align' => 'center', 'keepNext' => true]);
        if(isset($certificate->appraiserConfirm->name)) {
            $cell32->addText("KT. TỔNG GIÁM ĐỐC", ["name" => "Cambria", 'size' => '12.5','bold' => true], ['align' => 'center', 'keepNext' => true]);
        }
        $table3->addRow(Converter::inchToTwip(1.5), $this->cantSplit);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);;
        $table3->addRow(Converter::inchToTwip(.1));
        $cell33 = $table3->addCell(Converter::inchToTwip(4));
        $bien171 = (isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : '';
        $cell33->addText(mb_strtoupper($bien171), ["name" => "Cambria", 'size' => '12.5','bold' => true], ['align' => 'center', 'keepNext' => true]);
        $appraiserNumber =   isset($certificate->appraiser) ? $certificate->appraiser->appraiser_number : '';
        $cell33->addText("Số thẻ TĐV về giá: " . $appraiserNumber, ["name" => "Cambria", 'size' => '12.5','bold' => false], ['align' => 'center']);
        $cell34 = $table3->addCell(Converter::inchToTwip(4));
        $appraiserManager = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->name : $certificate->appraiserManager->name;
        $appraiserManagerNumber = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->appraiser_number : $certificate->appraiserManager->appraiser_number;
        $bien172 = $appraiserManager;
        $cell34->addText(mb_strtoupper($bien172), ["name" => "Cambria", 'size' => '12.5','bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell34->addText("Số thẻ TĐV về giá: " . $appraiserManagerNumber, ["name" => "Cambria", 'size' => '12.5','bold' => false], ['align' => 'center']);
        $cell34->addText( $certificate->appraiserConfirm->appraisePosition->description, ["name" => "Cambria", 'size' => '12.5','bold' => true], ['align' => 'center', 'keepNext' => true]);
    }
}
