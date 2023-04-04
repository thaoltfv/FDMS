<?php

namespace App\Services\Document;

use App\Enum\EstimateAssetDefault;
use App\Http\ResponseTrait;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use \PhpOffice\PhpWord\Shared\Converter;
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class ChungThu
{
    use ResponseTrait;
    public function setFormat(&$phpWord)
    {
        $phpWord->addNumberingStyle(
            'headingNumbering',
            array(
                'type' => 'multilevel',
                'levels' => array(
                    array('pStyle' => 'Heading1', 'format' => 'upperRoman', 'text' => '%1.', 'left' => 360, 'hanging' => 360, 'tabPos' => 360),
                    array('pStyle' => 'Heading2', 'format' => 'decimal', 'text' => '%2.', 'left' => 360, 'hanging' => 360, 'tabPos' => 360),
                    array('pStyle' => 'Heading3', 'format' => 'decimal', 'text' => '%2.%3.', 'left' => 600, 'hanging' => 360, 'tabPos' => 600),
                )
            )
        );
        $phpWord->addNumberingStyle(
            'bullets',
            array(
                'type' => 'multilevel',
                'levels' => array(
                    array('format' => 'upperLetter', 'text' => '-', 'left' => 360, 'hanging' => 0, 'suffix' => 'space'),
                )
            )
        );

        $phpWord->addTitleStyle(1,
            array('size' => '13','bold' => true, 'allCaps' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 0)
        );
        $phpWord->addTitleStyle(2,
            array('size' => '13','bold' => true),
            array('numStyle' => 'headingNumbering', 'numLevel' => 1)
        );
        $phpWord->addTitleStyle(3,
            array('size' => '13','bold' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 2)
        );

        $phpWord->addParagraphStyle(
            'leftTab',
            array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('left', 5000)))
        );
    }
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function generateDocx($certificate, $company, $assets, $format): array
    {
        $phpWord = new PhpWord();
        $this->setFormat($phpWord);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);

        $indentFistLine = ['indentation' => ['firstLine' => 360]];
        $keepNext = ['keepNext' => true];

        $phpWord->setDefaultParagraphStyle([
			'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(6),
			'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(6),
			'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5), 'right' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5)),
            'space' => [
				'line' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(16), 'rule' => 'exact'
			],
            'align' => 'both'
		]);
        $red = \PhpOffice\PhpWord\Style\Font::FGCOLOR_BLACK;
        $styleSection = [
			'footerHeight' => 300,
			'marginTop' => Converter::inchToTwip(.8),
            'marginBottom' => Converter::inchToTwip(.8),
            'marginRight' => Converter::inchToTwip(.8),
            'marginLeft' => Converter::inchToTwip(1.2)
		];
        $section = $phpWord->addSection($styleSection);

        $tableBasicStyle = array(
            'borderSize' => 'none',
            'cellMargin'  => Converter::inchToTwip(0),
        );

        $cantSplit = ['cantSplit' => true];

        $table1 = $section->addTable($tableBasicStyle);
        $table1->addRow(Converter::inchToTwip(.1));
        $cell11 = $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'center']);
        // if (($data = @file_get_contents($company->link)) !== false) {
            $imgName = env('STORAGE_IMAGES','images').'/'.'company_logo.png';
            $cell11->addImage(
                storage_path('app/public/'.$imgName),
                    array(
                    'width'         => 54,
                    'height'        => 33,
                    'wrappingStyle' => 'behind',
                    'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                    'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
                    'posVertical'    => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                    'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                    'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                )
            );
        // }

         # watermark
         $header = $section->addHeader();
         $header->addWatermark(storage_path('app/public/'.$imgName),
                                 array(
                                     'width' => 200,
                                     'marginTop' => 200,
                                     'marginLeft' => 120,
                                     'posHorizontal' => 'absolute',
                                     'posVertical' => 'absolute',
                                 )
                             );

        $cell12 = $table1->addCell(Converter::inchToTwip(2.6), ['valign' => 'center']);
        $cell12->addText(CommonService::formatCompanyName($company), ['bold' => true, 'size' => '12'], ['align' => 'center']);
        // $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'center']);
        $cell13 = $table1->addCell(Converter::inchToTwip(3.8), ['valign' => 'center']);
        $cell13->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '12'], ['align' => 'center']);
        $cell13->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true], ['align' => 'center']);

        $section->addText('', [], ['borderBottomSize' => 20, 'underline' => 'dash']);

        $table2 = $section->addTable($tableBasicStyle);
        $table2->addRow(Converter::inchToTwip(.1));
        $cell21 = $table2->addCell(Converter::inchToTwip(2));
        if(isset($certificate->certificate_num)&&!empty(trim($certificate->certificate_num))) {
            $cell21->addText("Số:    " . $certificate->certificate_num . "    /CT-ĐNI", null, ['align' => 'center']);
        } else {
            $cell21->addText("Số:      /CT-ĐNI", null, ['align' => 'center']);
        }
        $cell22 = $table2->addCell(Converter::inchToTwip(4));

        if(isset($certificate->certificate_date)&&!empty(trim($certificate->certificate_date))) {
            $certificateDate = date_create($certificate->certificate_date);
            $cell22->addText("Ngày " . $certificateDate->format('d') . " tháng " . $certificateDate->format('m') . " năm " . $certificateDate->format('Y'), ['bold' => false, 'size' => '13'], ['align' => 'right']);
        } else {
            $certificateDate = null;
            $cell22->addText("Ngày      tháng      năm      ", ['bold' => false, 'size' => '13'], ['align' => 'right']);
        }

        $section->addText("CHỨNG THƯ THẨM ĐỊNH GIÁ", ['bold' => true, 'size' => '18'], ['align' => 'center']);

        $section->addText("Kính gửi: " . $certificate->petitioner_name, ['bold' => true, 'size' => '14'], ['align' => 'center']);

        if(isset($certificate->document_date)&&!empty(trim($certificate->document_date))) {
            $document_date = date_create($certificate->document_date);
            $document_date_string = ' ngày ' . date_format($document_date, "d") . ' tháng ' . date_format($document_date, "m") . ' năm ' . date_format($document_date, "Y");
            $document_party = " giữa " . $company->name . !empty($company->acronym) ? ' (' . mb_strtoupper($company->acronym) . ')' : '' . " và " . $certificate->petitioner_name . '.';
            $section->addListItem("Căn cứ Hợp đồng thẩm định giá số    " . $certificate->document_num . '    /' . date_format($document_date, "Y") . '/HĐTV-TĐG' . $document_date_string . $document_party,0 , [], 'bullets', $indentFistLine);
        } else {
            $document_date_string = ' ngày        tháng        năm        ';
            $document_party = " giữa " . $company->name . " và " . $certificate->petitioner_name . '.';
            $section->addListItem("Căn cứ Hợp đồng thẩm định giá số    " . $certificate->document_num . '    /        /HĐTV-TĐG' . $document_date_string . $document_party,0 , [], 'bullets', $indentFistLine);
        }


        $section->addListItem("Căn cứ Báo cáo kết quả thẩm định giá, " . $company->name . " cung cấp Chứng thư thẩm định giá với các nội dung sau đây:",0 , [], 'bullets', $indentFistLine);

        $section->addTitle("Khách hàng thẩm định giá:", 2);

        $section->addListItem("Khách hàng: " . $certificate->petitioner_name,0 , [], 'bullets', $indentFistLine);
        $section->addListItem("Địa chỉ: " . $certificate->petitioner_address,0 , [], 'bullets', $indentFistLine);

        $section->addTitle("Thông tin về tài sản thẩm định giá:", 2);
        $name_assets = "";
        foreach ($assets as $index => $item) {
            $name_assets .= ($index) ? " và " : "";
            $name_assets .= $item->appraise_asset;
        }
        $section->addListItem("Tên tài sản: " . $name_assets,0 , [], 'bullets', $indentFistLine);
        $section->addListItem("Nội dung chi tiết xem tại Mục IV, Báo cáo kết quả thẩm định giá.",0 , [], 'bullets', $indentFistLine);


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

        $section->addText("Trên cơ sở các tài liệu do khách hàng cung cấp, dựa trên cách tiếp cận và phương pháp thẩm định giá được áp dụng trong tính toán, " . $company->name . " ước tính giá trị tài sản như sau:", [], array_merge($indentFistLine, $keepNext));

        $totalAll = CommonService::getCertificatePrice($certificate, 'total_asset_price');
        $totalAll = CommonService::roundCertificatePrice($certificate, $totalAll);

        $section->addText(number_format($totalAll, 0, ',', '.') . " đồng", ['bold' => true], array_merge($keepNext, ['align' => 'center']));

        $section->addText("(Bằng chữ: " . ucfirst(CommonService::convertNumberToWords($totalAll)) . " đồng)", ['italic' => true, 'bold' => true], ['align' => 'center']);

        $section->addText($company->name . " thông báo kết quả thẩm định giá đến " . $certificate->petitioner_name . " để thực hiện theo mục đích thẩm định giá và thời điểm thẩm định giá.", [], $indentFistLine);

        $section->addTitle("Những điều khoản loại trừ và hạn chế của kết quả thẩm định giá:", 2);

        $section->addListItem("Nội dung chi tiết xem tại Mục X, Báo cáo kết quả thẩm định giá.",0 , [], 'bullets', $indentFistLine);

        $section->addTitle("Thời hạn có hiệu lực của kết quả thẩm định giá:", 2);

        $section->addListItem("Kết quả thẩm định giá có hiệu lực trong thời hạn 06 tháng kể từ ngày phát hành chứng thư (nếu thị trường không có biến động nhiều).",0 , [], 'bullets', $indentFistLine);

        $section->addText('', [], ['borderBottomSize' => 6, 'underline' => 'dash']);


        $section->addListItem("Chứng thư phát hành có kèm theo Báo cáo kết quả TĐG và các phụ lục.",0 , ['italic' => true], 'bullets', $indentFistLine);
        $section->addListItem("Chứng thư thẩm định giá được phát hành 03 bản chính tiếng Việt, cấp cho khách hàng 02 bản, lưu tại " . $company->name . " 01 bản.",0 , ['italic' => true], 'bullets', $indentFistLine);
        $section->addListItem("Mọi hình thức sao chép chứng thư thẩm định giá không có sự đồng ý bằng văn bản của " . $company->name . " đều là hành vi vi phạm pháp luật.",0 , ['italic' => true], 'bullets', array_merge($indentFistLine, $keepNext));

        $section->addTextBreak(null, null, $keepNext);

        $table3 = $section->addTable($tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("THẨM ĐỊNH VIÊN VỀ GIÁ", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        if(isset($certificate->appraiserConfirm->name)) {
            $cell32->addText("KT. TỔNG GIÁM ĐỐC", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $cell32->addText( $certificate->appraiserConfirm->appraisePosition->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        } else {
            $cell32->addText("TỔNG GIÁM ĐỐC", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        }
        $table3->addRow(Converter::inchToTwip(1.5), $cantSplit);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $keepNext);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $keepNext);;
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


        //Footer
        $comName =  !empty($company->acronym) ? mb_strtoupper($company->acronym) : mb_strtoupper($company->name);
        $createdName =  isset($certificate->createdBy) ? CommonService::withoutAccents($certificate->createdBy->name) : '';
        if(isset($certificate->document_date)&&!empty(trim($certificate->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $certificate->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        $reportID = 'HSTD_'. $certificate->id;
        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $cell = $table->addCell(4500);
        $textrun = $cell->addTextRun();
        $textrun->addText($comName  .'/'. $createdName.'/'.$yearCVD.'/'.$reportID, array('size' => 8), array('align'=>'left'));
        $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align'=>'right'));

        $reportUserName = CommonService::getUserReport();
        $reportName = '1_CT' . '_' . $reportUserName . '_' . $reportID . '_' . $comName;
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $fileName = $reportName . '_' . $downloadTime . '_' . $downloadDate;

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'comparison_brief/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if(!File::exists(storage_path('app/public/'. $path))){
            File::makeDirectory(storage_path('app/public/'. $path), 0755, true);
        }
        try {
            $objWriter->save(storage_path('app/public/'. $path. $fileName .'.docx'));
        } catch (\Exception $e) {
            throw $e;
        }
        $data = [];
        $data['url'] = Storage::disk('public')->url($path .  $fileName .'.docx');
        $data['file_name'] = $fileName;
        return $data;
    }
}
