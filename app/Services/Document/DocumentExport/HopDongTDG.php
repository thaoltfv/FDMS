<?php

namespace App\Services\Document\DocumentExport;

use Illuminate\Support\Facades\Log;
use App\Enum\EstimateAssetDefault;
use App\Http\ResponseTrait;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\ListItem;
use PhpOffice\PhpWord\ComplexType\TblWidth;
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class HopDongTDG
{
    use ResponseTrait;
    function formatNumberFunction($number, $count = 0, $tenp = ',', $temp2 = '.')
    {
        if (!empty($number)) {
            $number = (float)$number;
            if (floor($number) != $number) {
                $number = number_format($number, $count, $tenp, $temp2);
            } else {
                $number = number_format($number, 0, $tenp, $temp2);
            }
        }
        return $number;
    }
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
                    array('format' => 'upperLetter', 'text' => '-', 'left' => 360, 'hanging' => 0, 'suffix' => 'space', 'line-height' => 1.5)
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
    // public function generateDocx($company, $certificate, $format, $appraises, $priceEstimatePrint): array
    // {

    //     $phpWord = new PhpWord();
    //     $this->setFormat($phpWord);
    //     $phpWord->setDefaultFontName('Times New Roman');
    //     $phpWord->setDefaultFontSize(13);
    //     // $phpWord->setDefaultParagraphStyle(
    //     //     array(
    //     //         'spacing' => 120,
    //     //         'lineHeight' => 1.0,
    //     //     )
    //     // );
    //     $styleTable = [
    //         'borderSize' => 1,
    //         'align' => JcTable::CENTER,
    //         // 'spaceBefore'        =>  240
    //         // 'cellMarginLeft'  => Converter::inchToTwip(1.0),

    //     ];

    //     $styleTableHide = [
    //         'align' => JcTable::START
    //     ];

    //     $styleTableImage = [
    //         'align' => JcTable::CENTER
    //     ];

    //     $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';

    //     // [] = ['indentation' => ['firstLine' => 360]];
    //     $keepNext = ['keepNext' => true];
    //     $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
    //     $cellRowContinue = array('vMerge' => 'continue');
    //     $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
    //     $cellHCentered = array('align' => 'center');
    //     $cellVCentered = array('valign' => 'center');
    //     $marginLeft =  array('marginLeft' => 400);
    //     $cellHJustify = array('align' => 'both');
    //     $cellVJustify = array('valign' => 'both');
    //     // [] = ['indentation' => ['firstLine' => 360]];
    //     $keepNext = ['keepNext' => true];

    //     $indentleftword =
    //         ['align' => 'both'];
    //     $indentleftNumber =
    //         ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.05)]];
    //     $indentleftSymbol =
    //         ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.15)]];

    //     $phpWord->setDefaultParagraphStyle([
    //         'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
    //         'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
    //         // 'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.13)),

    //         'align' => 'both'
    //     ]);
    //     $styleSection = [
    //         'footerHeight' => 300,
    //         'marginTop' => Converter::inchToTwip(0.7),
    //         'marginBottom' => Converter::inchToTwip(0.28),
    //         'marginRight' => Converter::inchToTwip(0.3),
    //         'marginLeft' => Converter::inchToTwip(0.6)
    //     ];

    //     $check = "";

    //     $section = $phpWord->addSection($styleSection);
    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row1 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row1->addCell(3500, $cellVCentered)->addText('CÔNG TY TNHH', ['bold' => true,], $cellHCentered);
    //     $row1->addCell(1000, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);
    //     $row1->addCell(5700, $cellVCentered)->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', ['bold' => true], $cellHCentered);
    //     $row2 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
    //     $row2->addCell(3500, $cellVCentered)->addText('THẨM ĐỊNH GIÁ NOVA', ['bold' => true,  'underline' => 'single'], $cellHCentered);
    //     $row2->addCell(1000, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);
    //     $row2->addCell(5700, $cellVCentered)->addText('Độc lập – Tự do - Hạnh phúc', ['bold' => true,   'underline' => 'single'], $cellHCentered);
    //     $row3 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
    //     $row3->addCell(3500, $cellVCentered)->addText('Số: ' . (isset($certificate->document_num) ? $certificate->document_num . '' : ''), null, $cellHCentered);
    //     $row3->addCell(1000, $cellVCentered)->addText(
    //         '',
    //         ['bold' => true,],
    //         $cellHCentered
    //     );
    //     $row3->addCell(5700, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);
    //     $isApartment = in_array('CC', $certificate->document_type ?? []);

    //     $section->addText("HỢP ĐỒNG CUNG CẤP DỊCH VỤ ", ['bold' => true, 'size' => '16'], ['align' => 'center']);
    //     $section->addText(
    //         "TƯ VẤN THẨM ĐỊNH GIÁ TÀI SẢN",
    //         ['bold' => true, 'size' => '16'],
    //         ['align' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12)]
    //     );
    //     $indent13 = ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.13), 'firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.1)]];
    //     $section->addText(
    //         "- Căn cứ Bộ Luật dân sự nước Cộng Hoà Xã Hội Chủ Nghĩa Việt Nam số 91/2015/QH13 được Quốc Hội thông qua ngày 24/11/2015 có hiệu lực ngày 01/01/2017;",
    //         ['italic' => true],
    //         $indent13
    //     );
    //     $section->addText(
    //         "- Căn cứ Luật thương mại nước Cộng Hoà Xã Hội Chủ Nghĩa Việt Nam do Quốc Hội thông qua ngày 01/01/2006;",
    //         ['italic' => true],
    //         $indent13
    //     );
    //     $section->addText(
    //         "- Căn cứ Luật giá số 11/2012/QH13 do Quốc Hội thông qua ngày 20/06/2012, có hiệu lực thi hành từ ngày 01/01/2013;",
    //         ['italic' => true],
    //         $indent13
    //     );
    //     $section->addText(
    //         "- Căn cứ vào chức năng quyền hạn của Công ty TNHH Thẩm định giá Nova và nhu cầu của khách hàng.",
    //         ['italic' => true],
    //         $indent13
    //     );
    //     // Lấy ngày khảo sát
    //     $stringTime = "ngày " . '  ' . " tháng " . '  ' . " năm " . '    ';
    //     $stringTimeSoc = '';
    //     if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
    //         $document_date = date_create($certificate->document_date);
    //         $stringTime = "ngày " . $document_date->format('d') . " tháng " . $document_date->format('m') . " năm " . $document_date->format('Y');
    //     }
    //     if (isset($certificate->issue_date_card) && !empty(trim($certificate->issue_date_card))) {
    //         $issue_date_card = date_create($certificate->issue_date_card);
    //         $stringTimeSoc =  $issue_date_card->format('d') . "/" . $issue_date_card->format('m') . "/" . $issue_date_card->format('Y');
    //     }
    //     $section->addText(
    //         "Hôm nay," .  $stringTime . " tại văn phòng Công ty TNHH Thẩm định giá Nova, chúng tôi gồm có:",
    //         null,
    //         ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.23)]]
    //     );

    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $alignBoth = ['align' => 'both'];
    //     $cellVTop = ['valign' => 'top'];
    //     $row1 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row1->addCell(1800, $cellVTop)->addText('BÊN A', ['bold' => true,],  $alignBoth);
    //     $row1->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row1->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_name), ['bold' => true],  $alignBoth);

    //     $row2 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
    //     => false));
    //     $row2->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
    //     $row2->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row2->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_address), null,  $alignBoth);

    //     $row3 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row3->addCell(1800, $cellVTop)->addText('-    Số CCCD', null,  $alignBoth);
    //     $row3->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row3->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_identity_card), null,  $alignBoth);

    //     $row31 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row31->addCell(1800, $cellVTop)->addText('-    Ngày cấp', null,  $alignBoth);
    //     $row31->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row31->addCell(8100, $cellVTop)->addText($stringTimeSoc, null,  $alignBoth);

    //     $row32 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row32->addCell(1800, $cellVTop)->addText('-    Nơi cấp', null,  $alignBoth);
    //     $row32->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row32->addCell(8100, $cellVTop)->addText($certificate->issue_place_card ? htmlspecialchars($certificate->issue_place_card) : "", null,  $alignBoth);

    //     $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row4->addCell(1800, $cellVTop)->addText('-    Số điện thoại', null,  $alignBoth);
    //     $row4->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row4->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_phone), null,  $alignBoth);


    //     $row5 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row5->addCell(1800, $cellVTop)->addText('BÊN B', ['bold' => true,],  $alignBoth);
    //     $row5->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row5->addCell(8100, $cellVTop)->addText('CÔNG TY TNHH THẨM ĐỊNH GIÁ NOVA', ['bold' => true],  $alignBoth);

    //     $row6 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
    //     => false));
    //     $row6->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
    //     $row6->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row6->addCell(8100, $cellVTop)->addText('Số 728-730 Võ Văn Kiệt, Phường 1, Quận 5, TP.HCM', null,  $alignBoth);

    //     $row7 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row7->addCell(1800, $cellVTop)->addText('-    Điện thoại bàn', null,  $alignBoth);
    //     $row7->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row7->addCell(8100, $cellVTop)->addText('(028) 3920 6779 – Fax: (028) 3920 6778', null,  $alignBoth);

    //     $row8 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row8->addCell(1800, $cellVTop)->addText('-   Mã số thuế', null,  $alignBoth);
    //     $row8->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row8->addCell(8100, $cellVTop)->addText('0314514140', null,  $alignBoth);


    //     $row9 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row9->addCell(1800, $cellVTop)->addText('-   Tài khoản số', null,  $alignBoth);
    //     $row9->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row9->addCell(8100, $cellVTop)->addText('3101 00024 27729 tại Ngân hàng TMCP Đầu tư và Phát triển Việt Nam – CN Hồ Chí Minh – PGD Trần Hưng Đạo.', null,  $alignBoth);

    //     $row10 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
    //     $row10->addCell(1800, $cellVTop)->addText('-   Đại diện', null,  $alignBoth);
    //     $row10->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $textRun = $row10->addCell(8100, $cellVTop)->addTextRun($alignBoth);

    //     $chucvu = isset($certificate->appraiserConfirm) && isset($certificate->appraiserConfirm->appraisePosition)
    //         ? $certificate->appraiserConfirm->appraisePosition->description
    //         : (isset($certificate->appraiserManager) && isset($certificate->appraiserManager->appraisePosition)
    //             ? $certificate->appraiserManager->appraisePosition->description
    //             : '');
    //     $chucvu = mb_convert_case(mb_strtolower($chucvu), MB_CASE_TITLE, "UTF-8");

    //     $daidien = isset($certificate->appraiserConfirm)
    //         ? $certificate->appraiserConfirm->name
    //         : (isset($certificate->appraiserManager)
    //             ? $certificate->appraiserManager->name
    //             : '');
    //     $textRun->addText('Ông ', ['bold' => false]);
    //     $textRun->addText($daidien, ['bold' => true]);
    //     $textRun->addText(' – Chức vụ: ' . $chucvu, ['bold' => false]);
    //     $section->addText(
    //         "Sau khi thương lượng, hai bên đồng ý ký kết hợp đồng cung cấp dịch vụ thẩm định giá tài sản với các điều kiện và điều khoản như sau:",
    //         null,
    //         ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]
    //     );

    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 1:', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText(' Nội dung công việc thực hiện', ['bold' => true]);

    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("1.1.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Bên A yêu cầu Bên B thực hiện việc tư vấn thẩm định giá tài sản cho Bên A, chi tiết cụ thể như sau:", null, $indentleftNumber);

    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row1 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row1->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
    //     $row1->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
    //     $textRun = $row1->addCell(8700, $cellVTop)->addTextRun($alignBoth);
    //     $appraiseAssetName = '';
    //     $appraiseApproaches = [];
    //     $appraiseMethodUsed = [];
    //     $appraiseMethodUsedStr = '';
    //     if (isset($priceEstimatePrint)) {
    //         foreach ($priceEstimatePrint as $index => $item) {
    //             $appraiseAssetName .= ($index == 0 ?  htmlspecialchars($item->appraise_asset) : ' và ' . htmlspecialchars($item->appraise_asset));
    //         }
    //     } elseif ($certificate->realEstate && count($certificate->realEstate) > 0) {
    //         if ($isApartment) {
    //             foreach ($certificate->realEstate as $index => $item) {
    //                 if ($item->apartment) {
    //                     $appraiseAssetName .= ($index == 0 ?  htmlspecialchars($item->apartment->appraise_asset) : ' và ' . htmlspecialchars($item->apartment->appraise_asset));
    //                     $apartment = $item->apartment;
    //                     $appraiseApproaches[$apartment->apartmentAppraisalBase->approach->id] = $apartment->apartmentAppraisalBase;
    //                 }
    //             }
    //             foreach ($appraiseApproaches as $item) {
    //                 array_push($appraiseMethodUsed, $item->methodUsed->name);
    //             }
    //             $appraiseMethodUsedStr = implode(', ', $appraiseMethodUsed);
    //             $appraiseMethodUsedStr = mb_strtolower($appraiseMethodUsedStr, 'utf8');
    //         } else {
    //             foreach ($certificate->realEstate as $index => $item) {
    //                 if ($item->appraises) {
    //                     $appraiseAssetName .= ($index == 0 ?  htmlspecialchars($item->appraises->appraise_asset) : ' và ' . htmlspecialchars($item->appraises->appraise_asset));
    //                     $appraise = $item->appraises;
    //                     $appraiseApproaches[$appraise->appraiseApproach->id] = $appraise;
    //                 }
    //             }
    //             foreach ($appraiseApproaches as $item) {
    //                 array_push($appraiseMethodUsed, $item->appraiseMethodUsed->name);
    //             }
    //             $appraiseMethodUsedStr = implode(', ', $appraiseMethodUsed);
    //             $appraiseMethodUsedStr = mb_strtolower($appraiseMethodUsedStr, 'utf8');
    //         }
    //     }

    //     $textRun->addText('Tài sản thẩm định giá : ' . $appraiseAssetName);
    //     // $textRun->addText('(Theo Giấy chứng nhận quyền sử dụng đất quyền sở hữu nhà ở và tài sản khác gắn liền với đất số CK 096662 số vào sổ cấp GCN:CS23305/DA ngày 30/05/2018 do Sở Tài Nguyên và Môi Trường thành phố Hồ Chí Minh cấp).', ['italic' => true]);

    //     $alignCenter =
    //         ['align' => 'center'];
    //     if ((isset($certificate->apartmentAssetPrint) && count($certificate->apartmentAssetPrint) > 0) ||
    //         (isset($certificate->appraises) && count($certificate->appraises) > 0) ||
    //         (isset($priceEstimatePrint) && count($priceEstimatePrint) > 0)
    //     ) {
    //         $table = $section->addTable([
    //             'borderSize' => 1,
    //             'align' => JcTable::START,
    //             'width' => 100 * 50,
    //             'unit' => 'pct'
    //         ]);
    //         $row1 = $table->addRow(100, array(
    //             'tblHeader' => false,
    //             'cantSplit' => true
    //         ));

    //         $row1->addCell(800, $cellVCentered)->addText('STT', ['bold' => true],  $alignCenter);
    //         $row1->addCell(7500, $cellVCentered)->addText('Hạng mục', ['bold' => true], $alignCenter);
    //         $row1->addCell(1600, $cellVCentered)->addText("Diện tích sàn (m\u{00B2})", ['bold' => true], $alignCenter);
    //     }
    //     $addressHSTD = '';
    //     if (isset($priceEstimatePrint)) {
    //         foreach ($priceEstimatePrint as $index => $item) {
    //             $addressHSTD = $item->full_address;
    //             $appraiseAssetNameApartment =  $item->appraise_asset;
    //             $row2 = $table->addRow(100, array(
    //                 'tblHeader' => false,
    //                 'cantSplit' => false
    //             ));
    //             $row2->addCell(800, $cellVTop)->addText('-', null,  $alignCenter);
    //             $row2->addCell(7500, $cellVTop)->addText($appraiseAssetNameApartment ? $appraiseAssetNameApartment : '', null, ['align' => 'left']);
    //             $row2->addCell(1600, $cellVTop)->addText(isset($item->total_area) ? $this->formatNumberFunction($item->total_area, 2, ',', '.') : '', null, ['align' => 'right', 'indentation' => ['right' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.15)]]);
    //         }
    //     } else {
    //         if ($isApartment) {
    //             foreach ($certificate->realEstate as $index => $item) {
    //                 if ($item->apartment) {
    //                     $addressHSTD = $item->apartment->full_address;
    //                     $appraiseAssetNameApartment =  $item->apartment->appraise_asset;
    //                     $row2 = $table->addRow(100, array(
    //                         'tblHeader' => false,
    //                         'cantSplit' => false
    //                     ));
    //                     $row2->addCell(800, $cellVTop)->addText('-', null,  $alignCenter);
    //                     $row2->addCell(7500, $cellVTop)->addText($appraiseAssetNameApartment ? $appraiseAssetNameApartment : '', null, ['align' => 'left']);
    //                     $row2->addCell(1600, $cellVTop)->addText(isset($item->apartment->apartmentAssetProperties) && isset($item->apartment->apartmentAssetProperties->area) ? $this->formatNumberFunction($item->apartment->apartmentAssetProperties->area, 2, ',', '.') : '', null, ['align' => 'right', 'indentation' => ['right' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.15)]]);
    //                 }
    //             }
    //         } else {
    //             foreach ($certificate->realEstate as $index => $item) {
    //                 if ($item->appraises) {
    //                     $addressHSTD = $item->appraises->full_address;
    //                     $appraiseAssetNameAppraise =  $item->appraises->appraise_asset;
    //                     if ($item->appraises->tangibleAssets) {
    //                         foreach ($item->appraises->tangibleAssets as $index2 => $item2) {
    //                             $row2 = $table->addRow(100, array(
    //                                 'tblHeader' => false,
    //                                 'cantSplit' => false
    //                             ));

    //                             $row2->addCell(800, $cellVTop)->addText('-', null,  $alignCenter);
    //                             $row2->addCell(7500, $cellVTop)->addText($appraiseAssetNameAppraise ? $appraiseAssetNameAppraise : '', null, ['align' => 'left']);
    //                             $row2->addCell(1600, $cellVTop)->addText(isset($item2->total_construction_base) ? $this->formatNumberFunction($item2->total_construction_base, 2, ',', '.') : '', null, ['align' => 'right', 'indentation' => ['right' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.15)]]);
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row1 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row1->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
    //     $row1->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
    //     $row1->addCell(2900, $cellVTop)->addText('Địa điểm thẩm định giá', null,  $alignBoth);
    //     $row1->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row1->addCell(5900, $cellVTop)->addText(htmlspecialchars($addressHSTD), null,  $alignBoth);

    //     $appraise_date_formatted = $certificate->appraise_date
    //         ? 'Tháng ' . date('m/Y', strtotime($certificate->appraise_date))
    //         : null;
    //     $row2 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row2->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
    //     $row2->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
    //     $row2->addCell(2900, $cellVTop)->addText('Thời điểm thẩm định giá ', null,  $alignBoth);
    //     $row2->addCell(
    //         100,
    //         $cellVTop
    //     )->addText(':', null,  $alignBoth);
    //     $row2->addCell(5900, $cellVTop)->addText($appraise_date_formatted ? $appraise_date_formatted . '.' : '', null,  $alignBoth);

    //     $row3 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row3->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
    //     $row3->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
    //     $row3->addCell(2900, $cellVTop)->addText('Mục đích thẩm định giá', null,  $alignBoth);
    //     $row3->addCell(
    //         100,
    //         $cellVTop
    //     )->addText(':', null,  $alignBoth);
    //     $row3->addCell(5900, $cellVTop)->addText((isset($certificate->appraisePurpose) ? $certificate->appraisePurpose->name . '.' : ''), null,  $alignBoth);

    //     $row4 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row4->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
    //     $row4->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
    //     $row4->addCell(2900, $cellVTop)->addText('Phương pháp thẩm định giá', null, ['align' => 'left']);
    //     $row4->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
    //     $row4->addCell(5900, $cellVTop)->addText('Sử dụng ' . $appraiseMethodUsedStr, null,  $alignBoth);
    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row5 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row5->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
    //     $row5->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
    //     $row5->addCell(3600, $cellVTop)->addText('Bên sử dụng kết quả thẩm định giá: ', null, ['align' => 'left']);
    //     // $row5->addCell(5100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_name) . '.', null,  $alignBoth);
    //     $row5->addCell(5100, $cellVTop)->addText('Khách hàng thẩm định giá.', null,  $alignBoth);

    //     $row6 = $table->addRow(100, array(
    //         'tblHeader' => false,
    //         'cantSplit' => false
    //     ));
    //     $row6->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
    //     $row6->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
    //     $row6->addCell(3600, $cellVTop)->addText('Số lượng chứng thư Bên A yêu cầu: ', null, ['align' => 'left']);
    //     $row6->addCell(5100, $cellVTop)->addText('02 bản chính bằng tiếng Việt.', null,  $alignBoth);

    //     $section->addText(
    //         "1.2.  Bên B đồng ý thực hiện tư vấn thẩm định giá tài sản nêu trên cho Bên A.",
    //         null,
    //         ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.16)]]
    //     );


    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 2:', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText(' Tiến trình thực hiện', ['bold' => true]);


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("2.1.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Bên A yêu cầu bên B thẩm định tài sản theo giấy yêu cầu thẩm định giá có xác nhận đồng ý nhận thẩm định giá của bên B.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("2.2.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Bên A cung cấp đầy đủ hồ sơ pháp lý của tài sản và hướng dẫn người của Bên B thẩm định hiện trạng tài sản tại hiện trường. Bên A thanh toán tiền đợt 1 cho bên B làm chi phí để bên B cử cán bộ và phương tiện phối hợp với bên A đi khảo sát thu thập thông tin và thẩm định hiện trường. Mức chi phí trên do hai bên thỏa thuận tùy thuộc vào loại tài sản và vị trí tài sản và được tính là chi phí không hoàn lại. Hình thức thanh toán bằng chuyển khoản hoặc thanh toán tiền mặt tại thời điểm hiện trường.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("2.3.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Tối đa 03 ngày làm việc, kể từ ngày bên B đã nhận đủ hồ sơ pháp lý và đi thẩm định hiện trường hoàn tất, bên B sẽ cử người gọi điện thoại thông báo kết quả sơ bộ cho bên A. Nếu bên A đồng ý phát hành chứng thư thẩm định giá thì bên A thanh toán đủ 100% phí thẩm định cho bên B, sau đó bên B sẽ cấp chứng thư cho bên A. ", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("2.4.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Sau khi nhận được kết quả sơ bộ mà bên A yêu cầu ngừng dịch vụ thì bên B sẽ hủy toàn bộ hồ sơ theo Quy trình lưu trữ nội bộ, Bên B sẽ không hoàn trả lại số tiền đã nhận của Bên A. ", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("2.5.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Trường hợp Bên A có nhu cầu thẩm định giá bổ sung các tài sản ngoài danh mục thì các bên sẽ thỏa thuận cụ thể về thời gian, quy trình, mức phí thẩm định đối với các tài sản phát sinh. ", null, $indentleftNumber);

    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 3:', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText(' Giá trị pháp lý của Chứng thư thẩm định giá', ['bold' => true]);


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("3.1.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Chứng thư thẩm định giá do Bên B cung cấp chỉ có giá trị đối với tài sản thẩm định giá tại thời điểm thẩm định giá và địa điểm thẩm định giá.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("3.2.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Chứng thư thẩm định giá do Bên B cung cấp chỉ nhằm thực hiện mục đích ghi trong hợp đồng này.", null, $indentleftNumber);

    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 4:', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText(' Phí dịch vụ thẩm định và phương thức thanh toán', ['bold' => true]);


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("4.1.", null, ['align' => 'right']);
    //     $textRun = $row->addCell(9300)->addTextRun($indentleftNumber);
    //     $textRun->addText("Phí dịch vụ thẩm định giá tài sản: ");
    //     if (isset($certificate->service_fee)) {
    //         $textServiceFee =  $this->formatNumberFunction($certificate->service_fee, 2, ',', '.');

    //         $textRun->addText(
    //             $textServiceFee,
    //             ['bold' => true]
    //         );
    //         $textRun->addText(' (Bằng chữ: ' . ucfirst(CommonService::convertNumberToWords($certificate->service_fee ?? 0)) . ' đồng chẵn./.).', ['italic' => true]);
    //     }
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Ghi chú: Phí dịch vụ đã bao gồm thuế giá trị gia tăng.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("4.2.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Phí dịch vụ thẩm định giá được thanh toán ngay sau khi hợp đồng thẩm định giá có hiệu lực.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("4.3.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Phương thức thanh toán: Tiền mặt hoặc Chuyển khoản.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("4.4.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Trường hợp có nhu cầu cung cấp thêm Chứng thư thẩm định giá ngoài số lượng Chứng thư thẩm định giá thì Bên A phải thanh toán cho Bên B mức phí tương ứng.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("4.5.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Nếu Bên A không thanh toán hết toàn bộ phí thẩm định cho Bên B ghi trong hợp đồng này thì Bên B sẽ không chịu trách nhiệm về kết quả thẩm định giá ghi trong chứng thư đã cấp cho Bên A (Chứng thư thẩm định giá sẽ không có giá trị pháp lý) và Bên B sẽ không chịu bất cứ trách nhiệm nào về chứng thư thẩm định giá.", null, $indentleftNumber);

    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 5:', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText(' Quyền và nghĩa vụ của Bên A', ['bold' => true]);


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("5.1.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Nhận Chứng thư thẩm định giá theo quy định tại điều 1 hợp đồng này.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("5.2.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Khi Bên A yêu cầu bên B cung cấp thêm quyển Chứng thư thì phải trả thêm số tiền phí tương ứng với số lượng yêu cầu. Bên A yêu cầu bên B cử nhân viên làm việc ngoài giờ thì Bên B có quyền thương lượng thu thêm phí dịch vụ ngoài giờ.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("5.3.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Cung cấp cho Bên B các hồ sơ, tài liệu liên quan đến tài sản thẩm định giá, hướng dẫn Bên B khảo sát hiện trạng tài sản và chịu trách nhiệm về tính hợp pháp, xác thực, đầy đủ của các hồ sơ, tài liệu mà mình cung cấp.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("5.4.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Chỉ được sử dụng Chứng thư thẩm định giá đúng mục đích thỏa thuận trong hợp đồng này. Nếu sử dụng sai mục đích, Bên A hoàn toàn chịu trách nhiệm trước pháp luật.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("5.5.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Thanh toán cho Bên B đầy đủ phí dịch vụ thẩm định giá theo thỏa thuận trong hợp đồng và các chi phí khác phát sinh khác nếu có. Trường hợp Bên A chưa thanh toán hết phí dịch vụ cho Bên B thì bên B không có trách nhiệm phải giao chứng thư cho Bên A. Bên B được miễn trừ hoàn toàn trách nhiệm đối với chứng thư khách hàng đã nhận mà chưa thanh toán đủ 100% phí dịch vụ ghi tại hợp đồng này.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("5.6.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Bên A sẽ được ưu tiên giảm 20% mức phí, nếu yêu cầu tái thẩm định cùng tài sản ghi trên hợp đồng này trong vòng 06 tháng kể từ ngày thẩm định giá lần đầu.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("5.7.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Các quyền và nghĩa vụ khác theo quy định pháp luật.", null, $indentleftNumber);

    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 6:', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText(' Quyền và nghĩa vụ của Bên B', ['bold' => true]);


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("6.1.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Yêu cầu Bên A cung cấp hồ sơ pháp lý, các tài liệu liên quan đến tài sản thẩm định giá.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("6.2.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Cung cấp cho Bên A Chứng thư sau khi nhận đủ phí dịch vụ quy định trong hợp đồng.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("6.3.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Tuân thủ các quy định của pháp luật trong quá trình thực hiện thẩm định giá tài sản.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("6.4.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Từ chối thực hiện thẩm định giá tài sản theo yêu cầu của Bên A khi nhận thấy tài sản đó không đủ điều kiện pháp lý để thực hiện việc thẩm định.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("6.5.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Bên B và người lao động của Bên B sẽ được miễn trừ toàn bộ trách nhiệm đối với kết quả thẩm định giá nếu Bên A hoặc người của Bên A cung cấp sai lệch hồ sơ pháp lý tài sản, thông tin tài sản không chính xác và không trung thực.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("6.6.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Sau khi đã nhận chứng thư mà Bên A không được ngân hàng cấp tín dụng, công ty thẩm định giá sẽ không hoàn trả chi phí thẩm định mà Bên A đã thanh toán trước đó.", null, $indentleftNumber);

    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("6.7.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Các quyền và nghĩa vụ khác theo quy định pháp luật.", null, $indentleftNumber);


    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 7: ', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText('Chấm dứt hợp đồng', ['bold' => true]);


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);


    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("7.1.", null, ['align' => 'right']);
    //     $row->addCell(9300, ['gridSpan' => 2])->addText("Hợp đồng được chấm dứt trong các trường hợp sau:", null, $indentleftNumber);

    //     $row2 = $table->addRow();
    //     $row2->addCell(600)->addText("a.", null, ['align' => 'right']);
    //     $row2->addCell(50)->addText("", null, ['align' => 'right']);
    //     $row2->addCell(9250)->addText("Hai bên hoàn thành nghĩa vụ theo thỏa thuận trong hợp đồng;", null, $indentleftword);

    //     $row3 = $table->addRow();
    //     $row3->addCell(600)->addText("b.", null, ['align' => 'right']);
    //     $row3->addCell(50)->addText("", null, ['align' => 'right']);
    //     $row3->addCell(9250)->addText("Hai bên thỏa thuận chấm dứt hợp đồng trước thời hạn:", null, $indentleftword);

    //     $row4 = $table->addRow();
    //     $row4->addCell(600)->addText("-", null, ['align' => 'right']);
    //     $row4->addCell(80)->addText("", null, ['align' => 'right']);
    //     $row4->addCell(9220)->addText("Trường hợp Bên A muốn chấm dứt hợp đồng trước hạn thì phải thông báo trước cho Bên B 01 ngày và phải thanh toán cho Bên B tiền phí dịch vụ tương ứng với khối lượng công việc Bên B đã thực hiện. Phí này do Bên B xác định và không thấp hơn phí đã thanh toán đợt 1.", null, $indentleftSymbol);

    //     $row5 = $table->addRow();
    //     $row5->addCell(600)->addText("-", null, ['align' => 'right']);
    //     $row5->addCell(80)->addText("", null, ['align' => 'right']);
    //     $row5->addCell(9220)->addText("Trường hợp Bên B muốn chấm dứt hợp đồng trước hạn thì phải thông báo trước cho Bên A 01 ngày và phải hoàn lại cho Bên A toàn bộ số tiền phí dịch vụ mà Bên B đã nhận.", null, $indentleftSymbol);

    //     $row6 = $table->addRow();
    //     $row6->addCell(600)->addText("c.", null, ['align' => 'right']);
    //     $row6->addCell(50)->addText("", null, ['align' => 'right']);
    //     $row6->addCell(9250)->addText("Đơn phương chấm dứt hợp đồng: Trường hợp một trong hai bên vi phạm các điều khoản trong hợp đồng thì bên kia có quyền đơn phương chấm dứt hợp đồng mà không cần báo trước. Bên vi phạm hợp đồng phải bồi thường thiệt hại (nếu có) cho bên kia.", null, $indentleftword);

    //     $row7 = $table->addRow();
    //     $row7->addCell(600)->addText("7.2.", null, ['align' => 'right']);
    //     $row7->addCell(9300, ['gridSpan' => 2])->addText("Hai bên thỏa thuận được quyền đơn phương chấm dứt hợp đồng trong các trường hợp sau đây: ", null, $indentleftNumber);

    //     $row8 = $table->addRow();
    //     $row8->addCell(600)->addText("a.", null, ['align' => 'right']);
    //     $row8->addCell(
    //         50
    //     )->addText("", null, ['align' => 'right']);
    //     $row8->addCell(9250)->addText("Sau 10 ngày ký hợp đồng mà Bên A không hướng dẫn Bên B thẩm định tài sản hiện trường; hoặc tối đa 10 ngày sau khi nhận thông báo (bằng thư qua email/hoặc tin nhắn điện thoại) mà Bên A không đến công ty thẩm định giá để nhận chứng thư thì Bên B có quyền đơn phương chấm dứt hợp đồng với Bên A, hợp đồng này mặc nhiên thanh lý.", null, $indentleftword);

    //     $row9 = $table->addRow();
    //     $row9->addCell(600)->addText("b.", null, ['align' => 'right']);
    //     $row9->addCell(
    //         50
    //     )->addText("", null, ['align' => 'right']);
    //     $row9->addCell(9250)->addText("Nếu Bên B không cung cấp chứng thư thẩm định giá cho Bên A theo đúng thời gian thỏa thuận thì Bên A có quyền đơn phương chấm dứt hợp đồng với Bên B, đồng thời yêu cầu Bên B phải hoàn tiền đã nhận lại cho Bên A.", null, $indentleftword);

    //     $textRun = $section->addTextRun(['align' => 'both']);
    //     $textRun->addText('Điều 8:', ['bold' => true, 'underline' => 'single']);
    //     $textRun->addText(' Các thỏa thuận khác', ['bold' => true]);


    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(600)->addText("8.1.", null, ['align' => 'right']);
    //     $row->addCell(9300)->addText("Mọi sửa đổi, bổ sung hợp đồng này phải do hai bên thỏa thuận, được lập thành văn bản và là một phần không tách rời của hợp đồng.", null, $indentleftNumber);


    //     $row2 = $table->addRow();
    //     $row2->addCell(600)->addText("8.2.", null, ['align' => 'right']);
    //     $row2->addCell(9300)->addText("Trong quá trình thực hiện hợp đồng, nếu phát sinh tranh chấp, hai bên sẽ cùng nhau thương lượng trên tinh thần hợp tác. Trường hợp không thương lượng được, vụ việc sẽ do Tòa án nhân dân Quận 5, TP.HCM giải quyết.", null, $indentleftNumber);


    //     $row3 = $table->addRow();
    //     $row3->addCell(600)->addText("8.3.", null, ['align' => 'right']);
    //     $row3->addCell(9300)->addText("Sau khi Bên B nhận đủ phí và cung cấp kết quả thẩm định cho Bên A thì trách nhiệm và nghĩa vụ hai bên chấm dứt, hợp đồng này mặc nhiên được thanh lý.", null, $indentleftNumber);


    //     $row4 = $table->addRow();
    //     $row4->addCell(600)->addText("8.4.", null, ['align' => 'right']);
    //     $row4->addCell(9300)->addText("Hợp đồng này có hiệu lực kể từ ngày ký, được lập thành 03 bản, có giá trị pháp lý như nhau, Bên A giữ 01 bản, bên B giữ 02 bản.", null, $indentleftNumber);

    //     $table = $section->addTable([
    //         'align' => JcTable::START,
    //         'width' => 100 * 50,
    //         'unit' => 'pct'
    //     ]);
    //     $row = $table->addRow();
    //     $row->addCell(4950)->addText("ĐẠI DIỆN BÊN A", ['bold' => true], ['align' => 'center']);
    //     $row->addCell(4950)->addText("ĐẠI DIỆN BÊN B", ['bold' => true], ['align' => 'center']);

    //     $textNamePetitioner = mb_strtoupper($certificate->petitioner_name);
    //     $textNamePetitioner = str_replace(['ÔNG / BÀ ', 'BÀ ', 'ÔNG '], '', $textNamePetitioner);
    //     $row2 = $table->addRow();
    //     $row2->addCell(4950)->addText("", ['bold' => true], ['align' => 'center']);
    //     $row2->addCell(4950)->addText($chucvu, ['bold' => true], ['align' => 'center']);

    //     $row3 = $table->addRow();
    //     $row3->addCell(4950)->addText("\n\n\n\n\n");
    //     $row3->addCell(4950)->addText("\n\n\n\n\n");

    //     $row5 = $table->addRow();
    //     $row5->addCell(4950)->addText("\n\n\n\n\n");
    //     $row5->addCell(4950)->addText("\n\n\n\n\n");

    //     $row6 = $table->addRow();
    //     $row6->addCell(4950)->addText("\n\n\n\n\n");
    //     $row6->addCell(4950)->addText("\n\n\n\n\n");

    //     $row4 = $table->addRow();
    //     $row4->addCell(4950)->addText($textNamePetitioner, ['bold' => true], ['align' => 'center']);
    //     $row4->addCell(4950)->addText(mb_strtoupper($daidien), ['bold' => true], ['align' => 'center']);



    //     $footer = $section->addFooter();
    //     $table = $footer->addTable();
    //     $table->addRow();
    //     $table->addCell(9900)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
    //     $reportUserName = CommonService::getUserReport();
    //     $reportName = 'HDTDG' . '_' . htmlspecialchars($certificate->petitioner_name);
    //     $reportName = str_replace(
    //         ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
    //         '',
    //         $reportName
    //     ); // replace invalid characters with underscore
    //     $reportName = str_replace(
    //         ' ',
    //         '_',
    //         $reportName
    //     ); // replace invalid characters with underscore
    //     $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
    //     $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
    //     $fileName = $reportName . '_' . $downloadTime . '_' . $downloadDate;

    //     $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    //     $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
    //     $path =  env('STORAGE_DOCUMENTS') . '/' . 'comparison_brief/' . $now->format('Y') . '/' . $now->format('m') . '/';
    //     if (!File::exists(storage_path('app/public/' . $path))) {
    //         File::makeDirectory(storage_path('app/public/' . $path), 0755, true);
    //     }
    //     try {
    //         $objWriter->save(storage_path('app/public/' . $path . $fileName . '.docx'));
    //     } catch (\Exception $e) {
    //         throw $e;
    //     }
    //     $data = [];
    //     $data['url'] = Storage::disk('public')->url($path .  $fileName . '.docx');
    //     $data['file_name'] = $fileName;
    //     $data['certificate'] = $certificate;
    //     $data['appraises'] = $check;
    //     return $data;
    // }
    public function generateDocx($company, $certificate, $format, $appraises, $priceEstimatePrint): array
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
            'align' => JcTable::CENTER,
            // 'spaceBefore'        =>  240
            // 'cellMarginLeft'  => Converter::inchToTwip(1.0),

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
        $marginLeft =  array('marginLeft' => 400);
        $cellHJustify = array('align' => 'both');
        $cellVJustify = array('valign' => 'both');
        // [] = ['indentation' => ['firstLine' => 360]];
        $keepNext = ['keepNext' => true];

        $indentleftword =
            ['align' => 'both'];
        $indentleftNumber =
            ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.05)]];
        $indentleftSymbol =
            ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.15)]];

        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            // 'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.13)),

            'align' => 'both'
        ]);
        $styleSection = [
            'footerHeight' => 300,
            'marginTop' => Converter::inchToTwip(0.7),
            'marginBottom' => Converter::inchToTwip(0.28),
            'marginRight' => Converter::inchToTwip(0.3),
            'marginLeft' => Converter::inchToTwip(0.6)
        ];

        $check = "";

        $section = $phpWord->addSection($styleSection);
        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row1 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row1->addCell(3500, $cellVCentered)->addText('CÔNG TY TNHH', ['bold' => true,], $cellHCentered);
        $row1->addCell(1000, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);
        $row1->addCell(5700, $cellVCentered)->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', ['bold' => true], $cellHCentered);
        $row2 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
        $row2->addCell(3500, $cellVCentered)->addText('THẨM ĐỊNH GIÁ NOVA', ['bold' => true,  'underline' => 'single'], $cellHCentered);
        $row2->addCell(1000, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);
        $row2->addCell(5700, $cellVCentered)->addText('Độc lập – Tự do - Hạnh phúc', ['bold' => true,   'underline' => 'single'], $cellHCentered);
        $row3 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
        $row3->addCell(3500, $cellVCentered)->addText('Số: ' . (isset($certificate->document_num) ? $certificate->document_num . '' : ''), null, $cellHCentered);
        $row3->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        $row3->addCell(5700, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);
        $isApartment = in_array('CC', $certificate->document_type ?? []);

        $section->addText("HỢP ĐỒNG CUNG CẤP DỊCH VỤ ", ['bold' => true, 'size' => '16'], ['align' => 'center']);
        $section->addText(
            "TƯ VẤN THẨM ĐỊNH GIÁ TÀI SẢN",
            ['bold' => true, 'size' => '16'],
            ['align' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12)]
        );
        $indent13 = ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.13), 'firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.1)]];
        $section->addText(
            "- Căn cứ Bộ Luật dân sự nước Cộng Hoà Xã Hội Chủ Nghĩa Việt Nam số 91/2015/QH13 được Quốc Hội thông qua ngày 24/11/2015 có hiệu lực ngày 01/01/2017;",
            ['italic' => true],
            $indent13
        );
        $section->addText(
            "- Căn cứ Luật thương mại nước Cộng Hoà Xã Hội Chủ Nghĩa Việt Nam do Quốc Hội thông qua ngày 01/01/2006;",
            ['italic' => true],
            $indent13
        );
        $section->addText(
            "- Căn cứ Luật giá số 11/2012/QH13 do Quốc Hội thông qua ngày 20/06/2012, có hiệu lực thi hành từ ngày 01/01/2013;",
            ['italic' => true],
            $indent13
        );
        $section->addText(
            "- Căn cứ vào chức năng quyền hạn của Công ty TNHH Thẩm định giá Nova và nhu cầu của khách hàng.",
            ['italic' => true],
            $indent13
        );
        // Lấy ngày khảo sát
        $stringTime = "ngày " . '  ' . " tháng " . '  ' . " năm " . Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Y');
        $stringTimeSoc = '';
        if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
            $document_date = date_create($certificate->document_date);
            $stringTime = "ngày " . $document_date->format('d') . " tháng " . $document_date->format('m') . " năm " . $document_date->format('Y');
        }
        if (isset($certificate->issue_date_card) && !empty(trim($certificate->issue_date_card))) {
            $issue_date_card = date_create($certificate->issue_date_card);
            $stringTimeSoc =  $issue_date_card->format('d') . "/" . $issue_date_card->format('m') . "/" . $issue_date_card->format('Y');
        }
        $section->addText(
            "Hôm nay," .  $stringTime . " tại văn phòng Công ty TNHH Thẩm định giá Nova, chúng tôi gồm có:",
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.23)]]
        );

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $alignBoth = ['align' => 'both'];
        $cellVTop = ['valign' => 'top'];
        $row1 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row1->addCell(1800, $cellVTop)->addText('BÊN A', ['bold' => true,],  $alignBoth);
        $row1->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row1->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_name), ['bold' => true],  $alignBoth);

        if ($certificate->is_company == 0) {
            $row2 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
            => false));
            $row2->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
            $row2->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
            $row2->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_address), null,  $alignBoth);

            $row3 = $table->addRow(100, array(
                'tblHeader' => false,
                'cantSplit' => false
            ));
            $row3->addCell(1800, $cellVTop)->addText('-    Số CCCD', null,  $alignBoth);
            $row3->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
            $row3->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_identity_card) . '   -    Ngày cấp: ' . $stringTimeSoc, null,  $alignBoth);
            $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
            $row4->addCell(1800, $cellVTop)->addText('-    Số điện thoại', null,  $alignBoth);
            $row4->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
            $row4->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_phone), null,  $alignBoth);
        } else {
            $row2 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
            => false));
            $row2->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
            $row2->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
            $row2->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_address), null,  $alignBoth);


            $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
            $row4->addCell(1800, $cellVTop)->addText('-    Số điện thoại', null,  $alignBoth);
            $row4->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
            $row4->addCell(8100, $cellVTop)->addText(htmlspecialchars($certificate->petitioner_phone), null,  $alignBoth);

            $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
            $row4->addCell(1800, $cellVTop)->addText('-    Mã số thuế: ', null,  $alignBoth);
            $row4->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
            $row4->addCell(8100, $cellVTop)->addText('', null,  $alignBoth);

            $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
            $row4->addCell(1800, $cellVTop)->addText('-    Đại diện: ', null,  $alignBoth);
            $row4->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
            $row4->addCell(8100, $cellVTop)->addText('                           -    Chức vụ: ', null,  $alignBoth);
        }


        // $row31 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        // $row31->addCell(1800, $cellVTop)->addText('-    Ngày cấp', null,  $alignBoth);
        // $row31->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        // $row31->addCell(8100, $cellVTop)->addText($stringTimeSoc, null,  $alignBoth);

        // $row32 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        // $row32->addCell(1800, $cellVTop)->addText('-    Nơi cấp', null,  $alignBoth);
        // $row32->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        // $row32->addCell(8100, $cellVTop)->addText($certificate->issue_place_card ? htmlspecialchars($certificate->issue_place_card) : "", null,  $alignBoth);




        $row5 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row5->addCell(1800, $cellVTop)->addText('BÊN B', ['bold' => true,],  $alignBoth);
        $row5->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row5->addCell(8100, $cellVTop)->addText('CÔNG TY TNHH THẨM ĐỊNH GIÁ NOVA', ['bold' => true],  $alignBoth);

        $row6 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
        => false));
        $row6->addCell(1800, $cellVTop)->addText('-   Địa chỉ', null,  $alignBoth);
        $row6->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row6->addCell(8100, $cellVTop)->addText('Số 728-730 Võ Văn Kiệt, Phường 1, Quận 5, TP.HCM', null,  $alignBoth);

        $row7 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row7->addCell(1800, $cellVTop)->addText('-   Điện thoại bàn', null,  $alignBoth);
        $row7->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row7->addCell(8100, $cellVTop)->addText('(028) 3920 6779 – Fax: (028) 3920 6778', null,  $alignBoth);

        $row8 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row8->addCell(1800, $cellVTop)->addText('-   Mã số thuế', null,  $alignBoth);
        $row8->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row8->addCell(8100, $cellVTop)->addText('0314514140', null,  $alignBoth);


        $row9 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row9->addCell(1800, $cellVTop)->addText('-   Tài khoản số', null,  $alignBoth);
        $row9->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row9->addCell(8100, $cellVTop)->addText('3101 00024 27729 tại Ngân hàng TMCP Đầu tư và Phát triển Việt Nam – CN Hồ Chí Minh – PGD Trần Hưng Đạo.', null,  $alignBoth);

        $row10 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row10->addCell(1800, $cellVTop)->addText('-   Đại diện', null,  $alignBoth);
        $row10->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $textRun = $row10->addCell(8100, $cellVTop)->addTextRun($alignBoth);

        $chucvu = isset($certificate->appraiserConfirm) && isset($certificate->appraiserConfirm->appraisePosition)
            ? $certificate->appraiserConfirm->appraisePosition->description
            : (isset($certificate->appraiserManager) && isset($certificate->appraiserManager->appraisePosition)
                ? $certificate->appraiserManager->appraisePosition->description
                : '');
        $chucvu = mb_convert_case(mb_strtolower($chucvu), MB_CASE_TITLE, "UTF-8");

        $daidien = isset($certificate->appraiserConfirm)
            ? $certificate->appraiserConfirm->name
            : (isset($certificate->appraiserManager)
                ? $certificate->appraiserManager->name
                : '');
        $textRun->addText('Ông ', ['bold' => false]);
        $textRun->addText(mb_strtoupper($daidien), ['bold' => true]);
        $textRun->addText(' – Chức vụ: ' . $chucvu, ['bold' => false]);
        $section->addText(
            "Sau khi thương lượng, hai bên đồng ý ký kết hợp đồng cung cấp dịch vụ thẩm định giá tài sản với các điều kiện và điều khoản như sau:",
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]
        );

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 1:', ['bold' => true, 'underline' => 'single']);
        $textRun->addText(' Nội dung công việc thực hiện', ['bold' => true]);

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("1.1.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên A đồng ý để Bên B thực hiện việc tư vấn thẩm định giá tài sản cho Bên A, cụ thể như sau:", null, $indentleftNumber);

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row1 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row1->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row1->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $textRun = $row1->addCell(8700, $cellVTop)->addTextRun($alignBoth);
        $appraiseAssetName = '';
        $addressHSTD = '';
        $appraiseApproaches = [];
        $appraiseMethodUsed = [];
        $appraiseMethodUsedStr = '';
        if ($certificate->realEstate && count($certificate->realEstate) > 0) {
            if ($isApartment) {
                foreach ($certificate->realEstate as $index => $item) {
                    if ($item->apartment) {
                        $appraiseAssetName .= ($index == 0 ?  htmlspecialchars($item->apartment->appraise_asset) : ' và ' . htmlspecialchars($item->apartment->appraise_asset));
                        $apartment = $item->apartment;
                        $appraiseApproaches[$apartment->apartmentAppraisalBase->approach->id] = $apartment->apartmentAppraisalBase;
                    }
                }
                foreach ($appraiseApproaches as $item) {
                    array_push($appraiseMethodUsed, $item->methodUsed->name);
                }
                $appraiseMethodUsedStr = implode(', ', $appraiseMethodUsed);
                $appraiseMethodUsedStr = mb_strtolower($appraiseMethodUsedStr, 'utf8');
            } else {
                foreach ($certificate->realEstate as $index => $item) {
                    if ($item->appraises) {
                        $appraiseAssetName .= ($index == 0 ?  htmlspecialchars($item->appraises->appraise_asset) : ' và ' . htmlspecialchars($item->appraises->appraise_asset));
                        $appraise = $item->appraises;
                        $appraiseApproaches[$appraise->appraiseApproach->id] = $appraise;
                    }
                }
                foreach ($appraiseApproaches as $item) {
                    array_push($appraiseMethodUsed, $item->appraiseMethodUsed->name);
                }
                $appraiseMethodUsedStr = implode(', ', $appraiseMethodUsed);
                $appraiseMethodUsedStr = mb_strtolower($appraiseMethodUsedStr, 'utf8');
            }
        }

        $textRun->addText('Quyền sử dụng đất và tài sản trên đất, gồm có: ');
        // $textRun->addText('(Theo Giấy chứng nhận quyền sử dụng đất quyền sở hữu nhà ở và tài sản khác gắn liền với đất số CK 096662 số vào sổ cấp GCN:CS23305/DA ngày 30/05/2018 do Sở Tài Nguyên và Môi Trường thành phố Hồ Chí Minh cấp).', ['italic' => true]);

        $alignCenter =
            ['align' => 'center'];
        $arrayTable = [];
        $checktangibleAsset = "";
        $appraise_law = "";
        if ($certificate->realEstate && count($certificate->realEstate) > 0) {
            if ($isApartment) {
                foreach ($certificate->realEstate as $index => $item) {
                    $firstRow = [];
                    $secondRow = [];
                    $stt = "";
                    if (($index + 1) > 0 && ($index + 1) < 10) {
                        $stt = "0" + strval($index + 1);
                    } else {
                        $stt = ($index + 1);
                    }

                    if ($item->apartment) {
                        if ($item->apartment->law) {
                            foreach ($item->apartment->law as $index2 => $item2) {
                                $appraise_law .= ($index2) ? " và " : "";
                                $appraise_law .= "01 bản photo Giấy  " . $item2->content . " do " . $item2->certifying_agency . " cấp.";
                            }
                        }
                        $addressHSTD = $item->apartment->full_address;
                        // Dòng địa chỉ
                        $firstRow[] = $stt;
                        $firstRow[] = htmlspecialchars($item->apartment->full_address);
                        $firstRow[] = '';
                        $firstRow[] = '';
                        $arrayTable[] = $firstRow;
                        // Dòng tên tài sản
                        $secondRow[] = '';
                        $secondRow[] = htmlspecialchars($item->apartment->appraise_asset);
                        $secondRow[] = (isset($item->total_area) ? $this->formatNumberFunction($item->total_area, 2, ',', '.') : '');
                        $secondRow[] = '';
                        $arrayTable[] = $secondRow;
                    }
                }
            } else {
                foreach ($certificate->realEstate as $index => $item) {

                    $firstRow = [];
                    $secondRow = [];
                    $stt = "";
                    if (($index + 1) > 0 && ($index + 1) < 10) {
                        $stt = "0" + strval($index + 1);
                    } else {
                        $stt = ($index + 1);
                    }

                    if ($item->appraises) {
                        if ($item->appraises->appraiseLaw) {
                            foreach ($item->appraises->appraiseLaw as $index2 => $item2) {
                                $appraise_law .= ($index2) ? " và " : "";
                                $appraise_law .= "01 bản photo Giấy  " . $item2->content . " do " . $item2->certifying_agency . " cấp.";
                            }
                        }
                        $addressHSTD = $item->appraises->full_address;
                        // Dòng địa chỉ
                        $firstRow[] = $stt;
                        $firstRow[] = htmlspecialchars($item->appraises->full_address);
                        $firstRow[] = '';
                        $firstRow[] = '';
                        $arrayTable[] = $firstRow;
                        // Dòng tên tài sản
                        $secondRow[] = '';
                        $secondRow[] = htmlspecialchars($item->appraises->appraise_asset);
                        $secondRow[] = (isset($item->total_area) ? $this->formatNumberFunction($item->total_area, 2, ',', '.') : '');
                        $secondRow[] =
                            isset($item->assetType) && isset($item->assetType->description)
                            ?   ($item->assetType->description == "ĐẤT TRỐNG" ? 'Đất trống' : ($item->assetType->description == "ĐẤT CÓ NHÀ" ? 'Đất có CTXD' : '')) : '';

                        $arrayTable[] = $secondRow;
                        // Dòng CTXD
                        $checktangibleAsset = $item->appraises->tangibleAssets;
                        if (count($checktangibleAsset) > 0) {
                            foreach ($checktangibleAsset as $key => $tangible) {
                                $tempRow = [];
                                $tempRow[] = '';
                                $tempRow[] = $tangible->tangible_name;
                                $tempRow[] = (isset($tangible->total_construction_area) ? $this->formatNumberFunction($tangible->total_construction_area, 2, ',', '.') : '');
                                $tempRow[] = $tangible->remaining_quality == 100 ? 'Mới' : 'Đã qua sử dụng';

                                $arrayTable[] = $tempRow;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($arrayTable)) {

            $table = $section->addTable([
                'cellMarginLeft' => Converter::inchToTwip(0.4),
                'borderSize' => 1,
                'align' => 'end',
                'width' => 50 * 50,
                'unit' => 'pct',
            ]);

            $rowHeader = [
                'tblHeader' => false,
                'cantSplit' => false
            ];
            $cantSplit = ['cantSplit' => false];
            $table->addRow(400, $rowHeader);
            $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered));
            $table->addCell(4500, $cellVCentered)->addText('Hạng mục', ['bold' => true], $cellHCentered);
            $table->addCell(1800, $cellVCentered)->addText('Diện tích (' . $m2 . ')', ['bold' => true], $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText('Hiện trạng', ['bold' => true], $cellHCentered);
            foreach ($arrayTable as $index => $item) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellVCentered)->addText($item[0], ['bold' => false], array_merge($cellHCentered));
                $table->addCell(4500, $cellVJustify)->addText($item[1], ['bold' => false], $cellHJustify);
                $table->addCell(1800, $cellVCentered)->addText($item[2], ['bold' => false], $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText($item[3], ['bold' => false], $cellHCentered);
            }
        }




        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct',

        ]);


        $row3 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row3->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row3->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row3->addCell(8800, $cellVTop)->addText('Mục đích thẩm định giá: ' . (isset($certificate->appraisePurpose) ? $certificate->appraisePurpose->name . '.' : ''), null,  $alignBoth);



        $appraise_date_formatted = $certificate->appraise_date
            ? 'Tháng ' . date('m/Y', strtotime($certificate->appraise_date))
            : null;
        $row2 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row2->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row2->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row2->addCell(8800, $cellVTop)->addText('Thời điểm thẩm định giá: ' . ($appraise_date_formatted ? $appraise_date_formatted . '.' : ''), null,  $alignBoth);



        $row1 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row1->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row1->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row1->addCell(8800, $cellVTop)->addText('Địa điểm thẩm định giá: ' . htmlspecialchars($addressHSTD), null,  $alignBoth);


        $row1 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row1->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row1->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row1->addCell(8800, $cellVTop)->addText('Bên sử dụng Chứng thư, Báo cáo thẩm định giá: ' . htmlspecialchars($certificate->petitioner_name), null,  $alignBoth);



        $row1 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row1->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row1->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row1->addCell(8800, $cellVTop)->addText('Các Hồ sơ, tài liệu, dữ liệu cá nhân do Bên A cung cấp cho bên B để bên B được sử dụng lập Hồ sơ Thẩm định giá tài sản gồm: ' .  $appraise_law, null,  $alignBoth);



        $row4 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row4->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row4->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row4->addCell(2900, $cellVTop)->addText('Phương pháp thẩm định giá: Quy định theo chuẩn mực thẩm định giá Việt Nam hiện hành.', null, ['align' => 'left']);


        $row6 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row6->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row6->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row6->addCell(8800, $cellVTop)->addText('Số lượng chứng thư Bên A yêu cầu: 02 bản chính bằng tiếng Việt.', null, ['align' => 'left']);

        $section->addText(
            "1.2.  Bên B đồng ý thực hiện tư vấn thẩm định giá tài sản nêu trên cho Bên A.",
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.16)]]
        );


        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 2:', ['bold' => true, 'underline' => 'single']);
        $textRun->addText(' Tiến trình thực hiện', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("2.1.", null, ['align' => 'right']);
        $row->addCell(9300)->addText($certificate->is_company == 0 ? 'Bên A yêu cầu bên B tư vấn thẩm định giá tài sản bằng văn bản yêu cầu thẩm định.' : 'Bên A yêu cầu bên B thẩm định tài sản bằng điện thoại, bằng văn bản yêu cầu thẩm định.', null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.2.", null, ['align' => 'right']);
        $row->addCell(9300)->addText($certificate->is_company == 0 ? "Bên A hoặc người của bên A cung cấp đầy đủ hồ sơ pháp lý của tài sản và hướng dẫn người của Bên B thẩm định hiện trạng tài sản tại hiện trường. Bên A thanh toán đợt 1 cho bên B để bên B cử cán bộ và phương tiện phối hợp với bên A đi khảo sát hiện trạng, thu thập thông tin tài sản tại hiện trường; phí thanh toán đợt 1 là mức phí khảo sát hiện trường tài sản, thu thập thông tin thị trường liên quan và lập kết quả sơ bộ, nên được tính là chi phí không hoàn lại. " : "Bên A hoặc người của bên A cung cấp đầy đủ hồ sơ pháp lý của tài sản và hướng dẫn người của Bên B thẩm định hiện trạng tài sản tại hiện trường. Bên A thanh toán đợt 1 cho bên B để bên B cử cán bộ và phương tiện phối hợp với bên A đi khảo sát thu thập thông tin và thẩm định hiện trường. Mức chi phí trên do hai bên thỏa thuận tùy thuộc vào loại tài sản và vị trí tài sản và được tính là chi phí không hoàn lại. ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.3.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Sau khi đi thẩm định hiện trường hoàn tất, tùy theo loại và số lượng tài sản, từ 02-05 ngày, bên B sẽ cử cán bộ đầu mối thông báo kết quả sơ bộ cho bên A hoặc người đầu mối của bên A. Nếu bên A đồng ý phát hành chứng thư và báo cáo thẩm định giá thì bên A thanh toán đủ 100% phí thẩm định cho bên B, sau đó bên B sẽ phát hành Chứng thư và Báo cáo thẩm định giá giao cho bên A. ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.4.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Trường hợp sau khi bên A được thông báo kết quả sơ bộ, mà bên A không đồng ý và yêu cầu ngừng dịch vụ thì bên B sẽ không hoàn phí đã nhận trước (nếu có); hoặc bên A được yêu cầu bên B hoãn phát hành Chứng thư và Báo cáo thẩm định giá, thời gian bên B chờ bên A phản hồi để phát hành sản phẩm là không quá 30 ngày kể từ ngày bên A nhận Kết quả sơ bộ. Quá 30 ngày như trên, Bên B sẽ hủy toàn bộ hồ sơ và Bên A không được quyền đòi lại số tiền đã thanh toán đợt 1 cho bên B. Ngược lại, bên B chậm cung cấp Chứng thư, Báo cáo thẩm định giá cho bên A quá 30 ngày, thì bên A được miễn phí dịch vụ. ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.5.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Trường hợp Bên A có nhu cầu thẩm định giá bổ sung các tài sản ngoài danh mục thì các bên sẽ thỏa thuận cụ thể về mức phí thẩm định đối với các tài sản phát sinh. ", null, $indentleftNumber);

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 3:', ['bold' => true, 'underline' => 'single']);
        $textRun->addText(' Giá trị pháp lý của Chứng thư thẩm định giá', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("3.1.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Chứng thư và Báo cáo thẩm định giá do Bên B phát hành phát hành cho bên A là một trong những cơ sở để bên A, tổ chức, cá nhân có liên quan xem xét, quyết định hoặc phê duyệt giá của tài sản.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("3.2.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Chứng thư và Báo cáo thẩm định giá do Bên B phát hành chỉ có giá trị sử dụng trong thời gian hiệu lực theo đúng mục đích thẩm định giá gắn với đúng thông tin tài sản, số lượng tài sản tại Hợp đồng này. Trường hợp số lượng, khối lượng tài sản khác hợp đồng thì giá tài sản sẽ khác.", null, $indentleftNumber);

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 4:', ['bold' => true, 'underline' => 'single']);
        $textRun->addText(' Phí dịch vụ thẩm định và phương thức thanh toán', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("4.1.", null, ['align' => 'right']);
        $textRun = $row->addCell(9300)->addTextRun($indentleftNumber);
        $textRun->addText("Phí dịch vụ thẩm định giá tài sản: ");
        if (isset($certificate->service_fee)) {
            $textServiceFee =  $this->formatNumberFunction($certificate->service_fee, 2, ',', '.');

            $textRun->addText(
                $textServiceFee,
                ['bold' => true]
            );
            $textRun->addText(' (Bằng chữ: ' . ucfirst(CommonService::convertNumberToWords($certificate->service_fee ?? 0)) . ' đồng chẵn./.).', ['italic' => true]);
        }
        $row = $table->addRow();
        $row->addCell(600)->addText("", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Ghi chú: Phí dịch vụ đã bao gồm thuế giá trị gia tăng.", null, ['align' => 'center', 'italic' => true]);

        $row = $table->addRow();
        $row->addCell(600)->addText("4.2.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Phí dịch vụ thẩm định giá được thanh toán ngay sau khi hợp đồng thẩm định giá có hiệu lực.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Hoặc thanh toán 02 lần:", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Lần 1: Thanh toán số tiền: ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Lần 2: Thanh toán số tiền: ", null, $indentleftNumber);


        $row = $table->addRow();
        $row->addCell(600)->addText("4.3.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Phương thức thanh toán: Tiền mặt hoặc Chuyển khoản (Trên 20 triệu tư vấn khách chuyển khoản).", null, $indentleftNumber);


        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 5:', ['bold' => true, 'underline' => 'single']);
        $textRun->addText(' Quyền và nghĩa vụ của Bên A', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("5.1.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Yêu cầu bên B cung cấp chứng thư thẩm định giá, báo cáo thẩm định giá. Nhận Chứng thư thẩm định giá theo quy định tại điều 1 hợp đồng này.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.2.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Từ chối cung cấp thông tin, tài liệu không liên quan đến hoạt động thẩm định giá, tài sản thẩm định giá.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.3.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Xem xét, quyết định việc sử dụng chứng thư thẩm định giá, báo cáo thẩm định giá.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.4.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Khi bên A yêu cầu bên B cung cấp thêm quyển Chứng thư thì phải trả thêm số tiền phí tương ứng với số lượng bản yêu cầu. ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.5.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên A yêu cầu bên B cử nhân viên làm việc ngoài giờ thì bên A thương lượng trả thêm phí dịch vụ ngoài giờ.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.6.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Cung cấp cho bên B các hồ sơ, tài liệu liên quan đến tài sản thẩm định giá, hướng dẫn Bên B khảo sát hiện trạng tài sản và chịu trách nhiệm về tính hợp pháp, xác thực, đầy đủ của các hồ sơ, tài liệu mà mình cung cấp.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.7.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Phối hợp, tạo điều kiện cho thẩm định viên về giá thực hiện thẩm định giá.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.8.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Thanh toán giá dịch vụ thẩm định giá theo thỏa thuận trong hợp đồng. Trường hợp bên A chưa thanh toán đủ phí dịch vụ cho bên B thì bên B được quyền không phát hành và cung cấp sản phẩm cho bên A.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("5.9.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên A chịu trách nhiệm về việc sử dụng chứng thư thẩm định giá, báo cáo thẩm định giá trong việc quyết định, phê duyệt giá tài sản. Việc sử dụng chứng thư thẩm định giá, báo cáo thẩm định giá phải trong thời gian hiệu lực của chứng thư thẩm định giá, theo đúng mục đích thẩm định giá gắn với đúng tài sản, số lượng tài sản tại hợp đồng thẩm định giá.", null, $indentleftNumber);


        $row = $table->addRow();
        $row->addCell(600)->addText("5.10.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên A sẽ được ưu tiên giảm 20% mức phí tái thẩm định tài sản trong vòng 06 tháng kể từ ngày thẩm định giá lần đầu.", null, $indentleftNumber);

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 6:', ['bold' => true, 'underline' => 'single']);
        $textRun->addText(' Quyền và nghĩa vụ của Bên B', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("6.1.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Yêu cầu bên A cung cấp hồ sơ pháp lý, các tài liệu, dữ liệu của cá nhân và của tài sản để bên B thu thập thông tin và lập hồ sơ thẩm định giá.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.2.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên B và người lao động của bên B sẽ được miễn trừ toàn bộ trách nhiệm đối với kết quả thẩm định giá nếu bên A hoặc người của bên A cung cấp sai lệch hồ sơ pháp lý tài sản, thông tin tài sản không chính xác và không trung thực. ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.3.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên B được miễn trừ hoàn toàn trách nhiệm đối với chứng thư, báo cáo thẩm định giá bên A đã nhận mà chưa thanh toán đủ 100% phí dịch vụ ghi tại đồng này. ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.4.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Từ chối thực hiện thẩm định giá tài sản theo yêu cầu của bên A khi nhận thấy tài sản đó không đủ điều kiện pháp lý để thực hiện việc thẩm định.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.5.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên A đã nhận chứng thư mà bên A không sử dụng, bên B sẽ không hoàn trả chi phí dịch vụ bên A đã thanh toán trước đó.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.6.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Cung cấp cho bên A Chứng thư, báo cáo thẩm định giá sau khi nhận đủ phí dịch vụ quy định trong hợp đồng. ", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.7.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Tuân thủ các quy định của pháp luật trong quá trình thực hiện thẩm định giá tài sản.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.8.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Giảm 20% mức phí nếu bên A tái thẩm định tài sản trong vòng 06 tháng kể từ ngày thẩm định giá lần đầu.", null, $indentleftNumber);

        $row = $table->addRow();
        $row->addCell(600)->addText("6.9.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Các quyền và nghĩa vụ khác theo quy định pháp luật.", null, $indentleftNumber);

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 7: ', ['bold' => true, 'underline' => 'single']);
        $textRun->addText('Chấm dứt hợp đồng', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);


        $row = $table->addRow();
        $row->addCell(600)->addText("7.1.", null, ['align' => 'right']);
        $row->addCell(9300, ['gridSpan' => 2])->addText("Hợp đồng được chấm dứt trong các trường hợp sau:", null, $indentleftNumber);

        $row2 = $table->addRow();
        $row2->addCell(600)->addText("a.", null, ['align' => 'right']);
        $row2->addCell(50)->addText("", null, ['align' => 'right']);
        $row2->addCell(9250)->addText("Hai bên hoàn thành nghĩa vụ theo thỏa thuận trong hợp đồng;", null, $indentleftword);

        $row3 = $table->addRow();
        $row3->addCell(600)->addText("b.", null, ['align' => 'right']);
        $row3->addCell(50)->addText("", null, ['align' => 'right']);
        $row3->addCell(9250)->addText("Hai bên thỏa thuận chấm dứt hợp đồng trước thời hạn:", null, $indentleftword);

        $row4 = $table->addRow();
        $row4->addCell(600)->addText("-", null, ['align' => 'right']);
        $row4->addCell(80)->addText("", null, ['align' => 'right']);
        $row4->addCell(9220)->addText("Trường hợp Bên A muốn chấm dứt hợp đồng trước hạn thì phải thông báo trước cho bên B 01 ngày và phải thanh toán cho bên B tiền phí dịch vụ tương ứng với khối lượng công việc bên B đã thực hiện. Phí này do bên B xác định và không thấp hơn phí đã thanh toán đợt 1.", null, $indentleftSymbol);

        $row5 = $table->addRow();
        $row5->addCell(600)->addText("-", null, ['align' => 'right']);
        $row5->addCell(80)->addText("", null, ['align' => 'right']);
        $row5->addCell(9220)->addText("Trường hợp bên B muốn chấm dứt hợp đồng trước hạn thì phải thông báo trước cho bên A 01 ngày và phải hoàn lại cho bên A toàn bộ số tiền phí dịch vụ mà bên B đã nhận.", null, $indentleftSymbol);

        $row6 = $table->addRow();
        $row6->addCell(600)->addText("c.", null, ['align' => 'right']);
        $row6->addCell(50)->addText("", null, ['align' => 'right']);
        $row6->addCell(9250)->addText("Đơn phương chấm dứt hợp đồng: Trường hợp một trong hai bên vi phạm các điều khoản trong hợp đồng thì bên kia có quyền đơn phương chấm dứt hợp đồng mà không cần báo trước. Bên vi phạm hợp đồng phải bồi thường thiệt hại (nếu có) cho bên kia.", null, $indentleftword);

        $row7 = $table->addRow();
        $row7->addCell(600)->addText("7.2.", null, ['align' => 'right']);
        $row7->addCell(9300, ['gridSpan' => 2])->addText("Hai bên thỏa thuận được quyền đơn phương chấm dứt hợp đồng trong các trường hợp sau đây: ", null, $indentleftNumber);

        $row8 = $table->addRow();
        $row8->addCell(600)->addText("a.", null, ['align' => 'right']);
        $row8->addCell(
            50
        )->addText("", null, ['align' => 'right']);
        $row8->addCell(9250)->addText("Bên A không hướng hướng dẫn bên B thẩm định tài sản hiện trường.", null, $indentleftword);

        $row9 = $table->addRow();
        $row9->addCell(600)->addText("b.", null, ['align' => 'right']);
        $row9->addCell(
            50
        )->addText("", null, ['align' => 'right']);
        $row9->addCell(9250)->addText("Bên A không nhận Chứng thư, báo cáo thẩm định giá.", null, $indentleftword);

        $row9 = $table->addRow();
        $row9->addCell(600)->addText("c.", null, ['align' => 'right']);
        $row9->addCell(
            50
        )->addText("", null, ['align' => 'right']);
        $row9->addCell(9250)->addText("Bên B không cung cấp chứng thư thẩm định giá cho bên A theo thỏa thuận hợp đồng thì bên A yêu cầu bên B phải hoàn tiền đã nhận lại cho bên A và đơn phương chấm dứt hợp đồng với bên B.", null, $indentleftword);

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 8:', ['bold' => true, 'underline' => 'single']);
        $textRun->addText(' Các thỏa thuận khác', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("8.1.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Mọi sửa đổi, bổ sung hợp đồng này phải do hai bên thỏa thuận, được lập thành văn bản và là một phần không tách rời của hợp đồng.", null, $indentleftNumber);


        $row2 = $table->addRow();
        $row2->addCell(600)->addText("8.2.", null, ['align' => 'right']);
        $row2->addCell(9300)->addText("Trong quá trình thực hiện hợp đồng, nếu phát sinh tranh chấp, hai bên sẽ cùng nhau thương lượng trên tinh thần hợp tác. Trường hợp không thương lượng được, vụ việc sẽ do Tòa án nhân dân TP.HCM giải quyết.", null, $indentleftNumber);


        $row3 = $table->addRow();
        $row3->addCell(600)->addText("8.3.", null, ['align' => 'right']);
        $row3->addCell(9300)->addText("Sau khi bên B nhận đủ phí dịch vụ, bên A đã nhận sản phẩm, thì trách nhiệm và nghĩa vụ hai bên chấm dứt, Hợp đồng này mặc nhiên được thanh lý.", null, $indentleftNumber);


        $row4 = $table->addRow();
        $row4->addCell(600)->addText("8.4.", null, ['align' => 'right']);
        $row4->addCell(9300)->addText("Hợp đồng này có hiệu lực kể từ ngày ký, được lập thành 04 bản, có giá trị pháp lý như nhau, bên A giữ 02 bản, bên B giữ 02 bản.", null, $indentleftNumber);

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(4950)->addText("ĐẠI DIỆN BÊN A", ['bold' => true], ['align' => 'center']);
        $row->addCell(4950)->addText("ĐẠI DIỆN BÊN B", ['bold' => true], ['align' => 'center']);

        $textNamePetitioner = mb_strtoupper($certificate->petitioner_name);
        $textNamePetitioner = str_replace(['ÔNG / BÀ ', 'BÀ ', 'ÔNG '], '', $textNamePetitioner);
        $row2 = $table->addRow();
        $row2->addCell(4950)->addText("", ['bold' => true], ['align' => 'center']);
        $row2->addCell(4950)->addText($chucvu, ['bold' => true], ['align' => 'center']);

        $row3 = $table->addRow();
        $row3->addCell(4950)->addText("\n\n\n\n\n");
        $row3->addCell(4950)->addText("\n\n\n\n\n");

        $row5 = $table->addRow();
        $row5->addCell(4950)->addText("\n\n\n\n\n");
        $row5->addCell(4950)->addText("\n\n\n\n\n");

        $row6 = $table->addRow();
        $row6->addCell(4950)->addText("\n\n\n\n\n");
        $row6->addCell(4950)->addText("\n\n\n\n\n");

        $row4 = $table->addRow();
        $row4->addCell(4950)->addText($textNamePetitioner, ['bold' => true], ['align' => 'center']);
        $row4->addCell(4950)->addText(mb_strtoupper($daidien), ['bold' => true], ['align' => 'center']);



        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $table->addCell(9900)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
        $reportUserName = CommonService::getUserReport();
        $reportName = 'HDTDG' . '_' . htmlspecialchars($certificate->petitioner_name);
        $reportName = str_replace(
            ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
            '',
            $reportName
        ); // replace invalid characters with underscore
        $reportName = str_replace(
            ' ',
            '_',
            $reportName
        ); // replace invalid characters with underscore
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
        $data['appraises'] = $check;
        return $data;
    }
}
