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
use Illuminate\Support\Facades\Log;

class GiayYeuCau
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


    //     $phpWord->setDefaultParagraphStyle([
    //         'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(6),
    //         'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(6),
    //         'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5), 'right' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5)),
    //         'space' => [
    //             'line' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(16), 'rule' => 'exact'
    //         ],
    //         'align' => 'both'
    //     ]);
    //     $styleSection = [
    //         'footerHeight' => 300,
    //         'marginTop' => Converter::inchToTwip(.8),
    //         'marginBottom' => Converter::inchToTwip(.8),
    //         'marginRight' => Converter::inchToTwip(.8),
    //         'marginLeft' => Converter::inchToTwip(.8)
    //     ];


    //     $section = $phpWord->addSection($styleSection);

    //     $section->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '13'], ['align' => 'center']);

    //     $section->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true], ['align' => 'center']);
    //     $section->addText(" ", ['size' => '10'], ['align' => 'center']);
    //     $section->addText("GIẤY YÊU CẦU THẨM ĐỊNH GIÁ", ['bold' => true, 'size' => '15'], ['align' => 'center']);

    //     $textRun = $section->addTextRun("alignItemCenter");
    //     $textRun->addText(' ', array('spaceAfter' => 240));
    //     $textRun->addText("Kính gửi", ['underline' => 'single', 'size' => '13'], ['align' => 'center']);
    //     $textRun->addText(": ", ['size' => '13'], ['align' => 'center']);
    //     $textRun->addText("CÔNG TY TNHH THẨM ĐỊNH GIÁ NOVA", ['bold' => true, 'size' => '13'], ['align' => 'center']);
    //     $textRun->addText(' ', array('spaceAfter' => 240));
    //     $section->addText("Địa chỉ: Số 728 – 730 Võ Văn Kiệt, Phường 1, Quận 5, TP. HCM", ['bold' => false, 'size' => '13'], ['align' => 'center']);
    //     $section->addText("Điện thoại: (028) 3920 6779	        Email: thamdinhnova@gmail.com", ['bold' => false, 'size' => '13'], ['align' => 'center']);
    //     $section->addText(" ", ['size' => '10'], ['align' => 'center']);
    //     $stringTimeSoc = "";
    //     $surveyTime = "";
    //     if (isset($certificate->issue_date_card) && !empty(trim($certificate->issue_date_card))) {
    //         $issue_date_card = date_create($certificate->issue_date_card);
    //         $stringTimeSoc =  $issue_date_card->format('d') . "/" . $issue_date_card->format('m') . "/" . $issue_date_card->format('Y');
    //     }
    //     if (isset($certificate->survey_time) && !empty(trim($certificate->survey_time))) {
    //         $survey_time = date_create($certificate->survey_time);
    //         $surveyTime =  'Ngày ' . $survey_time->format('d') . " tháng " . $survey_time->format('m') . " năm " . $survey_time->format('Y') . ' lúc ' . $survey_time->format('H') . ' giờ ' . $survey_time->format('i') . ' phút';
    //     }
    //     // 1
    //     $textRun = $section->addTextRun('Heading2');
    //     $textRun->addText("Thông tin ", ['bold' => false]);
    //     $textRun->addText("BÊN YÊU CẦU ", ['bold' => true]);
    //     $textRun->addText("thẩm định giá: ", ['bold' => false]);
    //     // KHCN
    //     if ($certificate->is_company == 0) {
    //         // $section->addListItem("Họ và tên: " . htmlspecialchars($certificate->petitioner_name) . '  ' . "Số CCCD: " . $certificate->petitioner_identity_card . '  ' . "Ngày cấp: " . $stringTimeSoc, 0, [], 'bullets', []);
    //         // $section->addListItem("Số CCCD: " . $certificate->petitioner_identity_card, 0, [], 'bullets', []);
    //         // $section->addListItem("Ngày cấp: " . $stringTimeSoc . "   ; Nơi cấp: " . ($certificate->issue_place_card ? htmlspecialchars($certificate->issue_place_card) : ""), 0, [], 'bullets', []);
    //         $section->addListItem("Họ và tên: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets', []);
    //         $section->addListItem("Số CCCD: " . $certificate->petitioner_identity_card . '         ' . " Ngày cấp: " . $stringTimeSoc, 0, [], 'bullets', []);
    //         $section->addListItem("Địa chỉ: " . htmlspecialchars($certificate->petitioner_address), 0, [], 'bullets', []);
    //         $section->addListItem("Số điện thoại: " . htmlspecialchars($certificate->petitioner_phone), 0, [], 'bullets', []);
    //     }
    //     // DN
    //     else {
    //         $section->addListItem("Tên Doanh nghiệp: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets', []);
    //         $section->addListItem("Địa chỉ: " . htmlspecialchars($certificate->petitioner_address), 0, [], 'bullets', []);
    //         $section->addListItem("Số điện thoại: " . htmlspecialchars($certificate->petitioner_phone) . '        ' . '- Email: ', 0, [], 'bullets', []);
    //         $section->addListItem("Mã số thuế: ", 0, [], 'bullets', []);
    //         $section->addListItem("Người đại diện:                                      - Chức vụ: ", 0, [], 'bullets', []);
    //     }


    //     //2
    //     $textRun = $section->addTextRun('Heading2');
    //     $textRun->addText("Mục đích: ", ['bold' => false]);
    //     $textRun->addText('Cung cấp các hồ sơ, dữ liệu cá nhân cho Công ty TNHH Thẩm định giá NOVA để lập Hồ sơ Thẩm định giá tài sản.', ['bold' => false]);

    //     //3
    //     $textRun = $section->addTextRun('Heading2');
    //     $textRun->addText("Phương thức nhận văn bản, hồ sơ, tài liệu: ", ['bold' => false]);
    //     $textRun->addText('Nhận qua mạng điện tử.', ['bold' => false]);


    //     //4
    //     $textRun = $section->addTextRun('Heading2');
    //     $textRun->addText("Nội dung: ", ['bold' => false]);
    //     $textRun->addText('Đề nghị Công ty TNHH Thẩm định giá Nova thẩm định giá tài sản như sau: ', ['bold' => false]);

    //     $name_assets = "";
    //     $appraise_law = "";
    //     $number_assets = "01";
    //     $check = "";
    //     $count = 0;
    //     $type1 = 0; //Đất trống
    //     $type2 = 0; //Đất có nhà
    //     $type3 = 0; //Chung cư
    //     // $appraiseAssetType = "Quyền sử dụng đất";
    //     // foreach ($appraises as $realEstate) {
    //     //     if ($realEstate->assetType->description == "ĐẤT TRỐNG") $type1 = 1;
    //     //     if ($realEstate->assetType->description == "ĐẤT CÓ NHÀ") $type2 = 1;
    //     //     if ($realEstate->assetType->description == "CHUNG CƯ") $type3 = 1;
    //     // }
    //     // if ($type1 && $type2 && $type3) {
    //     //     $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc và căn hộ chung cư";
    //     // } else if ($type1 && $type3) {
    //     //     $appraiseAssetType = "Quyền sử dụng đất và căn hộ chung cư";
    //     // } else if (($type1 && $type2) || ($type2)) {
    //     //     $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc";
    //     // } else if ($type3) {
    //     //     $appraiseAssetType = 'Quyền sở hữu căn hộ chung cư';
    //     // }
    //     $isApartment =
    //         in_array('CC', $certificate->document_type ?? []);
    //     if (isset($priceEstimatePrint)) {
    //         foreach ($priceEstimatePrint as $index => $item) {
    //             $name_assets .= ($index == 0 ?  $item->appraise_asset : 'và ' . $item->appraise_asset);
    //             $count += 1;
    //         }
    //     } elseif ($certificate->realEstate && count($certificate->realEstate) > 0) {
    //         if ($isApartment) {
    //             foreach ($certificate->realEstate as $index => $item) {
    //                 if ($item->apartment) {
    //                     $name_assets .= ($index) ? " và " : "";

    //                     $name_assets .= $item->apartment->appraise_asset;
    //                     if ($item->apartment->law) {
    //                         foreach ($item->apartment->law as $index2 => $item2) {
    //                             $appraise_law .= ($index2) ? " và " : "";
    //                             $appraise_law .= "01 Bản Giấy " . $item2->content . " do " . $item2->certifying_agency . " cấp.";
    //                         }
    //                     }
    //                     $count += 1;
    //                 }
    //             }
    //         } else {
    //             foreach ($certificate->realEstate as $index => $item) {
    //                 if ($item->appraises) {

    //                     $name_assets .= ($index) ? " và " : "";

    //                     $name_assets .= $item->appraises->appraise_asset;
    //                     $check = $item->appraises->tangibleAssets;
    //                     if ($item->appraises->appraiseLaw) {
    //                         foreach ($item->appraises->appraiseLaw as $index2 => $item2) {
    //                             $appraise_law .= ($index2) ? " và " : "";
    //                             $appraise_law .= "01 Bản Giấy " . $item2->content . " do " . $item2->certifying_agency . " cấp.";
    //                         }
    //                     }
    //                     $count += 1;
    //                 }
    //             }
    //         }
    //     }
    //     if ($count < 10 && $count > 0) {
    //         $number_assets = '0' . strval($count);
    //     } else {
    //         $number_assets = strval($count);
    //     }
    //     $section->addListItem("Tên tài sản: " . htmlspecialchars($name_assets) . '.', 0, [], 'bullets', []);
    //     $section->addListItem("Số lượng, khối lượng tài sản: " . $number_assets, 0, [], 'bullets', []);

    //     $tableBasicStyle = array(
    //         'borderSize' => 'none',
    //         'cellMargin'  => Converter::inchToTwip(0),
    //     );
    //     // $onlyOneAsset = (count($assets) > 1) ? false : true;
    //     $rowHeader = [
    //         'tblHeader' => true,
    //         'cantSplit' => true
    //     ];
    //     $cantSplit = ['cantSplit' => true];
    //     $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
    //     if ((isset($certificate->apartmentAssetPrint) && count($certificate->apartmentAssetPrint) > 0) ||
    //         (isset($certificate->appraises) && count($certificate->appraises) > 0) ||
    //         (isset($priceEstimatePrint) && count($priceEstimatePrint) > 0) ||
    //         (isset($certificate->realEstate) && count($certificate->realEstate) > 0)
    //     ) {
    //         $table = $section->addTable($styleTable);
    //         $table->addRow(400, $rowHeader);
    //         $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
    //         $table->addCell(4500, $cellVCentered)->addText('Hạng mục', ['bold' => true], $cellHCentered);
    //         $table->addCell(1000, $cellVCentered)->addText('Diện tích', ['bold' => true], $cellHCentered);
    //         $table->addCell(1000, $cellVCentered)->addText('Đơn vị tính', ['bold' => true], $cellHCentered);
    //         $table->addCell(1800, $cellVCentered)->addText('Thông tin tài sản kèm theo', ['bold' => true], $cellHCentered);
    //     }
    //     if (isset($priceEstimatePrint)) {
    //         foreach ($priceEstimatePrint as $stt => $asset) {
    //             $dt = 0;
    //             $table->addRow(400, $cantSplit);
    //             $table->addCell(600, $cellVCentered)->addText($stt + 1, ['bold' => true], array_merge($cellHCentered, $keepNext));
    //             $table->addCell(4500, $cellVJustify)->addText(htmlspecialchars($asset->full_address), ['bold' => true], $cellHJustify);
    //             $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //             $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //             $table->addCell(1800, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //             $dt = isset($asset->total_area) ? $this->formatNumberFunction($asset->total_area, 2, ',', '.') : '';
    //             $table->addRow(400, $cantSplit);
    //             $table->addCell(600, $cellVCentered)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
    //             $table->addCell(4500, $cellVJustify)->addText(htmlspecialchars($asset->appraise_asset), ['bold' => false], $cellHJustify);
    //             $table->addCell(1000, $cellVCentered)->addText($dt, ['bold' => false], $cellHCentered);
    //             $table->addCell(1000, $cellVCentered)->addText($m2, ['bold' => false], $cellHCentered);
    //             $table->addCell(1800, $cellVCentered)->addText('', ['bold' => false], $cellHCentered);
    //         }
    //     } elseif ($certificate->realEstate && count($certificate->realEstate) > 0) {
    //         if ($isApartment) {
    //             foreach ($certificate->realEstate as $stt => $asset) {
    //                 if ($asset->apartment) {
    //                     $dt = 0;
    //                     $table->addRow(400, $cantSplit);
    //                     $table->addCell(600, $cellVCentered)->addText($stt + 1, ['bold' => true], array_merge($cellHCentered, $keepNext));
    //                     $table->addCell(4500, $cellVJustify)->addText(htmlspecialchars($asset->apartment->full_address), ['bold' => true], $cellHJustify);
    //                     $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //                     $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //                     $table->addCell(1800, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //                     $dt = isset($asset->apartment->apartmentAssetProperties) && isset($asset->apartment->apartmentAssetProperties->area) ? $this->formatNumberFunction($asset->apartment->apartmentAssetProperties->area, 2, ',', '.') : '';
    //                     $table->addRow(400, $cantSplit);
    //                     $table->addCell(600, $cellVCentered)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
    //                     $table->addCell(4500, $cellVJustify)->addText(htmlspecialchars($asset->apartment->appraise_asset), ['bold' => false], $cellHJustify);
    //                     $table->addCell(1000, $cellVCentered)->addText($dt, ['bold' => false], $cellHCentered);
    //                     $table->addCell(1000, $cellVCentered)->addText($m2, ['bold' => false], $cellHCentered);
    //                     $table->addCell(1800, $cellVCentered)->addText('', ['bold' => false], $cellHCentered);
    //                 }
    //             }
    //         } else {
    //             foreach ($certificate->realEstate as $stt => $realestate) {
    //                 // Thông tin tài sản
    //                 if ($realestate->appraises) {
    //                     $dt = 0;
    //                     $table->addRow(400, $cantSplit);
    //                     $table->addCell(600, $cellVCentered)->addText($stt + 1, ['bold' => true], array_merge($cellHCentered, $keepNext));
    //                     $table->addCell(4500, $cellVJustify)->addText(htmlspecialchars($realestate->appraises->full_address), ['bold' => true], $cellHJustify);
    //                     $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //                     $table->addCell(1000, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //                     $table->addCell(1800, $cellVCentered)->addText('', ['bold' => true], $cellHCentered);
    //                     if ($realestate->appraises->tangibleAssets) {
    //                         foreach ($realestate->appraises->tangibleAssets as $tangible) {
    //                             $dt = $tangible->total_construction_area ? $this->formatNumberFunction($tangible->total_construction_area, 2, ',', '.') : '';
    //                         }
    //                     }
    //                     $table->addRow(400, $cantSplit);
    //                     $table->addCell(600, $cellVCentered)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
    //                     $table->addCell(4500, $cellVJustify)->addText(htmlspecialchars($realestate->appraises->appraise_asset), ['bold' => false], $cellHJustify);
    //                     $table->addCell(1000, $cellVCentered)->addText($dt, ['bold' => false], $cellHCentered);
    //                     $table->addCell(1000, $cellVCentered)->addText($m2, ['bold' => false], $cellHCentered);
    //                     $table->addCell(1800, $cellVCentered)->addText('', ['bold' => false], $cellHCentered);
    //                 }
    //             }
    //         }
    //     }

    //     $appraise_date = date_create($certificate->appraise_date);
    //     $bien101 = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';

    //     $stringContact = $certificate->name_contact . ($certificate->phone_contact != '' ? ' - ' : '') . $certificate->phone_contact;
    //     $section->addListItem("Mục đích yêu cầu thẩm định giá: " . $bien101 . ".", 0, [], 'bullets', []);
    //     $section->addListItem("Thời điểm thẩm định giá: Tháng "  . date_format($appraise_date, "m/Y") . '.', 0, [], 'bullets', []);
    //     // $section->addListItem("Bên sử dụng kết quả thẩm định giá: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets', []);
    //     $section->addListItem("Bên sử dụng kết quả thẩm định giá: Khách hàng thẩm định giá", 0, [], 'bullets', []);

    //     $section->addListItem("Nguồn gốc tài sản (Nhà nước/ không phải thuộc Nhà nước): Không phải thuộc Nhà nước", 0, [], 'bullets', []);
    //     $section->addListItem("Ngày giờ, địa điểm khảo sát: " . $surveyTime . ($surveyTime != '' ? ' tại địa chỉ ' : '') . $certificate->survey_location, 0, [], 'bullets', []);
    //     $section->addListItem("Tên, điện thoại người liên hệ: " . $stringContact, 0, [], 'bullets', []);
    //     $listItemRun  = $section->addListItemRun(0, 'bullets', []);
    //     $listItemRun->addText("Các hồ sơ, dữ liệu cá nhân, cung cấp gồm: ");
    //     $listItemRun->addText($appraise_law, ['italic' => true]);

    //     $section->addListItem("Số bản chứng thư yêu cầu cấp: 02 bản chính bằng tiếng Việt.", 0, [], 'bullets', []);
    //     $section->addText("    Tôi đồng ý cung cấp các Hồ sơ, Dữ liệu cá nhân như trên cho Công ty TNHH Thẩm định giá Nova, Công ty Nova được phép xử lý các dữ liệu được cung cấp để công ty tiến hành thu thập thông tin, lập hồ sơ Thẩm định giá tài sản phù hợp với mục đích được yêu cầu tại văn bản này.", []);
    //     $section->addText("    Tôi cam kết thanh toán đủ phí dịch vụ theo quy định Công ty TNHH thẩm định giá Nova.", []);



    //     $section->addTextBreak(null, null, $keepNext);

    //     $table3 = $section->addTable($tableBasicStyle);
    //     $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
    //     $table3->addCell(Converter::inchToTwip(4))->addText("", null,  $keepNext);;
    //     $table3->addCell(Converter::inchToTwip(4))->addText("TP.HCM, ngày        tháng         năm   ", null,  $keepNext);

    //     $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
    //     $cell31 = $table3->addCell(Converter::inchToTwip(4));
    //     $cell31->addText("ĐƠN VỊ NHẬN YÊU CẦU", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
    //     $cell32 = $table3->addCell(Converter::inchToTwip(4));
    //     $cell32->addText("NGƯỜI YÊU CẦU", ['bold' => true], ['align' => 'center', 'keepNext' => true]);

    //     //Footer
    //     // $comName =  !empty($company->acronym) ? mb_strtoupper($company->acronym) : mb_strtoupper($company->name);
    //     // $createdName =  isset($certificate->createdBy) ? CommonService::withoutAccents($certificate->createdBy->name) : '';
    //     // if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
    //     //     $yearCVD = Carbon::createFromFormat('Y-m-d',  $certificate->document_date)->format('Y');
    //     // } else {
    //     //     $yearCVD = "        ";
    //     // }
    //     // $reportID = 'HSTD_' . $certificate->id;
    //     $footer = $section->addFooter();
    //     $table = $footer->addTable();
    //     $table->addRow();
    //     $cell = $table->addCell(4500);
    //     $textrun = $cell->addTextRun();
    //     // $textrun->addText($comName  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID, array('size' => 8), array('align' => 'left'));
    //     $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right'));

    //     $reportUserName = CommonService::getUserReport();
    //     $reportName = 'GYC' . '_' . htmlspecialchars($certificate->petitioner_name);
    //     $reportName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $reportName); // replace invalid characters with underscore
    //     $reportName = str_replace(' ', '_', $reportName); // replace invalid characters with underscore
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
        $indentFistLine = ['indentation' => ['firstLine' => 360]];
        $indentPara = ['indentation' => ['left' => 360]];
        $indentPara2 = ['indentation' => ['left' => 400]];
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
            'marginLeft' => Converter::inchToTwip(.8)
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
        if ($certificate->is_company == 0) {
            $section->addText(" ", ['size' => '10'], ['align' => 'center']);
        }
        $stringTimeSoc = "";
        $surveyTime = "";
        if (isset($certificate->issue_date_card) && !empty(trim($certificate->issue_date_card))) {
            $issue_date_card = date_create($certificate->issue_date_card);
            $stringTimeSoc =  $issue_date_card->format('d') . "/" . $issue_date_card->format('m') . "/" . $issue_date_card->format('Y');
        }
        if (isset($certificate->survey_time) && !empty(trim($certificate->survey_time))) {
            $survey_time = date_create($certificate->survey_time);
            $surveyTime = $survey_time->format('H') . ' giờ ' . $survey_time->format('i') . ' phút, ' . 'ngày ' . $survey_time->format('d') . " tháng " . $survey_time->format('m') . " năm " . $survey_time->format('Y');
        }
        // 1
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Thông tin ", ['bold' => false]);
        $textRun->addText("BÊN YÊU CẦU ", ['bold' => true]);
        $textRun->addText("thẩm định giá: ", ['bold' => false]);
        // KHCN
        if ($certificate->is_company == 0) {
            // $section->addListItem("Họ và tên: " . htmlspecialchars($certificate->petitioner_name) . '  ' . "Số CCCD: " . $certificate->petitioner_identity_card . '  ' . "Ngày cấp: " . $stringTimeSoc, 0, [], 'bullets', []);
            // $section->addListItem("Số CCCD: " . $certificate->petitioner_identity_card, 0, [], 'bullets', []);
            // $section->addListItem("Ngày cấp: " . $stringTimeSoc . "   ; Nơi cấp: " . ($certificate->issue_place_card ? htmlspecialchars($certificate->issue_place_card) : ""), 0, [], 'bullets', []);
            $section->addListItem("Họ và tên: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets', []);
            $section->addListItem("Số CCCD: " . $certificate->petitioner_identity_card . '         ' . " Ngày cấp: " . $stringTimeSoc, 0, [], 'bullets', []);
            $section->addListItem("Địa chỉ: " . htmlspecialchars($certificate->petitioner_address), 0, [], 'bullets', []);
            $section->addListItem("Số điện thoại: " . htmlspecialchars($certificate->petitioner_phone), 0, [], 'bullets', []);
        }
        // DN
        else {
            $section->addListItem("Tên Doanh nghiệp: " . htmlspecialchars($certificate->petitioner_name), 0, [], 'bullets', []);
            $section->addListItem("Địa chỉ: " . htmlspecialchars($certificate->petitioner_address), 0, [], 'bullets', []);
            $section->addListItem("Số điện thoại: " . htmlspecialchars($certificate->petitioner_phone) . '        ' . '- Email: ', 0, [], 'bullets', []);
            $section->addListItem("Mã số thuế: ", 0, [], 'bullets', []);
            $section->addListItem("Người đại diện:                                      - Chức vụ: ", 0, [], 'bullets', []);
        }


        //2
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Nội dung yêu cầu: ", ['bold' => false]);
        $textRun->addText("BÊN YÊU CẦU ", ['bold' => true]);
        $textRun->addText("đề nghị ", ['bold' => false]);
        $textRun->addText("Công ty TNHH Thẩm định giá Nova ", ['bold' => true]);
        $textRun->addText('thẩm định giá tài sản sau:', ['bold' => false]);

        //2.1
        $textRun = $section->addTextRun('Heading3');
        $textRun->addText("Tên tài sản: Quyền sử dụng đất và tài sản trên đất. Số lượng, khối lượng tài sản yêu cầu thẩm định giá:", ['bold' => false]);
        $isApartment =  in_array('CC', $certificate->document_type ?? []);
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
                        $stt = "0" + ($index + 1);
                    } else {
                        $stt = ($index + 1);
                    }

                    if ($item->apartment) {
                        if ($item->apartment->law) {
                            foreach ($item->apartment->law as $index2 => $item2) {
                                $appraise_law .= ($index2) ? " và " : "";
                                $appraise_law .= "01 bản photo Giấy " . $item2->content . " do " . $item2->certifying_agency . " cấp.";
                            }
                        }
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
                        $stt = "0" + ($index + 1);
                    } else {
                        $stt = ($index + 1);
                    }

                    if ($item->appraises) {
                        if ($item->appraises->appraiseLaw) {
                            foreach ($item->appraises->appraiseLaw as $index2 => $item2) {
                                $appraise_law .= ($index2) ? " và " : "";
                                $appraise_law .= "01 bản photo Giấy " . $item2->content . " do " . $item2->certifying_agency . " cấp.";
                            }
                        }
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


        $tableBasicStyle = array(
            'borderSize' => 'none',
            'cellMargin'  => Converter::inchToTwip(0),
        );
        // $onlyOneAsset = (count($assets) > 1) ? false : true;
        $rowHeader = [
            'tblHeader' => true,
            'cantSplit' => false
        ];
        $cantSplit = ['cantSplit' => true];
        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        if (!empty($arrayTable)) {
            $table = $section->addTable($styleTable);
            $table->addRow(400, $rowHeader);
            $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered));
            $table->addCell(4500, $cellVCentered)->addText('Hạng mục', ['bold' => true], $cellHCentered);
            $table->addCell(1800, $cellVCentered)->addText('Diện tích (' . $m2 . ')', ['bold' => true], $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText('Hiện trạng', ['bold' => true], $cellHCentered);
            foreach ($arrayTable as $index => $item) {
                $table->addRow(400, false);
                $table->addCell(600, $cellVCentered)->addText($item[0], ['bold' => false], array_merge($cellHCentered));
                $table->addCell(4500, $cellVJustify)->addText($item[1], ['bold' => false], $cellHJustify);
                $table->addCell(1800, $cellVCentered)->addText($item[2], ['bold' => false], $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText($item[3], ['bold' => false], $cellHCentered);
            }
        }

        $appraise_date = date_create($certificate->appraise_date);
        $bien101 = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';

        $stringContact = $certificate->name_contact . ($certificate->phone_contact != '' ? ' - ' : '') . $certificate->phone_contact;
        // 2.2
        $textRun = $section->addTextRun('Heading3');
        $textRun->addText("Mục đích thẩm định giá: " . $bien101 . ".", ['bold' => false]);
        // 2.3
        $textRun = $section->addTextRun('Heading3');
        $textRun->addText("Thời điểm thẩm định giá: Tháng " . date_format($appraise_date, "m/Y") . ".", ['bold' => false]);
        // 2.4
        $textRun = $section->addTextRun('Heading3');
        $textRun->addText("Bên sử dụng Chứng thư, Báo cáo thẩm định giá: " . htmlspecialchars($certificate->petitioner_name), ['bold' => false]);
        $textRun = $section->addTextRun('Heading3');
        $textRun->addText("Các Hồ sơ, tài liệu, dữ liệu cá nhân ", ['bold' => false]);
        $textRun->addText("BÊN YÊU CẦU ", ['bold' => true]);
        $textRun->addText("cung cấp cho Công ty TNHH Thẩm định giá Nova để Công ty lập Hồ sơ Thẩm định giá tài sản gồm: ", ['bold' => false]);
        $textRun->addText($appraise_law, ['italic' => true]);
        // $listItemRun  = $section->addListItemRun(1, 'bullets', []);
        // $listItemRun->addText("Phương thức, địa điểm giao nhận hồ sơ: Email/phần mềm/tên công cụ mạng xã hội hoặc nhận hồ sơ trực tiếp tại địa chỉ " . htmlspecialchars($certificate->petitioner_address));
        // $listItemRun  = $section->addListItemRun(1, 'bullets', []);
        // $listItemRun->addText("Họ tên, số điện thoại người cung cấp hồ sơ: ");
        // $listItemRun  = $section->addListItemRun(1, 'bullets', []);
        // $listItemRun->addText("Họ tên, số điện thoại người nhận hồ sơ: ");
        $section->addListItem("Phương thức, địa điểm giao nhận hồ sơ: Email/phần mềm/tên công cụ mạng xã hội hoặc nhận hồ sơ trực tiếp tại văn phòng của công ty TNHH Thẩm định giá Nova. ", 0, [], 'bullets', $indentPara2);
        $section->addListItem("Họ tên, số điện thoại người cung cấp hồ sơ: ", 0, [], 'bullets', $indentPara2);
        $section->addListItem("Họ tên, số điện thoại người nhận hồ sơ: ", 0, [], 'bullets', $indentPara2);

        // 3
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Nguồn gốc tài sản (Nhà nước/ không phải thuộc Nhà nước): Không phải thuộc Nhà nước.");
        // 4
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Thời gian địa điểm và thông tin người liên hệ khảo sát hiện trạng tài sản: ");
        $section->addListItem("Thời gian, địa điểm khảo sát hiện trạng tài sản: dự kiến lúc " . $surveyTime . ($surveyTime != '' ? ' tại ' : '') . $certificate->survey_location, 0, [], 'bullets', $indentPara);
        $section->addListItem("Họ tên, số điện thoại người hướng dẫn: " .  $certificate->name_contact . ', điện thoại: ' . $certificate->phone_contact, 0, [], 'bullets', []);
        // 5
        $textRun = $section->addTextRun('Heading2');
        $textRun->addText("Các nội dung yêu cầu và thống nhất:");
        $section->addListItem(($certificate->is_company == 0 ? 'Sản phẩm yêu cầu: Chứng thư và Báo cáo Thẩm định giá kèm theo cơ sở giá trị thẩm định giá; hạn chế và loại trừ trách nhiệm.' : 'Sản phẩm: Chứng thư, Báo cáo thẩm định giá.'), 0, [], 'bullets', []);
        $section->addListItem("Số lượng: 02 bản chính bằng tiếng Việt.", 0, [], 'bullets', []);

        // 
        $textRun = $section->addTextRun();
        $textRun->addText("   BÊN YÊU CẦU ", ['bold' => true]);
        $textRun->addText("đồng ý cung cấp các Hồ sơ, tài liệu, dữ liệu như trên cho ", ['bold' => false]);
        $textRun->addText("Công ty TNHH Thẩm định giá Nova", ['bold' => true]);
        $textRun->addText(", Công ty được phép sử dụng tất cả các Hồ sơ, tài liệu, dữ liệu được cung cấp để Công ty Nova tiến hành thu thập thông tin, lập hồ sơ Thẩm định giá tài sản phù hợp với mục đích được yêu cầu tại văn bản này. ", ['bold' => false]);
        $textRun->addText("BÊN YÊU CẦU ", ['bold' => true]);
        $textRun->addText("đã được thông báo, trao đổi và thống nhất các giả thiết, giả thiết đặc biệt của tài sản, cơ sở giá trị thẩm định giá; các hạn chế và loại trừ trách nhiệm (nếu có) trong hồ sơ thẩm định giá.", ['bold' => false]);
        // 
        $textRun = $section->addTextRun();
        $textRun->addText("   BÊN YÊU CẦU ", ['bold' => true]);
        $textRun->addText("cam kết thanh toán đủ phí dịch vụ cho ", ['bold' => false]);
        $textRun->addText("Công ty TNHH Thẩm định giá Nova", ['bold' => true]);


        $section->addTextBreak(null, null, null);

        $table3 = $section->addTable($tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), null);
        $table3->addCell(Converter::inchToTwip(4))->addText("", null,  null);;
        $table3->addCell(Converter::inchToTwip(4))->addText("TP.HCM, ngày        tháng         năm " . Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Y'), null,  $keepNext);

        $table3->addRow(Converter::inchToTwip(.1), null);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("ĐƠN VỊ NHẬN YÊU CẦU", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        $cell32->addText("NGƯỜI YÊU CẦU", ['bold' => true], ['align' => 'center', 'keepNext' => true]);

        $table3->addRow(Converter::inchToTwip(.1), null);
        $cell33 = $table3->addCell(Converter::inchToTwip(4));
        $cell33->addText("Công ty TNHH Thẩm Định Giá Nova", ['bold' => false], ['align' => 'center', 'keepNext' => true]);
        $cell34 = $table3->addCell(Converter::inchToTwip(4));
        $cell34->addText(htmlspecialchars($certificate->petitioner_name), ['bold' => false], ['align' => 'center', 'keepNext' => true]);

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
        $reportName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $reportName); // replace invalid characters with underscore
        $reportName = str_replace(' ', '_', $reportName); // replace invalid characters with underscore
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
        $data['appraises'] = $checktangibleAsset;
        return $data;
    }
}
