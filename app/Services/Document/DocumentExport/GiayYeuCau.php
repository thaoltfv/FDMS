<?php

namespace App\Services\Document\DocumentExport;

use App\Enum\EstimateAssetDefault;
use App\Http\ResponseTrait;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\ListItem;
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class GiayYeuCau
{
    use ResponseTrait;
    public function setFormat(&$phpWord)
    {
        $phpWord->addNumberingStyle(
            'headingNumbering',
            array(
                'type' => 'multilevel',
                'levels' => array(
                    array('pStyle' => 'Heading1', 'format' => 'upperRoman', 'text' => '%1.', 'left' => 300, 'hanging' => 360, 'tabPos' => 360),
                    array('pStyle' => 'Heading2', 'format' => 'decimal', 'text' => '%2.', 'left' => 300, 'hanging' => 360, 'tabPos' => 360),
                    array('pStyle' => 'Heading3', 'format' => 'decimal', 'text' => '%2.%3.', 'left' => 600, 'hanging' => 360, 'tabPos' => 600),
                )
            )
        );
        $phpWord->addNumberingStyle(
            'bullets',
            array(
                'type' => 'multilevel',
                'levels' => array(
                    array('format' => 'upperLetter', 'text' => '-', 'left' => 360, 'hanging' => 360, 'suffix' => 'space', 'tabPos' => 900, 'lineHeight' => 1.5),
                )
            )
        );

        $phpWord->addTitleStyle(
            1,
            array('size' => '13', 'bold' => false, 'allCaps' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 0)
        );
        $phpWord->addTitleStyle(
            2,
            array('size' => '13', 'bold' => false),
            array('numStyle' => 'headingNumbering', 'numLevel' => 1)
        );
        $phpWord->addTitleStyle(
            3,
            array('size' => '13', 'bold' => false),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 2)
        );

        $phpWord->addParagraphStyle(
            'leftTab',
            array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('left', 5000)))

        );
        $phpWord->addParagraphStyle(
            'alignItemCenter',
            array('align' => 'center')
        );
    }
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function generateDocx($company, $certificate, $format, $documentConfig): array
    {
        $phpWord = new PhpWord();
        $this->setFormat($phpWord);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);
        // $phpWord->setDefaultParagraphStyle(
        //     array(
        //         'spacing' => 120,
        //         'lineHeight' => 1.0,
        //     )
        // );
        $styleTable = [
            'borderSize' => 1,
            'align' => JcTable::START,

        ];

        $styleTableHide = [
            'align' => JcTable::START
        ];

        $styleTableImage = [
            'align' => JcTable::CENTER
        ];

        $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';

        // [] = ['indentation' => ['firstLine' => 360]];
        $keepNext = ['keepNext' => true];
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellHCentered = array('align' => 'center');
        $cellVCentered = array('valign' => 'center');
        $cellHJustify = array('align' => 'both');
        $cellVJustify = array('valign' => 'both');
        [] = ['indentation' => ['firstLine' => 360]];
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
        $styleSection = [
            'footerHeight' => 300,
            'marginTop' => Converter::inchToTwip(.8),
            'marginBottom' => Converter::inchToTwip(.8),
            'marginRight' => Converter::inchToTwip(.8),
            'marginLeft' => Converter::inchToTwip(1.2)
        ];
        $section = $phpWord->addSection($styleSection);

        $section->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '13'], ['align' => 'center']);

        $section->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true], ['align' => 'center']);
        $section->addText(" ", ['size' => '10'], ['align' => 'center']);
        $section->addText("GIẤY YÊU CẦU THẨM ĐỊNH GIÁ", ['bold' => true, 'size' => '15'], ['align' => 'center']);

        $textRun = $section->addTextRun("alignItemCenter");
        $textRun->addText(' ', array('spaceAfter' => 240));
        $textRun->addText("Kính gửi", ['underline' => 'single', 'size' => '13'], ['align' => 'center']);
        $textRun->addText(": ", ['size' => '13'], ['align' => 'center']);
        $textRun->addText("CÔNG TY TNHH THẨM ĐỊNH GIÁ NOVA", ['bold' => true, 'size' => '13'], ['align' => 'center']);
        $textRun->addText(' ', array('spaceAfter' => 240));
        $section->addText("Địa chỉ: Số 728 – 730 Võ Văn Kiệt, Phường 1, Quận 5, TP. HCM", ['bold' => false, 'size' => '13'], ['align' => 'center']);
        $section->addText("Điện thoại: (028) 3920 6779	        Email: thamdinhnova@gmail.com", ['bold' => false, 'size' => '13'], ['align' => 'center']);
        $section->addText(" ", ['size' => '10'], ['align' => 'center']);

        // 1
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Thông tin cá nhân yêu cầu thẩm định: ", ['bold' => false]);
        $section->addListItem("Họ và tên: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets', []);
        $section->addListItem("Số thẻ CCCD: " . $certificate->petitioner_identity_card, 0, [], 'bullets', []);
        $section->addListItem("Ngày cấp:                   ; Nơi cấp:  ", 0, [], 'bullets', []);
        $section->addListItem("Địa chỉ: " . $certificate->petitioner_address, 0, [], 'bullets', []);
        $section->addListItem("Số điện thoại: " . $certificate->petitioner_phone, 0, [], 'bullets', []);

        //2
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Mục đích: ", ['bold' => false]);
        $textRun->addText('Cung cấp các hồ sơ, dữ liệu cá nhân cho Công ty TNHH Thẩm định giá NOVA để lập Hồ sơ Thẩm định giá tài sản.', ['bold' => false]);

        //3
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Phương thức nhận văn bản, hồ sơ, tài liệu: ", ['bold' => false]);
        $textRun->addText('Nhận qua mạng điện tử.', ['bold' => false]);


        //4
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Nội dung: ", ['bold' => false]);
        $textRun->addText('Đề nghị Công ty TNHH Thẩm định giá Nova thẩm định giá tài sản như sau: ', ['bold' => false]);

        $name_assets = "Quyền sử hữu căn hộ";
        $number_assets = "01";
        $count = 0;
        // foreach ($assets as $index => $item) {
        //     $name_assets .= ($index) ? " và " : "";
        //     $name_assets .= $item->appraise_asset;
        //     $count += 1;
        // }
        // if ($count < 10) {
        //     $number_assets = '0' . strval($count);
        // } else {
        //     $number_assets = strval($count);
        // }
        $section->addListItem("Tên tài sản: " . htmlspecialchars($name_assets), 0, [], 'bullets', []);
        $section->addListItem("Số lượng, khối lượng tài sản: " . $number_assets, 0, [], 'bullets', []);

        $tableBasicStyle = array(
            'borderSize' => 'none',
            'cellMargin'  => Converter::inchToTwip(0),
        );
        // $onlyOneAsset = (count($assets) > 1) ? false : true;
        $rowHeader = [
            'tblHeader' => true,
            'cantSplit' => true
        ];
        $cantSplit = ['cantSplit' => true];
        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable($styleTable);

        $table->addRow(400, $rowHeader);
        $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(5000, $cellVCentered)->addText('Hạng mục', ['bold' => true], $cellHCentered);
        $table->addCell(1000, $cellVCentered)->addText('Diện tích', ['bold' => true], $cellHCentered);
        $table->addCell(1000, $cellVCentered)->addText('Đơn vị tính', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('Thông tin tài sản kèm theo', ['bold' => true], $cellHCentered);
        // foreach ($assets as $stt => $asset) {
        // Thông tin tài sản
        $table->addRow(400, $cantSplit);
        $table->addCell(600, $cellVCentered)->addText('1', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVJustify)->addText('Căn hộ số 10.32, Chung cư Flora Anh Đào (Ehome 6), 619 Đỗ Xuân Hợp, phường Phước Long B, Quận 9, TP.HCM', ['bold' => true], $cellHJustify);
        $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
        $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);

        $table->addRow(400, $cantSplit);
        $table->addCell(600, $cellVCentered)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVJustify)->addText('Quyền sử hữu căn hộ', ['bold' => false], $cellHJustify);
        $table->addCell(1000, $cellVCentered)->addText('50', ['bold' => false], $cellHCentered);
        $table->addCell(1000, $cellVCentered)->addText($m2, ['bold' => false], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText('', ['bold' => false], $cellHCentered);
        // }

        $appraise_date = date_create($certificate->appraise_date);
        $bien101 = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';






        $section->addListItem("Mục đích yêu cầu thẩm định giá: " . $bien101 . ".", 0, [], 'bullets', []);
        $section->addListItem("Thời điểm thẩm định giá: Tháng "  . date_format($appraise_date, "m/Y") . '.', 0, [], 'bullets', []);
        $section->addListItem("Bên sử dụng kết quả thẩm định giá: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets', []);
        $section->addListItem("Nguồn gốc tài sản (Nhà nước/ không phải thuộc Nhà nước): Không phải thuộc Nhà nước", 0, [], 'bullets', []);
        $section->addListItem("Ngày giờ, địa điểm khảo sát: 04/03/2024 tại Căn hộ số 10.32, Chung cư Flora Anh Đào (Ehome 6), 619 Đỗ Xuân Hợp, phường Phước Long B, Quận 9, TP.HCM.", 0, [], 'bullets', []);
        $section->addListItem("Tên, điện thoại người liên hệ:                           ; Điện thoại:                   ", 0, [], 'bullets', []);
        $listItemRun  = $section->addListItemRun(0, 'bullets', []);
        $listItemRun->addText("Các hồ sơ, dữ liệu cá nhân, cung cấp gồm: ");
        $listItemRun->addText("01 Bản Giấy chứng nhận quyền sử dụng đất quyền sở hữu nhà ở và tài sản khác gắn liền với đất số CK 096662 số vào sổ cấp GCN:CS23305/DA ngày 30/05/2018 do Sở Tài Nguyên và Môi Trường thành phố Hồ Chí Minh cấp.", ['italic' => true]);

        $section->addListItem("Số bản chứng thư yêu cầu cấp: 02 bản chính bằng tiếng Việt.", 0, [], 'bullets', []);
        $section->addText("   Tôi đồng ý cung cấp các Hồ sơ, Dữ liệu cá nhân như trên cho Công ty TNHH Thẩm định giá Nova, Công ty Nova được phép xử lý các dữ liệu được cung cấp để công ty tiến hành thu thập thông tin, lập hồ sơ Thẩm định giá tài sản phù hợp với mục đích được yêu cầu tại văn bản này.", []);
        $section->addText("   Tôi cam kết thanh toán đủ phí dịch vụ theo quy định Công ty TNHH thẩm định giá Nova.", []);



        $section->addTextBreak(null, null, $keepNext);

        $table3 = $section->addTable($tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
        $table3->addCell(Converter::inchToTwip(4))->addText("", null,  $keepNext);;
        $table3->addCell(Converter::inchToTwip(4))->addText("TP.HCM, ngày        tháng        năm   ", null,  $keepNext);

        $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("ĐƠN VỊ NHẬN YÊU CẦU", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        $cell32->addText("NGƯỜI YÊU CẦU", ['bold' => true], ['align' => 'center', 'keepNext' => true]);

        //Footer
        // $comName =  !empty($company->acronym) ? mb_strtoupper($company->acronym) : mb_strtoupper($company->name);
        // $createdName =  isset($certificate->createdBy) ? CommonService::withoutAccents($certificate->createdBy->name) : '';
        // if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
        //     $yearCVD = Carbon::createFromFormat('Y-m-d',  $certificate->document_date)->format('Y');
        // } else {
        //     $yearCVD = "        ";
        // }
        // $reportID = 'HSTD_' . $certificate->id;
        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $cell = $table->addCell(4500);
        $textrun = $cell->addTextRun();
        // $textrun->addText($comName  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID, array('size' => 8), array('align' => 'left'));
        $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right'));

        $reportUserName = CommonService::getUserReport();
        $reportName = 'GYC' . '_' . htmlspecialchars($certificate->petitioner_name);
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $fileName = $reportName . '_' . $downloadTime . '_' . $downloadDate;

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/' . 'comparison_brief/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if (!File::exists(storage_path('app/public/' . $path))) {
            File::makeDirectory(storage_path('app/public/' . $path), 0755, true);
        }
        try {
            $objWriter->save(storage_path('app/public/' . $path . $fileName . '.docx'));
        } catch (\Exception $e) {
            throw $e;
        }
        $data = [];
        $data['url'] = Storage::disk('public')->url($path .  $fileName . '.docx');
        $data['file_name'] = $fileName;
        $data['certificate'] = $certificate;
        return $data;
    }
}
