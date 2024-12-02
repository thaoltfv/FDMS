<?php

namespace App\Services\Document\CertificateAsset;

use App\Enum\EstimateAssetDefault;
use App\Http\ResponseTrait;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class PhuLuc1
{
    use ResponseTrait;

    /**
     * @throws Exception
     * @throws \Exception
     */
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

        $phpWord->addTitleStyle(
            1,
            array('size' => '13', 'bold' => true, 'allCaps' => true, 'spaceBefore' => 100),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 0, 'spaceBefore' => 300, 'spaceAfter' => 100)
        );
        $phpWord->addTitleStyle(
            2,
            array('size' => '13', 'bold' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 1, 'spaceBefore' => 200, 'spaceAfter' => 100)
        );
        $phpWord->addTitleStyle(
            3,
            array('size' => '13', 'bold' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 2, 'spaceBefore' => 150, 'spaceAfter' => 100)
        );

        $phpWord->addParagraphStyle(
            'leftTab',
            array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('left', 5000)))
        );
    }
    public function generateDocx($certificate, $company, $assets, $format): array
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5), 'right' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5)),
            'space' => [
                'line' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(15), 'rule' => 'exact'
            ]
        ]);
        $phpWord->addParagraphStyle(
            'rightTab',
            array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('left', 4500)))
        );
        $this->setFormat($phpWord);
        $styleTable = [
            'borderSize' => 1,
            'align' => JcTable::START,
            'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
        ];

        $styleTableHide = [
            'align' => JcTable::START
        ];

        $styleTableImage = [
            'align' => JcTable::CENTER
        ];
        $styleImageLogo = [
            'width' => 54,
            'height' => 33,
            'align' => 'left',
            'wrappingStyle' => 'behind',
            'positioning' => 'absolute'
        ];
        $styleSection = [
            'footerHeight' => 300,
            'marginTop' => 600,
            'marginBottom' => 600,
            'marginRight' => 1000,
            'marginLeft' => 1000
        ];
        $rowHeader = array('tblHeader' => true, 'cantSplit' => true);
        $keepNext = ['keepNext' => true];
        $cantSplit = ['cantSplit' => true];

        //$m2 = 'm</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/></w:rPr><w:t xml:space="preserve">';
        $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';

        $columnWidthFirst = 1900;
        $columnWidthSecond = 1900;
        $columnWidthThird = 3300;
        $columnWidthFourth = 5000;

        $rowHeader = array('tblHeader' => true);

        $factors = EstimateAssetDefault::COMPARATION_FACTORS;

        $section = $phpWord->addSection($styleSection);

        $imgName = env('STORAGE_IMAGES','images') .'/'.'company_logo.png';

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

        $section->addImage(storage_path('app/public/'.$imgName), $styleImageLogo);
        $section->addText('PHỤ LỤC 1 KÈM THEO BÁO CÁO THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);
        $section->addText('QUYỀN SỬ DỤNG ĐẤT', ['italic' => true, 'size' => 14], ['align' => 'center']);
        $onlyOneAsset = (count($assets) > 1) ? false : true;
        foreach ($assets as $key => $asset) {
            if (($key + 1) > 1) {
                $section->addPageBreak();
            }
            $asset1 = $asset->assetGeneral[0] ?? null;
            $asset2 = $asset->assetGeneral[1] ?? null;
            $asset3 = $asset->assetGeneral[2] ?? null;

            $detail1 = $asset1->properties[0] ?? null;
            $detail2 = $asset2->properties[0] ?? null;
            $detail3 = $asset3->properties[0] ?? null;

            foreach ($asset->appraiseHasAssets as $appraiseHasAssets) {
                if ($appraiseHasAssets->asset_general_id == $asset1->id) {
                    foreach ($asset1->properties as $properties) {
                        if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                            $detail1 = $properties;
                        }
                    }
                }
                if ($appraiseHasAssets->asset_general_id == $asset2->id) {
                    foreach ($asset2->properties as $properties) {
                        if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                            $detail2 = $properties;
                        }
                    }
                }
                if ($appraiseHasAssets->asset_general_id == $asset3->id) {
                    foreach ($asset3->properties as $properties) {
                        if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                            $detail3 = $properties;
                        }
                    }
                }
            }
            $result = null;
            foreach ($asset->pic as $value) {
                if (isset($value->picType)) {
                    $result[$value->picType->description][] = $value;
                }
            }

            $comparisonFactor1 = [];
            $comparisonFactor2 = [];
            $comparisonFactor3 = [];
            $otherFactor1 = [];
            $otherFactor2 = [];
            $otherFactor3 = [];
            foreach ($asset->comparisonFactor as $comparisonFactor) {
                if ($comparisonFactor->type != 'yeu_to_khac') {
                    if ($asset1 && ($comparisonFactor->asset_general_id == $asset1->id)) {
                        $comparisonFactor1[$comparisonFactor->type] = $comparisonFactor;
                    }
                    if ($asset2 && ($comparisonFactor->asset_general_id == $asset2->id)) {
                        $comparisonFactor2[$comparisonFactor->type] = $comparisonFactor;
                    }
                    if ($asset3 && ($comparisonFactor->asset_general_id == $asset3->id)) {
                        $comparisonFactor3[$comparisonFactor->type] = $comparisonFactor;
                    }
                } else {
                    if ($asset1 && ($comparisonFactor->asset_general_id == $asset1->id)) {
                        $otherFactor1[] = $comparisonFactor;
                    }
                    if ($asset2 && ($comparisonFactor->asset_general_id == $asset2->id)) {
                        $otherFactor2[] = $comparisonFactor;
                    }
                    if ($asset3 && ($comparisonFactor->asset_general_id == $asset3->id)) {
                        $otherFactor3[] = $comparisonFactor;
                    }
                }
            }


            if ($onlyOneAsset) {
                $textRun = $section->addTextRun(['align' => 'both']);
                $textRun->addText('     ' . ($key + 1) . '. Tài sản thẩm định: ', ['bold' => true, 'size' => 14]);
            } else {
                $textRun = $section->addTextRun('Heading2');
                $textRun->addText('Tài sản thẩm định ' . ($key + 1) . ': ', ['bold' => true, 'size' => 14]);
            }
            $textRun->addText($asset->appraise_asset ?? '', ['size' => 13, 'bold' => false]);

            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('           Qua khảo sát hiện trạng thực tế tại khu vực thẩm định giá và tham khảo thông tin từ thị trường, ' . mb_strtoupper($company->acronym) . ' nhận thấy có ');
            $textRun->addText(count($asset->assetGeneral), ['bold' => true]);
            $textRun->addText(' tài sản so sánh có các yếu tố tương đồng nhất với tài sản thẩm định, và sử dụng làm cơ sở điều chỉnh để tiến hành xác định giá trị tài sản thẩm định, cụ thể như sau: ');
            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('✓ ', ['bold' => false]);
            $textRun->addText('Sơ đồ vị trí TSTĐG và các TSSS.', ['bold' => true]);

            if (isset($result['HÌNH BẢN ĐỒ'][0]->link)) {
                $phpWord->addTableStyle('Colspan Rowspan', $styleTableImage);
                $table = $section->addTable($styleTableImage);
                $table->addRow(800, ['align' => 'center']);
                $cell = $table->addCell(10000, ['align' => 'center']);
                // if(($data = @file_get_contents($result['HÌNH BẢN ĐỒ'][0]->link)) !== false) {
                try {
                    $cell->addImage(isset($result['HÌNH BẢN ĐỒ'][0]->link) ? $result['HÌNH BẢN ĐỒ'][0]->link : '', [
                        'width' => 480,
                        'height' => 185,
                        'align' => 'center',
                        'wrappingStyle' => 'square',
                        'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                        'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
                        'posVertical'    => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                    ]);
                } catch (Exception $e) {
                }
            }

            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('✓ ', ['bold' => false]);
            $textRun->addText('Thu thập thông tin TSTĐG và các TSSS ', ['bold' => true]);

            $section->addText('BẢNG TỔNG HỢP THÔNG TIN TSTĐG VÀ TSSS', null, ['align' => 'center']);
            $stt = 1;
            $label = [
                "phap_ly" => "Pháp lý",
                "quy_mo" => "Quy mô",
                "chieu_rong_mat_tien" => "Chiều rộng mặt tiền",
                "chieu_sau_khu_dat" => "Chiều sâu khu đất",
                "hinh_dang_dat" => "Hình dáng đất",
                "giao_thong" => "Giao thông",
                "ket_cau_duong" => "Kết cấu đường",
                "do_rong_duong" => "Bề rộng đường",
                "dieu_kien_ha_tang" => "Điều kiện hạ tầng",
                "kinh_doanh" => "Kinh doanh",
                "an_ninh_moi_truong_song" => "An ninh, môi trường sống",
                "phong_thuy" => "Phong thủy",
                "quy_hoach" => "Quy hoạch/hiện trạng",
                "dieu_kien_thanh_toan" => "Điều kiện thanh toán",
                'yeu_to_khac' => 'Yếu tố khác',
            ];
            $checked = [
                "phap_ly" => 0,
                "quy_mo" => 0,
                "chieu_rong_mat_tien" => 0,
                "chieu_sau_khu_dat" => 0,
                "hinh_dang_dat" => 0,
                "giao_thong" => 0,
                "ket_cau_duong" => 0,
                "do_rong_duong" => 0,
                "dieu_kien_ha_tang" => 0,
                "kinh_doanh" => 0,
                "an_ninh_moi_truong_song" => 0,
                "phong_thuy" => 0,
                "quy_hoach" => 0,
                "dieu_kien_thanh_toan" => 0,
                "yeu_to_khac" => 0
            ];
            if (isset($asset->assetGeneral)) {
                foreach ($asset->assetGeneral as $index => $assetGeneral) {
                    foreach ($asset->comparisonFactor as $comparisonFactor) {
                        if (($comparisonFactor->appraise_id == $asset->id)
                            && ($comparisonFactor->asset_general_id == $assetGeneral->id)
                            && (isset($label[$comparisonFactor->type]))
                        ) {
                            if (($checked[$comparisonFactor->type]) || (!$comparisonFactor->status)) continue;
                            $checked[$comparisonFactor->type]++;
                        }
                    }
                    if ($index) continue;
                }
            }

            $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
            $cellRowContinue = array('vMerge' => 'continue');
            $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
            $cellHCentered = array('align' => 'center');
            $cellVCentered = array('valign' => 'center');
            $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
            $table = $section->addTable($styleTable);
            $table->addRow(400, $rowHeader);
            $table->addCell(600, $cellRowSpan)->addText('TT', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Chỉ tiêu', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(' TSTĐ', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS1', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS2', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS3', ['bold' => true], $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Nguồn tin thu thập', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('Chưa biết ', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(($asset1->contact_person ?? '') . "\n" . ($asset1->contact_phone ?? ''), ['bold' => false, 'italic' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(($asset2->contact_person ?? '') . "\n" . ($asset2->contact_phone ?? ''), ['bold' => false, 'italic' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(($asset3->contact_person ?? '') . "\n" . ($asset3->contact_phone ?? ''), ['bold' => false, 'italic' => true], $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowContinue)->addText('', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Hình thức thu thập', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset1) ? $asset1 : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset2) ? $asset2 : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset3) ? $asset3 : '-', ['bold' => false], $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowContinue)->addText('', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Ghi chú', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset1) ? $asset1 : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset2) ? $asset2 : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset3) ? $asset3 : '-', ['bold' => false], $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Loại giao dịch', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset1->transactionType) ? CommonService::mbUcfirst($asset1->transactionType->description) : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset2->transactionType) ? CommonService::mbUcfirst($asset2->transactionType->description) : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset3->transactionType) ? CommonService::mbUcfirst($asset3->transactionType->description) : '-', ['bold' => false], $cellHCentered);

            $publicDate1 = date_create($asset1->public_date);
            $publicDate2 = date_create($asset2->public_date);
            $publicDate3 = date_create($asset3->public_date);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowContinue)->addText('', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Thời điểm giao dịch', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset1->transactionType) ? 'Tháng ' . date_format($publicDate1, 'm/Y') : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset2->transactionType) ? 'Tháng ' . date_format($publicDate2, 'm/Y') : '-', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset3->transactionType) ? 'Tháng ' . date_format($publicDate3, 'm/Y') : '-', ['bold' => false], $cellHCentered);
            
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Tọa độ', ['bold' => false], $cellHCentered);

            $coordinates = explode(',', ($asset->coordinates ?? ','));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('X: ' . isset($coordinates[0]) ? $coordinates[0] : '', null, ['align' => JcTable::CENTER]);
            $cell->addText('Y: ' . isset($coordinates[1]) ? $coordinates[1] : '', null, ['align' => JcTable::CENTER]);

            $coordinates = explode(',', ($asset1->coordinates ?? ','));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('X: ' . isset($coordinates[0]) ? $coordinates[0] : '', null, ['align' => JcTable::CENTER]);
            $cell->addText('Y: ' . isset($coordinates[1]) ? $coordinates[1] : '', null, ['align' => JcTable::CENTER]);

            $coordinates = explode(',', ($asset2->coordinates ?? ','));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('X: ' . isset($coordinates[0]) ? $coordinates[0] : '', null, ['align' => JcTable::CENTER]);
            $cell->addText('Y: ' . isset($coordinates[1]) ? $coordinates[1] : '', null, ['align' => JcTable::CENTER]);

            $coordinates = explode(',', ($asset3->coordinates ?? ','));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('X: ' . isset($coordinates[0]) ? $coordinates[0] : '', null, ['align' => JcTable::CENTER]);
            $cell->addText('Y: ' . isset($coordinates[1]) ? $coordinates[1] : '', null, ['align' => JcTable::CENTER]);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Địa chỉ thửa đất', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);

            $address = $asset->ward->name . ', ' . $asset->district->name . ', ' . $asset->province->name;
            $landNo1 = isset($asset1->properties[0]->compare_property_doc[0]->plot_num) ? 'Thửa số: '. $asset1->properties[0]->compare_property_doc[0]->plot_num .', ' : '';
            $docNo1 = isset($asset1->properties[0]->compare_property_doc[0]->doc_num) ? 'tờ: '. $asset1->properties[0]->compare_property_doc[0]->doc_num . ', ' : '';
            $address1 =$landNo1 . $docNo1 . $asset1->ward->name . ', ' . $asset1->district->name . ', ' . $asset1->province->name;
            $landNo2 = isset($asset2->properties[0]->compare_property_doc[0]->plot_num) ? 'Thửa số: '. $asset2->properties[0]->compare_property_doc[0]->plot_num .', ' : '';
            $docNo2 = isset($asset2->properties[0]->compare_property_doc[0]->doc_num) ? 'tờ: '. $asset2->properties[0]->compare_property_doc[0]->doc_num . ', ' : '';
            $address2 =$landNo2 . $docNo2 . $asset2->ward->name . ', ' . $asset2->district->name . ', ' . $asset2->province->name;
            $landNo3 = isset($asset3->properties[0]->compare_property_doc[0]->plot_num) ? 'Thửa số: '. $asset3->properties[0]->compare_property_doc[0]->plot_num .', ' : '';
            $docNo3 = isset($asset3->properties[0]->compare_property_doc[0]->doc_num) ? 'tờ: '. $asset3->properties[0]->compare_property_doc[0]->doc_num . ', ' : '';
            $address3 =$landNo3 . $docNo3 . $asset3->ward->name . ', ' . $asset3->district->name . ', ' . $asset3->province->name;
            $cell->addText(CommonService::mbCaseTitle($address), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(CommonService::mbCaseTitle($address1), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(CommonService::mbCaseTitle($address2), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(CommonService::mbCaseTitle($address3), null, ['align' => JcTable::CENTER]);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Pháp lý', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset->properties[0]->legal->description) ? CommonService::mbUcfirst($asset->properties[0]->legal->description) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($detail1->legal->description) ? CommonService::mbUcfirst($detail1->legal->description) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($detail2->legal->description) ? CommonService::mbUcfirst($detail2->legal->description) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($detail3->legal->description) ? CommonService::mbUcfirst($detail3->legal->description) : '-', null, ['align' => JcTable::CENTER]);

            //if($checked["quy_mo"]) {
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], array_merge($cellHCentered, $keepNext));
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Quy mô (Tổng diện tích)', ['bold' => false], array_merge($cellHCentered, $keepNext));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);

            $cell->addText((isset($asset->properties[0]->appraise_land_sum_area) ?  number_format($asset->properties[0]->appraise_land_sum_area, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);

            $cell->addText((isset($detail1->asset_general_land_sum_area) ? number_format($detail1->asset_general_land_sum_area, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail2->asset_general_land_sum_area) ? number_format($detail2->asset_general_land_sum_area, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail3->asset_general_land_sum_area) ? number_format($detail3->asset_general_land_sum_area, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $acronymTranferFacility = null;
            $landType = null;

            $landType1 = null;
            $landType2 = null;
            $landType3 = null;

            $assetViolateAreas = [];
            $assetViolateAreasTotal = [];
            $assetAreaByLands = [];
            $assetViolateAreaByLands = [];
            $assetUnitPrices = [];
            $assetUnitPriceByLands = [];
            if (isset($asset->assetUnitPrice)) {
                foreach ($asset->assetUnitPrice as $item) {
                    if ($item->update == 2) {
                        $assetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id] = $item->update_value;
                    } else {
                        $assetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id] = $item->original_value;
                    }

                    $assetUnitPriceByLands[$item->position_type_id][$item->land_type_id] = $item->landTypeData->acronym;
                }
            }
            if (isset($asset->assetUnitArea)) {
                foreach ($asset->assetUnitArea as $item) {
                    $assetViolateAreas[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = $item->violation_asset_area;
                    if (isset($assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id])) {
                        $assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id] += $item->violation_asset_area;
                    } else {
                        $assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id] = $item->violation_asset_area;
                    }
                }
            }

            if (isset($detail1->propertyDetail)) {
                foreach ($detail1->propertyDetail as $propertyDetail1) {
                    $assetAreaByLands[$propertyDetail1->land_type_purpose] = $propertyDetail1->landTypePurposeData->acronym;
                    $assetViolateAreaByLands[$propertyDetail1->land_type_purpose] = $propertyDetail1->landTypePurposeData->acronym;
                }
            }
            if (isset($detail2->propertyDetail)) {
                foreach ($detail2->propertyDetail as $propertyDetail2) {
                    $assetAreaByLands[$propertyDetail2->land_type_purpose] = $propertyDetail2->landTypePurposeData->acronym;
                    $assetViolateAreaByLands[$propertyDetail2->land_type_purpose] = $propertyDetail2->landTypePurposeData->acronym;
                }
            }
            if (isset($detail3->propertyDetail)) {
                foreach ($detail3->propertyDetail as $propertyDetail3) {
                    $assetAreaByLands[$propertyDetail3->land_type_purpose] = $propertyDetail3->landTypePurposeData->acronym;
                    $assetViolateAreaByLands[$propertyDetail3->land_type_purpose] = $propertyDetail3->landTypePurposeData->acronym;
                }
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText(($stt - 1) . '.1', ['bold' => false], array_merge($cellHCentered, $keepNext));
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Phù hợp QH', ['bold' => false], array_merge($cellHCentered, $keepNext));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetNotViolateAreaTotal = isset($asset->properties[0]->appraise_land_sum_area) ? $asset->properties[0]->appraise_land_sum_area : 0;
            $assetViolateAreaTotal = 0;
            $violateAreasAsset = [];
            if (isset($asset->properties[0]->propertyDetail)) {
                foreach ($asset->properties[0]->propertyDetail as $item) {
                    $assetViolateAreaTotal += $item->planning_area;
                    if (isset($violateAreasAsset[$item->landTypePurpose->acronym])) {
                        $violateAreasAsset[$item->landTypePurpose->acronym] += $item->planning_area;
                    } else {
                        $violateAreasAsset[$item->landTypePurpose->acronym] = $item->planning_area;
                    }
                }
            }
            $assetNotViolateAreaTotal -= $assetViolateAreaTotal;
            $cell->addText((isset($assetNotViolateAreaTotal) ?  number_format($assetNotViolateAreaTotal, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetNotViolateAreaTotal1 = isset($detail1->asset_general_land_sum_area) ? $detail1->asset_general_land_sum_area : 0;
            $assetNotViolateAreaTotal1 -= isset($assetViolateAreasTotal[$asset->id][$detail1->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail1->asset_general_id] : 0;
            $cell->addText((isset($assetNotViolateAreaTotal1) ? number_format($assetNotViolateAreaTotal1, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetNotViolateAreaTotal2 = isset($detail2->asset_general_land_sum_area) ? $detail2->asset_general_land_sum_area : 0;
            $assetNotViolateAreaTotal2 -= isset($assetViolateAreasTotal[$asset->id][$detail2->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail2->asset_general_id] : 0;
            $cell->addText((isset($assetNotViolateAreaTotal2) ? number_format($assetNotViolateAreaTotal2, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetNotViolateAreaTotal3 = isset($detail3->asset_general_land_sum_area) ? $detail3->asset_general_land_sum_area : 0;
            $assetNotViolateAreaTotal3 -= isset($assetViolateAreasTotal[$asset->id][$detail3->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail3->asset_general_id] : 0;
            $cell->addText((isset($assetNotViolateAreaTotal3) ? number_format($assetNotViolateAreaTotal3, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            foreach ($asset->properties[0]->propertyDetail as $propertyDetail) {
                if ($propertyDetail->is_transfer_facility) {
                    $acronymTranferFacility = $propertyDetail->landTypePurpose->acronym;
                    $landType = $propertyDetail;

                    $table->addRow(400, $cantSplit);
                    $table->addCell(600, $cellRowContinue)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText($acronymTranferFacility . ' (' . $m2 . ')', null,  array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($propertyDetail->total_area) ? number_format($propertyDetail->total_area - floatval($propertyDetail->planning_area), 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym1 = null;
                    if (isset($detail1->propertyDetail)) {
                        foreach ($detail1->propertyDetail as $propertyDetail1) {
                            //if (($propertyDetail1->landTypePurposeData->acronym == $acronymTranferFacility) || (in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail1->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                            if (($propertyDetail1->landTypePurposeData->acronym == $acronymTranferFacility)) {
                                $acronym1 += $propertyDetail1->total_area;
                                $acronym1 -= (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                                $landType1 = $propertyDetail1;
                                unset($assetAreaByLands[$propertyDetail1->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym1) ? number_format($acronym1, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym2 = null;
                    if (isset($detail2->propertyDetail)) {
                        foreach ($detail2->propertyDetail as $propertyDetail2) {
                            //if (($propertyDetail2->landTypePurposeData->acronym == $acronymTranferFacility) || (in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail2->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                            if (($propertyDetail2->landTypePurposeData->acronym == $acronymTranferFacility)) {
                                $acronym2 += $propertyDetail2->total_area;
                                $acronym2 -= (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                                $landType2 = $propertyDetail2;
                                unset($assetAreaByLands[$propertyDetail2->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym2) ? number_format($acronym2, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym3 = null;
                    if (isset($detail3->propertyDetail)) {
                        foreach ($detail3->propertyDetail as $propertyDetail3) {
                            //if (($propertyDetail3->landTypePurposeData->acronym == $acronymTranferFacility) || (in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail3->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                            if (($propertyDetail3->landTypePurposeData->acronym == $acronymTranferFacility)) {
                                $acronym3 += $propertyDetail3->total_area;
                                $acronym3 -= (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                                $landType3 = $propertyDetail3;
                                unset($assetAreaByLands[$propertyDetail3->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym3) ? number_format($acronym3, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));
                }
            }
            foreach ($asset->properties[0]->propertyDetail as $propertyDetail) {
                if (!$propertyDetail->is_transfer_facility) {
                    $curentAcronym = $propertyDetail->landTypePurpose->acronym;
                    $table->addRow(400, $cantSplit);
                    // dd($propertyDetail->landTypePurpose->acronym);
                    $table->addCell(null,  $cellRowContinue)->addText('', null, $keepNext);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText($propertyDetail->landTypePurpose->acronym . ' (' . $m2 . ')', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($propertyDetail->total_area) ? number_format($propertyDetail->total_area - floatval($propertyDetail->planning_area), 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym1 = null;
                    if (isset($detail1->propertyDetail)) {
                        foreach ($detail1->propertyDetail as $propertyDetail1) {
                            //if ($propertyDetail1->landTypePurposeData->acronym != $acronymTranferFacility) {
                            if ($propertyDetail1->landTypePurposeData->acronym == $curentAcronym) {
                                $acronym1 += $propertyDetail1->total_area;
                                $acronym1 -= (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                                unset($assetAreaByLands[$propertyDetail1->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym1) ? number_format($acronym1, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym2 = null;
                    if (isset($detail2->propertyDetail)) {
                        foreach ($detail2->propertyDetail as $propertyDetail2) {
                            //if ($propertyDetail2->landTypePurposeData->acronym != $acronymTranferFacility) {
                            if ($propertyDetail2->landTypePurposeData->acronym == $curentAcronym) {
                                $acronym2 += $propertyDetail2->total_area;
                                $acronym2 -= (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                                unset($assetAreaByLands[$propertyDetail2->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym2) ? number_format($acronym2, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym3 = null;
                    if (isset($detail3->propertyDetail)) {
                        foreach ($detail3->propertyDetail as $propertyDetail3) {
                            //if ($propertyDetail3->landTypePurposeData->acronym != $acronymTranferFacility) {
                            if ($propertyDetail3->landTypePurposeData->acronym == $curentAcronym) {
                                $acronym3 += $propertyDetail3->total_area;
                                $acronym3 -= (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                                unset($assetAreaByLands[$propertyDetail3->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym3) ? number_format($acronym3, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));
                }
            }
            foreach ($assetAreaByLands as $landTypePurposeId => $value) {
                if (!isset($assetAreaByLands[$landTypePurposeId])) continue;

                $table->addRow(400, $cantSplit);
                $table->addCell(null, $cellRowContinue);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText($value . ' (' . $m2 . ')', null,  $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null,  $cellHCentered);
                //$table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format(0, 2, ',', '.'), null,  $cellHCentered);

                $acronym1 = null;
                if (isset($detail1->propertyDetail)) {
                    foreach ($detail1->propertyDetail as $propertyDetail1) {
                        if (($propertyDetail1->landTypePurposeData->id == $landTypePurposeId)) {
                            $acronym1 += $propertyDetail1->total_area;
                            $acronym1 -= (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                            unset($assetAreaByLands[$propertyDetail1->land_type_purpose_id]);
                        }
                    }
                }
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym1) ? number_format($acronym1, 2, ',', '.') : '-', null,  $cellHCentered);

                $acronym2 = null;
                if (isset($detail2->propertyDetail)) {
                    foreach ($detail2->propertyDetail as $propertyDetail2) {
                        if (($propertyDetail2->landTypePurposeData->id == $landTypePurposeId)) {
                            $acronym2 += $propertyDetail2->total_area;
                            $acronym2 -= (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                            unset($assetAreaByLands[$propertyDetail2->land_type_purpose_id]);
                        }
                    }
                }
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym2) ? number_format($acronym2, 2, ',', '.') : '-', null,  $cellHCentered);

                $acronym3 = null;
                if (isset($detail3->propertyDetail)) {
                    foreach ($detail3->propertyDetail as $propertyDetail3) {
                        if (($propertyDetail3->landTypePurposeData->id == $landTypePurposeId)) {
                            $acronym3 += $propertyDetail3->total_area;
                            $acronym3 -= (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                            unset($assetAreaByLands[$propertyDetail3->land_type_purpose_id]);
                        }
                    }
                }
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($acronym3) ? number_format($acronym3, 2, ',', '.') : '-', null,  $cellHCentered);

                unset($assetAreaByLands[$landTypePurposeId]);
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText(($stt - 1) . '.2', ['bold' => false], array_merge($cellHCentered, $keepNext));
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Vi phạm QH', ['bold' => false], array_merge($cellHCentered, $keepNext));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(((isset($assetViolateAreaTotal) && $assetViolateAreaTotal) ?  number_format($assetViolateAreaTotal, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetViolateAreaTotal1 = isset($assetViolateAreasTotal[$asset->id][$detail1->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail1->asset_general_id] : 0;
            $cell->addText((isset($assetViolateAreaTotal1) ? number_format($assetViolateAreaTotal1, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetViolateAreaTotal2 = isset($assetViolateAreasTotal[$asset->id][$detail2->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail2->asset_general_id] : 0;
            $cell->addText((isset($assetViolateAreaTotal2) ? number_format($assetViolateAreaTotal2, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetViolateAreaTotal3 = isset($assetViolateAreasTotal[$asset->id][$detail3->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail3->asset_general_id] : 0;
            $cell->addText((isset($assetViolateAreaTotal3) ? number_format($assetViolateAreaTotal3, 2, ',', '.') . $m2 : '-'), null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            foreach ($asset->properties[0]->propertyDetail as $propertyDetail) {
                if ($propertyDetail->is_transfer_facility) {
                    $acronymTranferFacility = $propertyDetail->landTypePurpose->acronym;
                    $landType = $propertyDetail;

                    $table->addRow(400, $cantSplit);
                    $table->addCell(600, $cellRowContinue)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText($acronymTranferFacility . ' (' . $m2 . ')', null,  array_merge($cellHCentered, $keepNext));
                    $violateAreaAsset = isset($violateAreasAsset[$propertyDetail->landTypePurpose->acronym]) ? $violateAreasAsset[$propertyDetail->landTypePurpose->acronym] : null;
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($violateAreaAsset) && ($violateAreaAsset != 0)) ? number_format($violateAreaAsset, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym1 = null;
                    if (isset($detail1->propertyDetail)) {
                        foreach ($detail1->propertyDetail as $propertyDetail1) {
                            //if (($propertyDetail1->landTypePurposeData->acronym == $acronymTranferFacility) || (in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail1->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                            if (($propertyDetail1->landTypePurposeData->acronym == $acronymTranferFacility)) {
                                $acronym1 = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                                unset($assetViolateAreaByLands[$propertyDetail1->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym1) && ($acronym1 != 0)) ? number_format($acronym1, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym2 = null;
                    if (isset($detail2->propertyDetail)) {
                        foreach ($detail2->propertyDetail as $propertyDetail2) {
                            //if (($propertyDetail2->landTypePurposeData->acronym == $acronymTranferFacility) || (in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail2->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                            if (($propertyDetail2->landTypePurposeData->acronym == $acronymTranferFacility)) {
                                $acronym2 = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                                unset($assetViolateAreaByLands[$propertyDetail2->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym2) && ($acronym2 != 0)) ? number_format($acronym2, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym3 = null;
                    if (isset($detail3->propertyDetail)) {
                        foreach ($detail3->propertyDetail as $propertyDetail3) {
                            //if (($propertyDetail3->landTypePurposeData->acronym == $acronymTranferFacility) || (in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail3->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                            if (($propertyDetail3->landTypePurposeData->acronym == $acronymTranferFacility)) {
                                $acronym3 = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                                unset($assetViolateAreaByLands[$propertyDetail3->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym3) && ($acronym3 != 0)) ? number_format($acronym3, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));
                }
            }
            foreach ($asset->properties[0]->propertyDetail as $propertyDetail) {
                if (!$propertyDetail->is_transfer_facility) {
                    $curentAcronym = $propertyDetail->landTypePurpose->acronym;
                    $table->addRow(400, $cantSplit);
                    $table->addCell(null,  $cellRowContinue)->addText('', null, $keepNext);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText($propertyDetail->landTypePurpose->acronym . ' (' . $m2 . ')', null, $cellHCentered);
                    $violateAreaAsset = isset($violateAreasAsset[$propertyDetail->landTypePurpose->acronym]) ? $violateAreasAsset[$propertyDetail->landTypePurpose->acronym] : null;
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($violateAreaAsset) && ($violateAreaAsset != 0)) ? number_format($violateAreaAsset, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym1 = null;
                    if (isset($detail1->propertyDetail)) {
                        foreach ($detail1->propertyDetail as $propertyDetail1) {
                            //if ($propertyDetail1->landTypePurposeData->acronym != $acronymTranferFacility) {
                            if ($propertyDetail1->landTypePurposeData->acronym == $curentAcronym) {
                                $acronym1 = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                                unset($assetViolateAreaByLands[$propertyDetail1->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym1) && ($acronym1 != 0)) ? number_format($acronym1, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym2 = null;
                    if (isset($detail2->propertyDetail)) {
                        foreach ($detail2->propertyDetail as $propertyDetail2) {
                            //if ($propertyDetail2->landTypePurposeData->acronym != $acronymTranferFacility) {
                            if ($propertyDetail2->landTypePurposeData->acronym == $curentAcronym) {
                                $acronym2 = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                                unset($assetViolateAreaByLands[$propertyDetail2->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym2) && ($acronym2 != 0)) ? number_format($acronym2, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));

                    $acronym3 = null;
                    if (isset($detail3->propertyDetail)) {
                        foreach ($detail3->propertyDetail as $propertyDetail3) {
                            //if ($propertyDetail3->landTypePurposeData->acronym != $acronymTranferFacility) {
                            if ($propertyDetail3->landTypePurposeData->acronym == $curentAcronym) {
                                $acronym3 = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                                unset($assetViolateAreaByLands[$propertyDetail3->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym3) && ($acronym3 != 0)) ? number_format($acronym3, 2, ',', '.') : '-', null,  array_merge($cellHCentered, $keepNext));
                }
            }
            foreach ($assetViolateAreaByLands as $landTypePurposeId => $value) {
                if (!isset($assetViolateAreaByLands[$landTypePurposeId])) continue;

                $table->addRow(400, $cantSplit);
                $table->addCell(null, $cellRowContinue);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText($value . ' (' . $m2 . ')', null,  $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null,  $cellHCentered);
                //$table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format(0, 2, ',', '.'), null,  $cellHCentered);

                $acronym1 = null;
                if (isset($detail1->propertyDetail)) {
                    foreach ($detail1->propertyDetail as $propertyDetail1) {
                        if (($propertyDetail1->landTypePurposeData->id == $landTypePurposeId)) {
                            $acronym1 = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                            unset($assetViolateAreaByLands[$propertyDetail1->land_type_purpose_id]);
                        }
                    }
                }
                $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym1) && ($acronym1 != 0)) ? number_format($acronym1, 2, ',', '.') : '-', null,  $cellHCentered);

                $acronym2 = null;
                if (isset($detail2->propertyDetail)) {
                    foreach ($detail2->propertyDetail as $propertyDetail2) {
                        if (($propertyDetail2->landTypePurposeData->id == $landTypePurposeId)) {
                            $acronym2 = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                            unset($assetViolateAreaByLands[$propertyDetail2->land_type_purpose_id]);
                        }
                    }
                }
                $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym2) && ($acronym2 != 0)) ? number_format($acronym2, 2, ',', '.') : '-', null,  $cellHCentered);

                $acronym3 = null;
                if (isset($detail3->propertyDetail)) {
                    foreach ($detail3->propertyDetail as $propertyDetail3) {
                        if (($propertyDetail3->landTypePurposeData->id == $landTypePurposeId)) {
                            $acronym3 = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                            unset($assetViolateAreaByLands[$propertyDetail3->land_type_purpose_id]);
                        }
                    }
                }
                $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($acronym3) && ($acronym3 != 0)) ? number_format($acronym3, 2, ',', '.') : '-', null,  $cellHCentered);

                unset($assetViolateAreaByLands[$landTypePurposeId]);
            }

            $asset1TotalViolateAmount = 0;
            $asset2TotalViolateAmount = 0;
            $asset3TotalViolateAmount = 0;

            $circularUnitBasePrice1 = null;
            $circularUnitBasePrice2 = null;
            $circularUnitBasePrice3 = null;

            $baseUnitPrice = 0;
            $baseAcronym = '';
            $baseAcronymId = '';
            $basePositionTypeId = '';
            if ((isset($asset->properties[0])) && (count($asset->properties[0]->propertyDetail) == 1)) {
                $baseUnitPrice = $asset->properties[0]->propertyDetail[0]->circular_unit_price;
                $baseAcronym = $asset->properties[0]->propertyDetail[0]->landTypePurpose->acronym;
                $baseAcronymId = $asset->properties[0]->propertyDetail[0]->land_type_purpose_id;
                $basePositionTypeId = $asset->properties[0]->propertyDetail[0]->position_type_id;
            } else {
                foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                    if ($item->is_transfer_facility || !$index) {
                        $baseUnitPrice = $item->circular_unit_price;
                        $baseAcronym = $item->landTypePurpose->acronym;
                        $baseAcronymId = $item->land_type_purpose_id;
                        $basePositionTypeId = $item->position_type_id;
                    }
                }
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], array_merge($cellHCentered, $keepNext));
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Đơn giá theo QĐ', ['bold' => false], array_merge($cellHCentered, $keepNext));
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('', null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('', null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('', null, ['align' => JcTable::CENTER, 'keepNext' => true]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('', null, ['align' => JcTable::CENTER, 'keepNext' => true]);

            foreach ($asset->properties[0]->propertyDetail as $propertyDetail) {
                if ($propertyDetail->is_transfer_facility) {
                    $table->addRow(400, $cantSplit);
                    $table->addCell(600, $cellRowSpan)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText($propertyDetail->landTypePurpose->acronym . ' (đ/' . $m2 . ')', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($propertyDetail->circular_unit_price) ? number_format($propertyDetail->circular_unit_price, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    unset($assetUnitPriceByLands[$propertyDetail->position_type_id][$propertyDetail->land_type_purpose_id]);

                    $circularUnitPrice1 = null;
                    if (isset($detail1->propertyDetail)) {
                        foreach ($detail1->propertyDetail as $propertyDetail1) {
                            if (($propertyDetail1->landTypePurposeData->acronym == $propertyDetail->landTypePurpose->acronym)) {
                                //|| (in_array($propertyDetail1->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice1 = $propertyDetail1->circular_unit_price;
                                $circularUnitPrice1 = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose] : $circularUnitPrice1;
                                $area = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                                $asset1TotalViolateAmount += ($circularUnitPrice1 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail1->position_type_id][$propertyDetail1->land_type_purpose]);
                            }
                        }
                    }
                    $circularBaseUnitPriceTmp = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$baseAcronymId])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$baseAcronymId] : $baseUnitPrice;
                    $circularUnitPrice1 = isset($circularUnitPrice1) ? $circularUnitPrice1 : $circularBaseUnitPriceTmp;
                    $circularUnitBasePrice1 = $circularUnitPrice1;
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice1) ? number_format($circularUnitPrice1, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    $circularUnitPrice2 = null;
                    if (isset($detail2->propertyDetail)) {
                        foreach ($detail2->propertyDetail as $propertyDetail2) {
                            if (($propertyDetail2->landTypePurposeData->acronym == $propertyDetail->landTypePurpose->acronym)) {
                                //|| (in_array($propertyDetail2->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice2 = $propertyDetail2->circular_unit_price;
                                $circularUnitPrice2 = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose] : $circularUnitPrice2;
                                $area = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                                $asset2TotalViolateAmount += ($circularUnitPrice2 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail2->position_type_id][$propertyDetail2->land_type_purpose]);
                            }
                        }
                    }
                    $circularBaseUnitPriceTmp = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$baseAcronymId])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$baseAcronymId] : $baseUnitPrice;
                    $circularUnitPrice2 = isset($circularUnitPrice2) ? $circularUnitPrice2 : $circularBaseUnitPriceTmp;
                    $circularUnitBasePrice2 = $circularUnitPrice2;
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice2) ? number_format($circularUnitPrice2, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    $circularUnitPrice3 = null;
                    if (isset($detail3->propertyDetail)) {
                        foreach ($detail3->propertyDetail as $propertyDetail3) {
                            if (($propertyDetail3->landTypePurposeData->acronym == $propertyDetail->landTypePurpose->acronym)) {
                                //|| (in_array($propertyDetail3->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice3 = $propertyDetail3->circular_unit_price;
                                $circularUnitPrice3 = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : $circularUnitPrice3;
                                $area = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                                $asset3TotalViolateAmount += ($circularUnitPrice3 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail3->position_type_id][$propertyDetail3->land_type_purpose]);
                            }
                        }
                    }
                    $circularBaseUnitPriceTmp = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$baseAcronymId])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$baseAcronymId] : $baseUnitPrice;
            $circularUnitPrice3 = isset($circularUnitPrice3) ? $circularUnitPrice3 : $circularBaseUnitPriceTmp;
                    $circularUnitBasePrice3 = $circularUnitPrice3;
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice3) ? number_format($circularUnitPrice3, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));
                }
            }
            foreach ($asset->properties[0]->propertyDetail as $propertyDetail) {
                if (!$propertyDetail->is_transfer_facility) {
                    $table->addRow(400, $cantSplit);
                    $table->addCell(null, $cellRowContinue)->addText('', null, $keepNext);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText($propertyDetail->landTypePurpose->acronym . ' (đ/' . $m2 . ')', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($propertyDetail->circular_unit_price) ? number_format($propertyDetail->circular_unit_price, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    unset($assetUnitPriceByLands[$propertyDetail->position_type_id][$propertyDetail->land_type_purpose_id]);

                    $circularUnitPrice1 = null;
                    if (isset($detail1->propertyDetail)) {
                        foreach ($detail1->propertyDetail as $propertyDetail1) {
                            if (($propertyDetail1->landTypePurposeData->acronym == $propertyDetail->landTypePurpose->acronym)) {
                                //|| (in_array($propertyDetail1->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice1 = $propertyDetail1->circular_unit_price;
                                $circularUnitPrice1 = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose] : $circularUnitPrice1;
                                $area = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                                $asset1TotalViolateAmount += ($circularUnitPrice1 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail1->position_type_id][$propertyDetail1->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice1) ? number_format($circularUnitPrice1, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    $circularUnitPrice2 = null;
                    if (isset($detail2->propertyDetail)) {
                        foreach ($detail2->propertyDetail as $propertyDetail2) {
                            if ($propertyDetail2->landTypePurposeData->acronym == $propertyDetail->landTypePurpose->acronym) {
                                $circularUnitPrice2 = $propertyDetail2->circular_unit_price;
                                $circularUnitPrice2 = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose] : $circularUnitPrice2;
                                $area = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                                $asset2TotalViolateAmount += ($circularUnitPrice2 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail2->position_type_id][$propertyDetail2->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice2) ? number_format($circularUnitPrice2, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    $circularUnitPrice3 = null;
                    if (isset($detail3->propertyDetail)) {
                        foreach ($detail3->propertyDetail as $propertyDetail3) {
                            if (($propertyDetail3->landTypePurposeData->acronym == $propertyDetail->landTypePurpose->acronym)) {
                                //|| (in_array($propertyDetail3->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($propertyDetail->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice3 = $propertyDetail3->circular_unit_price;
                                $circularUnitPrice3 = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose] : $circularUnitPrice3;
                                $area = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                                $asset3TotalViolateAmount += ($circularUnitPrice3 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail3->position_type_id][$propertyDetail3->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice3) ? number_format($circularUnitPrice3, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));
                }
            }
            foreach ($assetUnitPriceByLands as $positionTypeId => $assetUnitPriceByLand) {
                foreach ($assetUnitPriceByLand as $landTypePurposeId => $value) {
                    if (!isset($assetUnitPriceByLands[$positionTypeId][$landTypePurposeId])) continue;
                    $table->addRow(400, $cantSplit);
                    $table->addCell(600, $cellRowContinue)->addText('', ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText($value . ' (đ/' . $m2 . ')', null, array_merge($cellHCentered, $keepNext));
                    //$table->addCell($columnWidthSecond, $cellVCentered)->addText(0, null, array_merge($cellHCentered,$keepNext) );
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, array_merge($cellHCentered, $keepNext));

                    $circularUnitPrice1 = null;
                    if (isset($detail1->propertyDetail)) {
                        foreach ($detail1->propertyDetail as $propertyDetail1) {
                            if (($propertyDetail1->landTypePurposeData->id == $landTypePurposeId)) {
                                //|| (in_array($propertyDetail1->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($landTypePurposeId, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice1 = $propertyDetail1->circular_unit_price;
                                $circularUnitPrice1 = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose] : $circularUnitPrice1;
                                $area = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$propertyDetail1->land_type_purpose][$propertyDetail1->position_type_id] : 0;
                                $asset1TotalViolateAmount += ($circularUnitPrice1 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail1->position_type_id][$propertyDetail1->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice1) ? number_format($circularUnitPrice1, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    $circularUnitPrice2 = null;
                    if (isset($detail2->propertyDetail)) {
                        foreach ($detail2->propertyDetail as $propertyDetail2) {
                            if (($propertyDetail2->landTypePurposeData->id == $landTypePurposeId)) {
                                //|| (in_array($propertyDetail2->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($landTypePurposeId, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice2 = $propertyDetail2->circular_unit_price;
                                $circularUnitPrice2 = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose] : $circularUnitPrice2;
                                $area = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$propertyDetail2->land_type_purpose][$propertyDetail2->position_type_id] : 0;
                                $asset2TotalViolateAmount += ($circularUnitPrice2 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail2->position_type_id][$propertyDetail2->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice2) ? number_format($circularUnitPrice2, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    $circularUnitPrice3 = null;
                    if (isset($detail3->propertyDetail)) {
                        foreach ($detail3->propertyDetail as $propertyDetail3) {
                            if (($propertyDetail3->landTypePurposeData->id == $landTypePurposeId)) {
                                //|| (in_array($propertyDetail3->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($landTypePurposeId, EstimateAssetDefault::GROUP_LAND_TYPE))) {
                                $circularUnitPrice3 = $propertyDetail3->circular_unit_price;
                                $circularUnitPrice3 = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose] : $circularUnitPrice3;
                                $area = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$propertyDetail3->land_type_purpose][$propertyDetail3->position_type_id] : 0;
                                $asset3TotalViolateAmount += ($circularUnitPrice3 * $area);
                                unset($assetUnitPriceByLands[$propertyDetail3->position_type_id][$propertyDetail3->land_type_purpose]);
                            }
                        }
                    }
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($circularUnitPrice3) ? number_format($circularUnitPrice3, 0, ',', '.') : '-', null, array_merge($cellHCentered, $keepNext));

                    unset($assetUnitPriceByLands[$positionTypeId][$landTypePurposeId]);
                }
            }
            //}

            // if($checked["chieu_rong_mat_tien"]) {
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Chiều rộng mặt tiền', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($asset->properties[0]->front_side_width) ? number_format($asset->properties[0]->front_side_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail1->front_side_width) ? number_format($detail1->front_side_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail2->front_side_width) ? number_format($detail2->front_side_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail3->front_side_width) ? number_format($detail3->front_side_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            // }

            // if($checked["chieu_sau_khu_dat"]) {
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Chiều dài', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($asset->properties[0]->insight_width) ? number_format($asset->properties[0]->insight_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail1->insight_width) ? number_format($detail1->insight_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail2->insight_width) ? number_format($detail2->insight_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($detail3->insight_width) ? number_format($detail3->insight_width, 2, ',', '.') : '-') . 'm', null, ['align' => JcTable::CENTER]);
            // }

            // if($checked["hinh_dang_dat"]) {/
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Hình dáng', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset->properties[0]->landShape) ? CommonService::mbUcfirst($asset->properties[0]->landShape->description) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($detail1->landShape) ? CommonService::mbUcfirst($detail1->landShape->description) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($detail2->landShape) ? CommonService::mbUcfirst($detail2->landShape->description) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($detail3->landShape) ? CommonService::mbUcfirst($detail3->landShape->description) : '-', null, ['align' => JcTable::CENTER]);
            // }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Kết cấu xây dựng', ['bold' => false], $cellHCentered);
            $ktxd = '';
            if (isset($asset->tangibleAssets[0])) {
                $ktxd .= isset($asset->tangibleAssets[0]->buildingType->description) ? CommonService::mbUcfirst($asset->tangibleAssets[0]->buildingType->description) : '-';
            }
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText($ktxd, null, ['align' => JcTable::CENTER]);

            $ktxd1 = '';
            if (isset($asset1->tangibleAssets[0])) {
                $ktxd1 .= isset($asset1->tangibleAssets[0]->buildingType->description) ? CommonService::mbUcfirst($asset1->tangibleAssets[0]->buildingType->description) : '-';
            }
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText($ktxd1, null, ['align' => JcTable::CENTER]);

            $ktxd2 = '';
            if (isset($asset2->tangibleAssets[0])) {
                $ktxd2 .= isset($asset2->tangibleAssets[0]->buildingType->description) ? CommonService::mbUcfirst($asset2->tangibleAssets[0]->buildingType->description) : '-';
            }
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText($ktxd2, null, ['align' => JcTable::CENTER]);


            $ktxd3 = '';
            if (isset($asset3->tangibleAssets[0])) {
                $ktxd3 .= isset($asset3->tangibleAssets[0]->buildingType->description) ? CommonService::mbUcfirst($asset3->tangibleAssets[0]->buildingType->description) : '-';
            }
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText($ktxd3, null, ['align' => JcTable::CENTER]);
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('DTSXD', null, $cellHCentered);

            $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($asset->tangibleAssets[0]->total_construction_base) ? number_format($asset->tangibleAssets[0]->total_construction_base, 2, ',', '.') . $m2 : '-'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($asset1->tangibleAssets[0]->total_construction_base) ? number_format($asset1->tangibleAssets[0]->total_construction_base, 2, ',', '.') . $m2 : '-'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($asset2->tangibleAssets[0]->total_construction_base) ? number_format($asset2->tangibleAssets[0]->total_construction_base, 2, ',', '.') . $m2 : '-'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText((isset($asset3->tangibleAssets[0]->total_construction_base) ? number_format($asset3->tangibleAssets[0]->total_construction_base, 2, ',', '.') . $m2 : '-'), null, $cellHCentered);


            $table->addRow(400, $cantSplit);
            $table->addCell(null, $cellRowContinue);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Tỷ lệ CLCL', null, $cellHCentered);
            // $clcl = $asset->tangibleAssets[0]->remaining_quality ?? 0;
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);

            $clcl1 = $asset1->tangibleAssets[0]->remaining_quality ?? 0;
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($clcl1 != 0 ? $clcl1 . '%' : '-', null, $cellHCentered);

            $clcl2 = $asset2->tangibleAssets[0]->remaining_quality ?? 0;
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($clcl2 != 0 ? $clcl2 . '%' : '-', null, $cellHCentered);

            $clcl3 = $asset3->tangibleAssets[0]->remaining_quality ?? 0;
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($clcl3 != 0 ? $clcl3 . '%' : '-', null, $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, $cellRowContinue);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Đơn giá xây dựng mới', null, $cellHCentered);

            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);

            $dgxd1 = isset($asset1->tangibleAssets[0]) ? $asset1->tangibleAssets[0]->unit_price_m2 : 0;
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($dgxd1 != 0 ? number_format($dgxd1, 0, ',', '.') : '-', null, $cellHCentered);

            $dgxd2 = isset($asset2->tangibleAssets[0]) ? $asset2->tangibleAssets[0]->unit_price_m2 : 0;
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($dgxd2 != 0 ? number_format($dgxd2, 0, ',', '.') : '-', null, $cellHCentered);

            $dgxd3 = isset($asset3->tangibleAssets[0]) ? $asset3->tangibleAssets[0]->unit_price_m2 : 0;
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($dgxd3 != 0 ? number_format($dgxd3, 0, ',', '.') : '-', null, $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Giá trị còn lại CTXD (đồng)', ['bold' => false], $cellHCentered);

            $dtsxd1 = $asset1->tangibleAssets[0]->total_construction_base ?? 0;
            $dtsxd2 = $asset2->tangibleAssets[0]->total_construction_base ?? 0;
            $dtsxd3 = $asset3->tangibleAssets[0]->total_construction_base ?? 0;

            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);

            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $dgxdm1 = $dtsxd1 * $dgxd1 * $clcl1 / 100;
            $cell->addText($dgxdm1 != 0 ? number_format($dgxdm1, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);

            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $dgxdm2 = $dtsxd2 * $dgxd2 * $clcl2 / 100;

            $cell->addText($dgxdm2 != 0 ? number_format($dgxdm2, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);

            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $dgxdm3 = $dtsxd3 * $dgxd3 * $clcl3 / 100;
            $cell->addText($dgxdm3 != 0 ? number_format($dgxdm3, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);

            //if($checked["giao_thong"]) {
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Vị trí', ['bold' => false], $cellHCentered);
            //$table->addCell($columnWidthSecond, $cellVCentered)->addText('Giao thông', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $assetDesc = $asset->properties[0]->description ?? '-';
            $cell->addText(htmlspecialchars($assetDesc), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(htmlspecialchars($detail1->description) ?? '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(htmlspecialchars($detail2->description) ?? '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(htmlspecialchars($detail3->description) ?? '-', null, ['align' => JcTable::CENTER]);
            //}

            // if($checked["ket_cau_duong"]) {
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Kết cấu đường', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor1['ket_cau_duong']->appraise_title) ? CommonService::mbUcfirst($comparisonFactor1['ket_cau_duong']->appraise_title) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor1['ket_cau_duong']->asset_title) ? CommonService::mbUcfirst($comparisonFactor1['ket_cau_duong']->asset_title) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor2['ket_cau_duong']->asset_title) ? CommonService::mbUcfirst($comparisonFactor2['ket_cau_duong']->asset_title) : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor3['ket_cau_duong']->asset_title) ? CommonService::mbUcfirst($comparisonFactor3['ket_cau_duong']->asset_title) : '-', null, ['align' => JcTable::CENTER]);
            // }
            // if($checked["do_rong_duong"]) {
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Bề rộng đường', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor1['do_rong_duong']->appraise_title) ? number_format((float)$comparisonFactor1['do_rong_duong']->appraise_title, 2, ',', '.') . 'm' : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor1['do_rong_duong']->asset_title) ? number_format((float)$comparisonFactor1['do_rong_duong']->asset_title, 2, ',', '.') . 'm' : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor2['do_rong_duong']->asset_title) ? number_format((float)$comparisonFactor2['do_rong_duong']->asset_title, 2, ',', '.') . 'm' : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($comparisonFactor3['do_rong_duong']->asset_title) ? number_format((float)$comparisonFactor3['do_rong_duong']->asset_title, 2, ',', '.') . 'm' : '-', null, ['align' => JcTable::CENTER]);
            // }
            if($checked["dieu_kien_ha_tang"]) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Điều kiện hạ tầng', ['bold' => false], $cellHCentered);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset->properties[0]->conditions->description) ? CommonService::mbUcfirst($asset->properties[0]->conditions->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail1->conditions->description) ? CommonService::mbUcfirst($detail1->conditions->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail2->conditions->description) ? CommonService::mbUcfirst($detail2->conditions->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail3->conditions->description) ? CommonService::mbUcfirst($detail3->conditions->description) : '-', null, ['align' => JcTable::CENTER]);
            }
            if($checked["kinh_doanh"]) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Lợi thế kinh doanh', ['bold' => false], $cellHCentered);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset->properties[0]->business->description) ? CommonService::mbUcfirst($asset->properties[0]->business->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail1->business->description) ? CommonService::mbUcfirst($detail1->business->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail2->business->description) ? CommonService::mbUcfirst($detail2->business->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail3->business->description) ? CommonService::mbUcfirst($detail3->business->description) : '-', null, ['align' => JcTable::CENTER]);
            }
            if($checked["an_ninh_moi_truong_song"]) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('An ninh, môi trường sống', ['bold' => false], $cellHCentered);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset->properties[0]->socialSecurity->description) ? CommonService::mbUcfirst($asset->properties[0]->socialSecurity->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail1->socialSecurity->description) ? CommonService::mbUcfirst($detail1->socialSecurity->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail2->socialSecurity->description) ? CommonService::mbUcfirst($detail2->socialSecurity->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail3->socialSecurity->description) ? CommonService::mbUcfirst($detail3->socialSecurity->description) : '-', null, ['align' => JcTable::CENTER]);
            }
            if($checked["phong_thuy"]) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Phong thủy', ['bold' => false], $cellHCentered);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset->properties[0]->fengShui->description) ? CommonService::mbUcfirst($asset->properties[0]->fengShui->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail1->fengShui->description) ? CommonService::mbUcfirst($detail1->fengShui->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail2->fengShui->description) ? CommonService::mbUcfirst($detail2->fengShui->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail3->fengShui->description) ? CommonService::mbUcfirst($detail3->fengShui->description) : '-', null, ['align' => JcTable::CENTER]);
            }
            if($checked["dieu_kien_thanh_toan"]) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Điều kiện thanh toán', ['bold' => false], $cellHCentered);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset->properties[0]->paymenMethod->description) ? CommonService::mbUcfirst($asset->properties[0]->paymenMethod->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail1->paymenMethod->description) ? CommonService::mbUcfirst($detail1->paymenMethod->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail2->paymenMethod->description) ? CommonService::mbUcfirst($detail2->paymenMethod->description) : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($detail3->paymenMethod->description) ? CommonService::mbUcfirst($detail3->paymenMethod->description) : '-', null, ['align' => JcTable::CENTER]);
            }
            //Other factors
            foreach ($otherFactor1 as $factor => $item) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText($item->name, ['bold' => false], $cellHCentered);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($otherFactor1[$factor]) ? $otherFactor1[$factor]->appraise_title : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($otherFactor1[$factor]) ? $otherFactor1[$factor]->asset_title : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($otherFactor2[$factor]) ? $otherFactor2[$factor]->asset_title : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($otherFactor3[$factor]) ? $otherFactor3[$factor]->asset_title : '-', null, ['align' => JcTable::CENTER]);
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Giá rao bán', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset1->total_amount) ? number_format($asset1->total_amount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset2->total_amount) ? number_format($asset2->total_amount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset3->total_amount) ? number_format($asset3->total_amount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);

            $asset1AdjustPercent = 0;
            $asset2AdjustPercent = 0;
            $asset3AdjustPercent = 0;
            $asset1TotalEstimateAmount = 0;
            $asset2TotalEstimateAmount = 0;
            $asset3TotalEstimateAmount = 0;
            $changePurposePrice1 = null;
            $changePurposePrice2 = null;
            $changePurposePrice3 = null;
            foreach ($asset->appraiseAdapter as $item) {
                if ($item['asset_general_id'] == $asset1->id) {
                    $asset1AdjustPercent = $item['percent'];
                    $changePurposePrice1 = isset($item['change_purpose_price']) ? $item['change_purpose_price'] : null;
                    $asset1TotalViolateAmount = isset($item['change_violate_price']) ? $item['change_violate_price'] : $asset1TotalViolateAmount;
                    $asset1TotalEstimateAmount = $asset1->total_amount * $asset1AdjustPercent / 100;
                }
                if ($item['asset_general_id'] == $asset2->id) {
                    $asset2AdjustPercent = $item['percent'];
                    $changePurposePrice2 = isset($item['change_purpose_price']) ? $item['change_purpose_price'] : null;
                    $asset2TotalViolateAmount = isset($item['change_violate_price']) ? $item['change_violate_price'] : $asset2TotalViolateAmount;
                    $asset2TotalEstimateAmount = $asset2->total_amount * $asset2AdjustPercent / 100;
                }
                if ($item['asset_general_id'] == $asset3->id) {
                    $asset3AdjustPercent = $item['percent'];
                    $changePurposePrice3 = isset($item['change_purpose_price']) ? $item['change_purpose_price'] : null;
                    $asset3TotalViolateAmount = isset($item['change_violate_price']) ? $item['change_violate_price'] : $asset3TotalViolateAmount;
                    $asset3TotalEstimateAmount = $asset3->total_amount * $asset3AdjustPercent / 100;
                }
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Tỷ lệ giá rao bán', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($asset1AdjustPercent) ? number_format($asset1AdjustPercent, 0, ',', '.') : 100) . '%', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($asset2AdjustPercent) ? number_format($asset2AdjustPercent, 0, ',', '.') : 100) . '%', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText((isset($asset3AdjustPercent) ? number_format($asset3AdjustPercent, 0, ',', '.') : 100) . '%', null, ['align' => JcTable::CENTER]);

            if ($asset1TotalViolateAmount || $asset2TotalViolateAmount || $asset3TotalViolateAmount) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Giá trị phần diện tích quy hoạch', ['bold' => false], $cellHCentered);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText('-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset1TotalViolateAmount) ? number_format($asset1TotalViolateAmount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset2TotalViolateAmount) ? number_format($asset2TotalViolateAmount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);
                $cell = $table->addCell($columnWidthSecond, $cellVCentered);
                $cell->addText(isset($asset3TotalViolateAmount) ? number_format($asset3TotalViolateAmount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Tổng giá trị tài sản ước tính', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset1TotalEstimateAmount) ? number_format($asset1TotalEstimateAmount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset2TotalEstimateAmount) ? number_format($asset2TotalEstimateAmount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(isset($asset3TotalEstimateAmount) ? number_format($asset3TotalEstimateAmount, 0, ',', '.') : '-', null, ['align' => JcTable::CENTER]);

            $baseUnitPrice = floatval($baseUnitPrice);
            //echo '<pre>';
            $price1 = 0;
            if(isset($changePurposePrice1)) {
                $price1 = $changePurposePrice1;
            } else if (isset($detail1->propertyDetail)) {
                foreach ($detail1->propertyDetail as $item) {
                    if ($item->landTypePurposeData->acronym != $baseAcronym) {
                        $circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose] : $item->circular_unit_price;
                        $circularBaseUnitPrice = (isset($circularUnitBasePrice1)) ? $circularUnitBasePrice1 : $baseUnitPrice;
                        $unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);
                        $area = $item->total_area;
                        $area -= (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
                        $price1 += $unitPrice * floatval($area);
                    }
                }
            }
            $price2 = 0;
            if(isset($changePurposePrice2)) {
                $price2 = $changePurposePrice2;
            } else if (isset($detail2->propertyDetail)) {
                foreach ($detail2->propertyDetail as $item) {
                    if ($item->landTypePurposeData->acronym != $baseAcronym) {
                        $circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose] : $item->circular_unit_price;
                        $circularBaseUnitPrice = (isset($circularUnitBasePrice2)) ? $circularUnitBasePrice2 : $baseUnitPrice;
                        $unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);
                        $area = $item->total_area;
                        $area -= (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
                        $price2 += $unitPrice * floatval($area);
                    }
                }
            }
            $price3 = 0;
            if(isset($changePurposePrice3)) {
                $price3 = $changePurposePrice3;
            } else if (isset($detail3->propertyDetail)) {
                foreach ($detail3->propertyDetail as $item) {
                    if ($item->landTypePurposeData->acronym != $baseAcronym) {
                        $circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose] : $item->circular_unit_price;
                        $circularBaseUnitPrice = (isset($circularUnitBasePrice3)) ? $circularUnitBasePrice3 : $baseUnitPrice;
                        $unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);
                        $area = $item->total_area;
                        $area -= (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
                        $price3 += $unitPrice * floatval($area);
                    }
                }
            }
            //exit;
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Chi phí chuyển mục đích sử dụng', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format(abs($price1), 0, ',', '.'), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format(abs($price2), 0, ',', '.'), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format(abs($price3), 0, ',', '.'), null, ['align' => JcTable::CENTER]);

            //Land price = Sell price after ajust + tranfer - construct price
            $totalPrice1 = ($asset1TotalEstimateAmount ?? 0) + $price1 - $dgxdm1 - $asset1TotalViolateAmount;
            $totalPrice2 = ($asset2TotalEstimateAmount ?? 0) + $price2 - $dgxdm2 - $asset2TotalViolateAmount;
            $totalPrice3 = ($asset3TotalEstimateAmount ?? 0) + $price3 - $dgxdm3 - $asset3TotalViolateAmount;

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Giá trị QSDĐ ' . ($landType->landTypePurpose->acronym ?? '') . ' ước tính (Đất trống)', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('-', null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format($totalPrice1, 0, ',', '.'), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format($totalPrice2, 0, ',', '.'), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format($totalPrice3, 0, ',', '.'), null, ['align' => JcTable::CENTER]);

            $dgd1 = isset($assetNotViolateAreaTotal1) ? ($totalPrice1 / $assetNotViolateAreaTotal1) : 0;
            $dgd2 = isset($assetNotViolateAreaTotal2) ? ($totalPrice2 / $assetNotViolateAreaTotal2) : 0;
            $dgd3 = isset($assetNotViolateAreaTotal3) ? ($totalPrice3 / $assetNotViolateAreaTotal3) : 0;

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText($stt++, ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Đ/giá đất ' . ($landType->landTypePurpose->acronym ?? '') . ' B.Quân.', ['bold' => false], $cellHCentered);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText('-', ['bold' => true], ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format($dgd1, 0, ',', '.'), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format($dgd2, 0, ',', '.'), null, ['align' => JcTable::CENTER]);
            $cell = $table->addCell($columnWidthSecond, $cellVCentered);
            $cell->addText(number_format($dgd3, 0, ',', '.'), null, ['align' => JcTable::CENTER]);

            $section->addPageBreak(1);
            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('✓ ', ['bold' => false]);
            $textRun->addText('Phân tích, so sánh, điều chỉnh mức giá do các yếu khác biệt của các tài sản so sánh với tài sản cần thẩm định giá:', ['bold' => true, 'italic' => true]);

            $phpWord->addTableStyle('Rowspan', $styleTableHide);
            $table = $section->addTable('Rowspan');
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellVCentered)->addText(null, ['bold' => false], $cellHCentered);
            $cell = $table->addCell(10000, $cellColSpan);
            $cell->addText('Phân tích, so sánh các tài sản so sánh với tài sản cần thẩm định giá:', ['bold' => true]);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellVCentered)->addText('+', ['bold' => true], $cellHCentered);
            $cell = $table->addCell(10000, $cellColSpan);
            $cell->addText('Căn cứ vào quá trình khảo sát thực tế tài sản thẩm định giá.', ['bold' => false]);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellVCentered)->addText('+', ['bold' => true], $cellHCentered);
            $cell = $table->addCell(10000, $cellColSpan);
            $cell->addText('Căn cứ vào bảng thông tin các tài sản so sánh (TSSS) mà ' . mb_strtoupper($company->acronym) . ' đã thu thập trong vòng 02 năm so với thời điểm định giá.', ['bold' => false]);

            for ($i = 1; $i <= 3; $i++) {
                $comparisonFactorTmp = $comparisonFactor1;
                $otherFactorTmp = $otherFactor1;
                if ($i == 2) {
                    $comparisonFactorTmp = $comparisonFactor2;
                    $otherFactorTmp = $otherFactor2;
                } else if ($i == 3) {
                    $comparisonFactorTmp = $comparisonFactor3;
                    $otherFactorTmp = $otherFactor3;
                }

                $table->addRow(500, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText('▪', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $cell = $table->addCell(10000, $cellColSpan);
                $cell->addText('TSSS' . $i . ':', ['bold' => true], ['align' => JcTable::START]);
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText('', ['bold' => true], $cellHCentered);
                $cell = $table->addCell(10000, $cellColSpan);
                foreach ($factors as $factor) {
                    if (isset($comparisonFactorTmp[$factor]) && $comparisonFactorTmp[$factor]->status == 1) {
                        $unit = ($factor == 'quy_mo') ? $m2 : 'm';
                        $appraiseTitle = $comparisonFactorTmp[$factor]->appraise_title;
                        $appraiseTitle = (is_numeric($appraiseTitle) || $factor == 'quy_mo') ? number_format(floatval($appraiseTitle), 2, ',', '.') . $unit : CommonService::mbUcfirst($appraiseTitle);

                        $assetTitle = $comparisonFactorTmp[$factor]->asset_title;
                        $assetTitle = (is_numeric($assetTitle) || $factor == 'quy_mo') ? number_format(floatval($assetTitle), 2, ',', '.') . $unit : mb_strtolower($assetTitle);

                        $description = $comparisonFactorTmp[$factor]->name . ' ' . mb_strtolower($comparisonFactorTmp[$factor]->description) . ' TSTĐ ' . '(';
                        //$description .= ($comparisonFactorTmp[$factor]->description == 'TƯƠNG ĐỒNG' ? $appraiseTitle : $appraiseTitle . ' so với ' . $assetTitle) . ')';
                        $description .= ($comparisonFactorTmp[$factor]->description == 'TƯƠNG ĐỒNG' ? $appraiseTitle : $assetTitle . ' so với ' . $appraiseTitle) . ')';
                        $cell->addText($description, ['bold' => false]);
                    }
                }
                foreach ($otherFactor1 as $factor => $item) {
                    if (isset($otherFactorTmp[$factor]) && $otherFactorTmp[$factor]->status == 1) {
                        $appraiseTitle = $otherFactorTmp[$factor]->appraise_title;
                        //$appraiseTitle = CommonService::mbUcfirst($appraiseTitle);

                        $assetTitle = $otherFactorTmp[$factor]->asset_title;
                        $assetTitle = ($assetTitle);

                        $description = $otherFactorTmp[$factor]->name . ' ' . mb_strtolower($otherFactorTmp[$factor]->description) . ' TSTĐ ' . '(';
                        $description .= ($otherFactorTmp[$factor]->description == 'TƯƠNG ĐỒNG' ? $appraiseTitle : $assetTitle . ' so với ' . $appraiseTitle) . ')';
                        $cell->addText($description, ['bold' => false]);
                    }
                }
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText(null, ['bold' => true],array_merge( $cellHCentered,$keepNext));
            $cell = $table->addCell(10000, $cellColSpan);
            $cell->addText('Điều chỉnh mức giá do các yếu khác biệt của các tài sản so sánh với tài sản cần thẩm định giá:', ['bold' => true, 'italic' => true], ['align' => JcTable::START,'keepNext'=>true]);

            foreach ($factors as $factor) {
                if (isset($comparisonFactor1[$factor]) && $comparisonFactor1[$factor]->status == 1) {
                    $table->addRow(500, $cantSplit);
                    $table->addCell(600, $cellRowSpan)->addText('+', ['bold' => true], array_merge($cellHCentered, $keepNext));
                    $cell = $table->addCell(10000, $cellColSpan);
                    $cell->addText($comparisonFactor1[$factor]->name, ['bold' => true], ['align' => JcTable::START,'keepNext'=>true]);
                    if (isset($comparisonFactor1[$factor])) {
                        $table->addRow(400, $cantSplit);
                        $table->addCell(600, $cellRowSpan)->addText('', ['bold' => true], array_merge($cellHCentered,$keepNext));
                        $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS1:', ['bold' => true], ['align' => 'right','keepNext'=>true]);
                        $cell = $table->addCell(8000, $cellColSpan);
                        $cell->addText(CommonService::mbUcfirst($comparisonFactor1[$factor]->description) . ' TSTĐ ' . abs($comparisonFactor1[$factor]->adjust_percent) . '%',null,$keepNext);
                    }

                    if (isset($comparisonFactor2[$factor])) {
                        $table->addRow(400, $cantSplit);
                        $table->addCell(600, $cellRowSpan)->addText('', ['bold' => true],array_merge($cellHCentered,$keepNext) );
                        $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS2:', ['bold' => true], ['align' => 'right','keepNext'=>true]);
                        $cell = $table->addCell(8000, $cellColSpan);
                        $cell->addText(CommonService::mbUcfirst($comparisonFactor2[$factor]->description) . ' TSTĐ ' . abs($comparisonFactor2[$factor]->adjust_percent) . '%',null,$keepNext);
                    }

                    if (isset($comparisonFactor3[$factor])) {
                        $table->addRow(400, $cantSplit);
                        $table->addCell(600, $cellRowSpan)->addText('', ['bold' => true], $cellHCentered);
                        $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS3:', ['bold' => true], ['align' => 'right']);
                        $cell = $table->addCell(8000, $cellColSpan);
                        $cell->addText(CommonService::mbUcfirst($comparisonFactor3[$factor]->description) . ' TSTĐ ' . abs($comparisonFactor3[$factor]->adjust_percent) . '%');
                    }
                }
            }

            foreach ($otherFactor1 as $factor => $item) {
                if (isset($otherFactor1[$factor]) && $otherFactor1[$factor]->status == 1) {
                    $table->addRow(500, $cantSplit);
                    $table->addCell(600, $cellRowSpan)->addText('+', ['bold' => true], array_merge($cellHCentered, $keepNext));
                    $cell = $table->addCell(10000, $cellColSpan);
                    $cell->addText($otherFactor1[$factor]->name, ['bold' => true], ['align' => JcTable::START,'keepNext'=>true]);
                    if (isset($otherFactor1[$factor])) {
                        $table->addRow(400, $cantSplit);
                        $table->addCell(600, $cellRowSpan)->addText('', ['bold' => true],array_merge($cellHCentered,$keepNext) );
                        $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS1:', ['bold' => true], ['align' => 'right','keepNext'=>true]);
                        $cell = $table->addCell(8000, $cellColSpan);
                        $cell->addText(CommonService::mbUcfirst($otherFactor1[$factor]->description) . ' TSTĐ ' . abs($otherFactor1[$factor]->adjust_percent) . '%',null,$keepNext);
                    }

                    if (isset($otherFactor2[$factor])) {
                        $table->addRow(400, $cantSplit);
                        $table->addCell(600, $cellRowSpan)->addText('', ['bold' => true], $cellHCentered);
                        $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS2:', ['bold' => true], ['align' => 'right','keepNext'=>true]);
                        $cell = $table->addCell(8000, $cellColSpan);
                        $cell->addText(CommonService::mbUcfirst($otherFactor2[$factor]->description) . ' TSTĐ ' . abs($otherFactor2[$factor]->adjust_percent) . '%',null,$keepNext);
                    }

                    if (isset($otherFactor3[$factor])) {
                        $table->addRow(400, $cantSplit);
                        $table->addCell(600, $cellRowSpan)->addText('', ['bold' => true], $cellHCentered);
                        $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS3:', ['bold' => true], ['align' => 'right']);
                        $cell = $table->addCell(8000, $cellColSpan);
                        $cell->addText(CommonService::mbUcfirst($otherFactor3[$factor]->description) . ' TSTĐ ' . abs($otherFactor3[$factor]->adjust_percent) . '%');
                    }
                }
            }

            $section->addPageBreak(1);
            $section->addText('BẢNG ĐIỀU CHỈNH CÁC YẾU TỐ SO SÁNH TSTĐG VÀ TSSS', ['bold' => false, 'size' => 13], ['align' => 'center']);

            $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
            $table = $section->addTable($styleTable);
            $table->addRow(400, $rowHeader);
            $table->addCell(600, $cellVCentered)->addText('TT', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('YẾU TỐ', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(' TSTĐ', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS1', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS2', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('TSSS3', ['bold' => true], $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellVCentered)->addText('1', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Đơn giá quyền sử dụng đất (đ/' . $m2 . ')', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($dgd1, 0, ',', '.'), ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($dgd2, 0, ',', '.'), ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($dgd3, 0, ',', '.'), ['bold' => true], $cellHCentered);

            //Print alphabetic header base on their index
            $alphas = range('A', 'Z');
            $index = 0;

            $totalPricePL1 = 0;
            $totalPricePL2 = 0;
            $totalPricePL3 = 0;

            //Ti le dieu chinh
            $tldc1 = 0;
            $tldc2 = 0;
            $tldc3 = 0;

            //Muc gia chi dan
            $mgcd1 = 0;
            $mgcd2 = 0;
            $mgcd3 = 0;

            if (isset($comparisonFactor1['phap_ly']) && $comparisonFactor1['phap_ly']->status == 1) {
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellRowSpan)->addText($alphas[$index], ['bold' => false], array_merge($cellHCentered, $keepNext));
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Pháp lý', ['bold' => true], $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($asset->properties[0]->legal->description) ? CommonService::mbUcfirst($asset->properties[0]->legal->description) : '-', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($detail1->legal->description) ? CommonService::mbUcfirst($detail1->legal->description) : '-', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($detail2->legal->description) ? CommonService::mbUcfirst($detail2->legal->description) : '-', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(isset($detail3->legal->description) ? CommonService::mbUcfirst($detail3->legal->description) : '-', null, $cellHCentered);

                $percentPl1 = $comparisonFactor1['phap_ly']->adjust_percent ?? 0;
                $percentPl2 = $comparisonFactor2['phap_ly']->adjust_percent ?? 0;
                $percentPl3 = $comparisonFactor3['phap_ly']->adjust_percent ?? 0;

                $table->addRow(400, $cantSplit);
                $table->addCell(null, $cellRowContinue);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Tỷ lệ điều chỉnh', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($percentPl1 . '%', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($percentPl2 . '%', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($percentPl3 . '%', null, $cellHCentered);

                $pricePl1 = $percentPl1 * $dgd1 / 100;
                $pricePl2 = $percentPl2 * $dgd2 / 100;
                $pricePl3 = $percentPl3 * $dgd3 / 100;

                $tldc1 += $pricePl1;
                $tldc2 += $pricePl2;
                $tldc3 += $pricePl3;

                $table->addRow(400, $cantSplit);
                $table->addCell(null, $cellRowContinue);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Mức điều chỉnh', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($pricePl1, 0, ',', '.'), null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($pricePl2, 0, ',', '.'), null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($pricePl3, 0, ',', '.'), null, $cellHCentered);

                $totalPricePL1 = $dgd1 * (1 + $percentPl1 / 100);
                $totalPricePL2 = $dgd2 * (1 + $percentPl2 / 100);
                $totalPricePL3 = $dgd3 * (1 + $percentPl3 / 100);

                $mgcd1 = $totalPricePL1;
                $mgcd2 = $totalPricePL2;
                $mgcd3 = $totalPricePL3;

                $table->addRow(400, $cantSplit);
                $table->addCell(null, $cellRowContinue);
                $table->addCell($columnWidthFirst, $cellVCentered)->addText('Giá sau điều chỉnh', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($totalPricePL1, 0, ',', '.'), null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($totalPricePL2, 0, ',', '.'), null, $cellHCentered);
                $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($totalPricePL3, 0, ',', '.'), null, $cellHCentered);
            }

            $stepPrice1 = $totalPricePL1;
            $stepPrice2 = $totalPricePL2;
            $stepPrice3 = $totalPricePL3;
            foreach ($factors as $factor) {
                if ($factor !== 'phap_ly' && isset($comparisonFactor1[$factor]) && $comparisonFactor1[$factor]->status == 1) {
                    $index += 1;
                    $table->addRow(400, $cantSplit);
                    $table->addCell(600, $cellRowSpan)->addText($alphas[$index], ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText(isset($comparisonFactor1[$factor]->name) ? CommonService::mbUcfirst($comparisonFactor1[$factor]->name) : '-', ['bold' => true], array_merge($cellHCentered, $keepNext));


                    $unit = ($factor == 'quy_mo') ? $m2 : 'm';

                    $appraiseTitle = isset($comparisonFactor1[$factor]->appraise_title) ? CommonService::mbUcfirst($comparisonFactor1[$factor]->appraise_title) : '-';
                    $appraiseTitle = (is_numeric($appraiseTitle) || $factor == 'quy_mo') ? number_format(floatval($appraiseTitle), 2, ',', '.') . $unit : CommonService::mbUcfirst($appraiseTitle);

                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($appraiseTitle, null, array_merge($cellHCentered, $keepNext));


                    $assetTitle = isset($comparisonFactor1[$factor]->asset_title) ? CommonService::mbUcfirst($comparisonFactor1[$factor]->asset_title) : '-';
                    $assetTitle = (is_numeric($assetTitle) || $factor == 'quy_mo') ? number_format(floatval($assetTitle), 2, ',', '.') . $unit : CommonService::mbUcfirst($assetTitle);

                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($assetTitle, null, array_merge($cellHCentered, $keepNext));

                    $assetTitle = isset($comparisonFactor2[$factor]->asset_title) ? CommonService::mbUcfirst($comparisonFactor2[$factor]->asset_title) : '-';
                    $assetTitle = (is_numeric($assetTitle) || $factor == 'quy_mo') ? number_format(floatval($assetTitle), 2, ',', '.') . $unit : CommonService::mbUcfirst($assetTitle);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($assetTitle, null,array_merge($cellHCentered, $keepNext));

                    $assetTitle = isset($comparisonFactor3[$factor]->asset_title) ? CommonService::mbUcfirst($comparisonFactor3[$factor]->asset_title) : '-';
                    $assetTitle = (is_numeric($assetTitle) || $factor == 'quy_mo') ? number_format(floatval($assetTitle), 2, ',', '.') . $unit : CommonService::mbUcfirst($assetTitle);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($assetTitle, null, array_merge($cellHCentered, $keepNext));

                    $percent1 = $comparisonFactor1[$factor]->adjust_percent ?? 0;
                    $percent2 = $comparisonFactor2[$factor]->adjust_percent ?? 0;
                    $percent3 = $comparisonFactor3[$factor]->adjust_percent ?? 0;

                    $table->addRow(400, $cantSplit);
                    $table->addCell(null, $cellRowContinue)->addText('',null,$keepNext);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText('Tỷ lệ điều chỉnh', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($percent1 . '%', null,array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($percent2 . '%', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($percent3 . '%', null, array_merge($cellHCentered, $keepNext));

                    $price1 = $percent1 * $totalPricePL1 / 100;
                    $price2 = $percent2 * $totalPricePL2 / 100;
                    $price3 = $percent3 * $totalPricePL3 / 100;

                    $tldc1 += $price1;
                    $tldc2 += $price2;
                    $tldc3 += $price3;

                    $mgcd1 += $price1;
                    $mgcd2 += $price2;
                    $mgcd3 += $price3;

                    $stepPrice1 += $price1;
                    $stepPrice2 += $price2;
                    $stepPrice3 += $price3;

                    $table->addRow(400, $cantSplit);
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText('Mức điều chỉnh', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($price1, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($price2, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($price3, 0, ',', '.'), null, $cellHCentered);

                    $table->addRow(400, $cantSplit);
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText('Giá sau điều chỉnh', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($stepPrice1, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($stepPrice2, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($stepPrice3, 0, ',', '.'), null, $cellHCentered);

                }
            }

            foreach ($otherFactor1 as $factor => $item) {
                if ($factor !== 'phap_ly' && isset($otherFactor1[$factor]) && $otherFactor1[$factor]->status == 1) {
                    $index += 1;
                    $table->addRow(400, $cantSplit);
                    $table->addCell(600, $cellRowSpan)->addText($alphas[$index], ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText(isset($otherFactor1[$factor]->name) ? CommonService::mbUcfirst($otherFactor1[$factor]->name) : '-', ['bold' => true], array_merge($cellHCentered, $keepNext));


                    $unit = ($factor == 'quy_mo') ? '' : '';

                    //$appraiseTitle = isset($otherFactor1[$factor]->appraise_title) ? CommonService::mbUcfirst($otherFactor1[$factor]->appraise_title) : '-';
                    $appraiseTitle = isset($otherFactor1[$factor]->appraise_title) ? ($otherFactor1[$factor]->appraise_title) : '-';
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($appraiseTitle, null, array_merge($cellHCentered, $keepNext));


                    //$assetTitle = isset($otherFactor1[$factor]->asset_title) ? CommonService::mbUcfirst($otherFactor1[$factor]->asset_title) : '-';
                    $assetTitle = isset($otherFactor1[$factor]->asset_title) ? ($otherFactor1[$factor]->asset_title) : '-';
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($assetTitle, null, array_merge($cellHCentered, $keepNext));

                    //$assetTitle = isset($otherFactor2[$factor]->asset_title) ? CommonService::mbUcfirst($otherFactor2[$factor]->asset_title) : '-';
                    $assetTitle = isset($otherFactor2[$factor]->asset_title) ? ($otherFactor2[$factor]->asset_title) : '-';
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($assetTitle, null, array_merge($cellHCentered, $keepNext));

                    //$assetTitle = isset($otherFactor3[$factor]->asset_title) ? CommonService::mbUcfirst($otherFactor3[$factor]->asset_title) : '-';
                    $assetTitle = isset($otherFactor3[$factor]->asset_title) ? ($otherFactor3[$factor]->asset_title) : '-';
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($assetTitle, null, array_merge($cellHCentered, $keepNext));

                    $percent1 = $otherFactor1[$factor]->adjust_percent ?? 0;
                    $percent2 = $otherFactor2[$factor]->adjust_percent ?? 0;
                    $percent3 = $otherFactor3[$factor]->adjust_percent ?? 0;

                    $table->addRow(400, $cantSplit);
                    $table->addCell(null, $cellRowContinue)->addText('',null,$keepNext);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText('Tỷ lệ điều chỉnh', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($percent1 . '%', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($percent2 . '%', null, array_merge($cellHCentered, $keepNext));
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText($percent3 . '%', null, array_merge($cellHCentered, $keepNext));

                    $price1 = $percent1 * $totalPricePL1 / 100;
                    $price2 = $percent2 * $totalPricePL2 / 100;
                    $price3 = $percent3 * $totalPricePL3 / 100;

                    $tldc1 += $price1;
                    $tldc2 += $price2;
                    $tldc3 += $price3;

                    $mgcd1 += $price1;
                    $mgcd2 += $price2;
                    $mgcd3 += $price3;

                    $stepPrice1 += $price1;
                    $stepPrice2 += $price2;
                    $stepPrice3 += $price3;

                    $table->addRow(400, $cantSplit);
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText('Mức điều chỉnh', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($price1, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($price2, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($price3, 0, ',', '.'), null, $cellHCentered);

                    $table->addRow(400, $cantSplit);
                    $table->addCell(null, $cellRowContinue);
                    $table->addCell($columnWidthFirst, $cellVCentered)->addText('Giá sau điều chỉnh', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($stepPrice1, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($stepPrice2, 0, ',', '.'), null, $cellHCentered);
                    $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($stepPrice3, 0, ',', '.'), null, $cellHCentered);

                }
            }

            $mgcdMin = min($mgcd1, $mgcd2, $mgcd3);
            $mgcdMax = max($mgcd1, $mgcd2, $mgcd3);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('2', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthFirst, $cellVCentered)->addText('Mức giá chỉ dẫn (đ/' . $m2 . ')', ['bold' => true], $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText('-', null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($mgcd1, 0, ',', '.'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($mgcd2, 0, ',', '.'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($mgcd3, 0, ',', '.'), null, $cellHCentered);

            $mgtb = count($asset->assetGeneral) > 0 ? (($mgcd1 + $mgcd2 + $mgcd3) / count($asset->assetGeneral)) : 0;
            $mgtbTmp = $mgtb;

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('3', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Mức giá trung bình của các mức giá chỉ dẫn (đ/' . $m2 . ')', ['bold' => false], ['align' => 'left']);
            $table->addCell($columnWidthFourth, ['gridSpan' => 3, 'valign' => 'center'])->addText(number_format($mgtbTmp, 0, ',', '.'), null, $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('4', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Mức độ chênh lệch với mức giá trung bình của các mức giá chỉ dẫn (%) (không quá ±15%) ', ['bold' => false], ['align' => 'left']);

            $mgcl1 = $mgtb ? round(($mgcd1 - $mgtb) / $mgtb * 100, 0) : 0;
            $mgcl2 = $mgtb ? round(($mgcd2 - $mgtb) / $mgtb * 100, 0) : 0;
            $mgcl3 = $mgtb ? round(($mgcd3 - $mgtb) / $mgtb * 100, 0) : 0;

            if (abs($mgcl1) > 15) {
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($mgcl1 . '%', ['color' => 'FF0000'], $cellHCentered);
            } else {
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($mgcl1 . '%', null, $cellHCentered);
            }
            if (abs($mgcl2) > 15) {
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($mgcl2 . '%', ['color' => 'FF0000'], $cellHCentered);
            } else {
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($mgcl2 . '%', null, $cellHCentered);
            }
            if (abs($mgcl3) > 15) {
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($mgcl3 . '%', ['color' => 'FF0000'], $cellHCentered);
            } else {
                $table->addCell($columnWidthSecond, $cellVCentered)->addText($mgcl3 . '%', null, $cellHCentered);
            }

            $namePP = "trung bình của 3 TSSS";
            if (isset($asset->unify_indicative_price_slug) && ($asset->unify_indicative_price_slug == 'thap-nhat')) {
                $mgtb = $mgcdMin;
                $maTSSS = "";
                if ($mgcd1 == $mgtb) {
                    $maTSSS = "TSSS 1";
                } else if ($mgcd2 == $mgtb) {
                    $maTSSS = "TSSS 2";
                } else {
                    $maTSSS = "TSSS 3";
                }
                $namePP = "thấp nhất của " . $maTSSS;
            }
            if (isset($asset->unify_indicative_price_slug) && ($asset->unify_indicative_price_slug == 'cao-nhat')) {
                $mgtb = $mgcdMax;
                $maTSSS = "";
                if ($mgcd1 == $mgtb) {
                    $maTSSS = "TSSS 1";
                } else if ($mgcd2 == $mgtb) {
                    $maTSSS = "TSSS 2";
                } else {
                    $maTSSS = "TSSS 3";
                }
                $namePP = "cao nhất của " . $maTSSS;
            }

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('5', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tổng giá trị điều chỉnh gộp (đ/' . $m2 . ')', ['bold' => false], ['align' => 'left']);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format(abs($tldc1), 0, ',', '.'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format(abs($tldc2), 0, ',', '.'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format(abs($tldc3), 0, ',', '.'), null, $cellHCentered);

            $comparisonFactorMin1 = 1000;
            $comparisonFactorMax1 = 0;
            $comparisonFactorChange1 = 0;
            foreach ($comparisonFactor1 as $comparisonFactor) {
                if ($comparisonFactor->status == 1 && abs($comparisonFactor->adjust_percent) != 0) {
                    if (abs($comparisonFactor->adjust_percent) < $comparisonFactorMin1) {
                        $comparisonFactorMin1 = abs($comparisonFactor->adjust_percent);
                    }
                    if (abs($comparisonFactor->adjust_percent) > $comparisonFactorMax1) {
                        $comparisonFactorMax1 = abs($comparisonFactor->adjust_percent);
                    }
                    $comparisonFactorChange1 += 1;
                }
            }
            // tính số lần điều chỉnh
            foreach ($otherFactor1 as $otherFactor) {
                if ($otherFactor->status == 1 && abs($otherFactor->adjust_percent) != 0) {
                    if (abs($otherFactor->adjust_percent) < $comparisonFactorMin1) {
                        $comparisonFactorMin1 = abs($otherFactor->adjust_percent);
                    }
                    if (abs($otherFactor->adjust_percent) > $comparisonFactorMax1) {
                        $comparisonFactorMax1 = abs($otherFactor->adjust_percent);
                    }
                    $comparisonFactorChange1 += 1;
                }
            }
            if ( $comparisonFactorMin1 = 1000) $comparisonFactorMin1 = 0;

            $comparisonFactorMin2 = 1000;
            $comparisonFactorMax2 = 0;
            $comparisonFactorChange2 = 0;
            foreach ($comparisonFactor2 as $comparisonFactor) {
                if ($comparisonFactor->status == 1 && abs($comparisonFactor->adjust_percent) != 0) {
                    if (abs($comparisonFactor->adjust_percent) < $comparisonFactorMin2) {
                        $comparisonFactorMin2 = abs($comparisonFactor->adjust_percent);
                    }
                    if (abs($comparisonFactor->adjust_percent) > $comparisonFactorMax2) {
                        $comparisonFactorMax2 = abs($comparisonFactor->adjust_percent);
                    }
                    $comparisonFactorChange2 += 1;
                }
            }
            // tính số lần điều chỉnh
            foreach ($otherFactor2 as $otherFactor) {
                if ($otherFactor->status == 1 && abs($otherFactor->adjust_percent) != 0) {
                    if (abs($otherFactor->adjust_percent) < $comparisonFactorMin2) {
                        $comparisonFactorMin2 = abs($otherFactor->adjust_percent);
                    }
                    if (abs($otherFactor->adjust_percent) > $comparisonFactorMax2) {
                        $comparisonFactorMax2 = abs($otherFactor->adjust_percent);
                    }
                    $comparisonFactorChange2 += 1;
                }
            }
            if ( $comparisonFactorMin2 = 1000) $comparisonFactorMin2 = 0;

            $comparisonFactorMin3 = 1000;
            $comparisonFactorMax3 = 0;
            $comparisonFactorChange3 = 0;
            foreach ($comparisonFactor3 as $comparisonFactor) {
                if ($comparisonFactor->status == 1 && abs($comparisonFactor->adjust_percent) != 0) {
                    if (abs($comparisonFactor->adjust_percent) < $comparisonFactorMin3) {
                        $comparisonFactorMin3 = abs($comparisonFactor->adjust_percent);
                    }
                    if (abs($comparisonFactor->adjust_percent) > $comparisonFactorMax3) {
                        $comparisonFactorMax3 = abs($comparisonFactor->adjust_percent);
                    }
                    $comparisonFactorChange3 += 1;
                }
            }
            // tính số lần điều chỉnh
            foreach ($otherFactor3 as $otherFactor) {
                if ($otherFactor->status == 1 && abs($otherFactor->adjust_percent) != 0) {
                    if (abs($otherFactor->adjust_percent) < $comparisonFactorMin3) {
                        $comparisonFactorMin3 = abs($otherFactor->adjust_percent);
                    }
                    if (abs($otherFactor->adjust_percent) > $comparisonFactorMax3) {
                        $comparisonFactorMax3 = abs($otherFactor->adjust_percent);
                    }
                    $comparisonFactorChange3 += 1;
                }
            }
            if ( $comparisonFactorMin3 = 1000) $comparisonFactorMin3 = 0;
            // dd($otherFactor1);
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('6', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tổng số lần điều chỉnh (lần)', ['bold' => false], ['align' => 'left']);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($comparisonFactorChange1, null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($comparisonFactorChange2, null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText($comparisonFactorChange3, null, $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('7', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Biên độ điều chỉnh (%)', ['bold' => false], ['align' => 'left']);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(abs($comparisonFactorMin1) . '% - ' . abs($comparisonFactorMax1)  . '%', null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(abs($comparisonFactorMin2) . '% - ' . abs($comparisonFactorMax2)  . '%', null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(abs($comparisonFactorMin3) . '% - ' . abs($comparisonFactorMax3)  . '%', null, $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('8', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tổng giá trị điều chỉnh thuần (đ/' . $m2 . ')', ['bold' => false], ['align' => 'left']);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($tldc1, 0, ',', '.'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($tldc2, 0, ',', '.'), null, $cellHCentered);
            $table->addCell($columnWidthSecond, $cellVCentered)->addText(number_format($tldc3, 0, ',', '.'), null, $cellHCentered);

            //add from database
            $mgtb = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $baseAcronym . '_price');

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('9', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Thống nhất mức giá chỉ dẫn (đ/' . $m2 . ')', ['bold' => true], ['align' => 'left']);
            $table->addCell($columnWidthFourth, ['gridSpan' => 3, 'valign' => 'center'])->addText(number_format($mgtb, 0, ',', '.'), null, $cellHCentered);

            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellRowSpan)->addText('10', ['bold' => false], $cellHCentered);
            $table->addCell($columnWidthThird, ['gridSpan' => 2, 'valign' => 'center'])->addText('Làm tròn (đ/' . $m2 . '):', ['bold' => true], ['align' => 'left']);
            $table->addCell($columnWidthFourth, ['gridSpan' => 3, 'valign' => 'center'])->addText(number_format(CommonService::roundAssetPrice($asset, $mgtb), 0, ',', '.'), null, $cellHCentered);

            $phpWord->addTableStyle('Rowspan', $styleTableHide);

            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('❖ ', ['bold' => false]);
            $textRun->addText('Kết luận:', ['bold' => true]);

            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('     - Tổng hợp các nguồn thông tin, điều chỉnh các TSSS, mức giá chênh lệch với mức giá trung bình của các mức giá chỉ dẫn không quá ±15%.', ['bold' => false]);

            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('     - Mức giá sau khi điều chỉnh từ các TSSS về mức giá ước tính của TSTĐ dao động từ khoảng Mức giá chỉ dẫn thấp nhất ' . number_format($mgcdMin, 0, ',', '.') . 'đ/' . $m2 . ' đến Mức giá chỉ dẫn cao nhất ' . number_format($mgcdMax, 0, ',', '.') . 'đ/' . $m2 . '. Tổ thẩm định lựa chọn đơn giá đất sau điều chỉnh ' . $namePP . ' trên làm mức giá chỉ dẫn cho TSTĐ: ' . number_format($mgtb, 0, ',', '.') . 'đ/' . $m2 . '.');
            /* $textRun->addText('     - Mức giá sau khi điều chỉnh từ các TSSS về mức giá ước tính của TSTĐ dao động từ khoảng Mức giá chỉ dẫn thấp nhất ' . number_format($mgcdMin, 0, ',', '.') . 'đ/m');
            $textRun->addText('2', ['superScript' => true]);
            $textRun->addText(' đến Mức giá chỉ dẫn cao nhất ' . number_format($mgcdMax, 0, ',', '.') . 'đ/m');
            $textRun->addText('2', ['superScript' => true]);
            $textRun->addText('. Tổ thẩm định lựa chọn đơn giá đất sau điều chỉnh '.$namePP.' trên làm mức giá chỉ dẫn cho TSTĐ: ' . number_format($mgtb, 0, ',', '.') . 'đ/m');
            $textRun->addText('2', ['superScript' => true]);
            $textRun->addText('.'); */

            $mainPurpose = '';
            $isMultiPurpose = false;
            $isZoning = false;

            $gdtt = [];
            if ($asset->layer_cutting_procedure) {
                $mgtbr = CommonService::roundAssetPrice($asset, $asset->layer_cutting_price);
            } else {
                $mgtbr = CommonService::roundAssetPrice($asset, $mgtb);
            }
            foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                if ($item->is_transfer_facility) {
                    $mainPurpose = $item->landTypePurpose->acronym;
                    $firstText = "     + Đất " . $item->landTypePurpose->acronym . ": " . number_format($mgtb, 0, ',', '.') . " đ/m";
                    $secondText = "– Làm tròn: " . number_format(CommonService::roundAssetPrice($asset, $mgtb), 0, ',', '.') . " đ/m";
                    $textrun = $section->addTextRun('rightTab');
                    $textrun->addText($firstText);
                    $textrun->addText('2', ['superScript' => true]);
                    $textrun->addText(" \t ");
                    $textrun->addText($secondText);
                    $textrun->addText('2', ['superScript' => true]);

                    if ($asset->layer_cutting_procedure) {
                        $textRun = $section->addTextRun(['align' => 'both']);
                        $textRun->addText('     -    Mức giá sau khi điều chỉnh theo chiều sâu: ', []);
                        $firstText = "     + Đất " . $mainPurpose . ": " . number_format($asset->layer_cutting_price, 0, ',', '.') . " đ/m";
                        $secondText = "– Làm tròn: " . number_format($mgtbr, 0, ',', '.') . " đ/m";
                        $textRun = $section->addTextRun('rightTab');
                        $textRun->addText($firstText);
                        $textRun->addText('2', ['superScript' => true]);
                        $textRun->addText(" \t ");
                        $textRun->addText($secondText);
                        $textRun->addText('2', ['superScript' => true]);
                    }
                    $gdtt[$index] = $mgtbr;
                } else {
                    $isMultiPurpose = true;
                }
                if ($item->is_zoning) {
                    $isZoning = true;
                }
            }

            if ($isMultiPurpose) {
                if (!isset($asset->composite_land_remaning_slug) || ($asset->composite_land_remaning_slug == 'theo-chi-phi-chuyen-mdsd-dat')) {
                    foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                        if (!$item->is_transfer_facility && ($item->total_area - $item->planning_area ) > 0) {

                            $textRun = $section->addTextRun(['align' => 'both', 'keepNext' => true]);
                            $textRun->addText('❖ ', ['bold' => false]);
                            $textRun->addText('Xác định giá đất ' . $item->landTypePurpose->acronym . ' thị trường:', ['bold' => true]);

                            $cpcdmdsd = $baseUnitPrice - floatval($item->circular_unit_price);
                            if ($cpcdmdsd >= 0) {
                                $formular = ' = ' . number_format($mgtbr, 0, ',', '.') . ' - ' . number_format($cpcdmdsd, 0, ',', '.') . ' = ' . number_format($mgtbr - $cpcdmdsd, 0, ',', '.') . 'đ/' . $m2 . '';
                                $textRun = $section->addTextRun(['align' => 'both']);
                                $textRun->addText('     - Đơn giá đất ' . $item->landTypePurpose->acronym . ' thị trường = đơn giá đất ' . $mainPurpose . ' thị trường - chi phí chuyển MĐSD từ đất ' . $item->landTypePurpose->acronym . ' sang đất ' . $mainPurpose . $formular, ['bold' => false]);
                            } else {
                                $formular = ' = ' . number_format($mgtbr, 0, ',', '.') . ' + ' . number_format(abs($cpcdmdsd), 0, ',', '.') . ' = ' . number_format($mgtbr - $cpcdmdsd, 0, ',', '.') . 'đ/' . $m2 . '';
                                $textRun = $section->addTextRun(['align' => 'both']);
                                $textRun->addText('     - Đơn giá đất ' . $item->landTypePurpose->acronym . ' thị trường = đơn giá đất ' . $mainPurpose . ' thị trường + chi phí chuyển MĐSD từ đất ' . $mainPurpose . ' sang đất ' . $item->landTypePurpose->acronym . $formular, ['bold' => false]);
                            }

                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $donGiaDat = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_price');
                            $round = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_round');
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);

                            $firstText = "     + Đất " . $item->landTypePurpose->acronym . ": " . number_format($donGiaDat, 0, ',', '.') . " đ/m";
                            $secondText = "– Làm tròn: " . number_format($donGiaDatRound, 0, ',', '.') . " đ/m";
                            $textrun = $section->addTextRun('rightTab');
                            $textrun->addText($firstText);
                            $textrun->addText('2', ['superScript' => true]);
                            $textrun->addText(" \t ");
                            $textrun->addText($secondText);
                            $textrun->addText('2', ['superScript' => true]);

                            $gdtt[$index] = $donGiaDatRound;
                        }
                    }
                }

                if (isset($asset->composite_land_remaning_slug) && ($asset->composite_land_remaning_slug == 'theo-ty-le-gia-dat-co-so-chinh')) {
                    $sttTmp = 0;
                    foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                        if (!$item->is_transfer_facility) {
                            // dd ($item->landTypePurpose->acronym);
                            $textRun = $section->addTextRun(['align' => 'both', 'keepNext' => true]);
                            $textRun->addText('❖ ', ['bold' => false]);
                            $textRun->addText('Xác định giá đất ' . $item->landTypePurpose->acronym . ' thị trường:', ['bold' => true]);

                            $cpcdmdsd = $baseUnitPrice - floatval($item->circular_unit_price);
                            $resultTmp = number_format($mgtbr * $asset->composite_land_remaning_value / 100, 0, ',', '.');
                            $formular = '     Đất ' . $item->landTypePurpose->acronym . ' = ' . number_format($mgtbr, 0, ',', '.') . 'đ/' . $m2 . ' x ' . $asset->composite_land_remaning_value . '% = ' . $resultTmp . 'đ/' . $m2 . '';
                            if (!$sttTmp) {
                                $textRun = $section->addTextRun(['align' => 'both']);
                                $textRun->addText('     - Qua khảo sát thực tế tại khu vực thẩm định giá, TTĐ nhận định Đơn giá đất CHN thị trường bằng ' . $asset->composite_land_remaning_value . '% đơn giá đất ' . $baseAcronym . ' thị trường là phù hợp', ['bold' => false]);
                            }

                            $textRun = $section->addTextRun(['align' => 'both']);
                            $textRun->addText($formular, ['bold' => false]);

                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $donGiaDat = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_price');
                            $round = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_round');
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);

                            $firstText = "     + Đất " . $item->landTypePurpose->acronym . ": " . number_format($donGiaDat, 0, ',', '.') . " đ/m";
                            $secondText = "– Làm tròn: " . number_format($donGiaDatRound, 0, ',', '.') . " đ/m";
                            $textrun = $section->addTextRun('rightTab');
                            $textrun->addText($firstText);
                            $textrun->addText('2', ['superScript' => true]);
                            $textrun->addText(" \t ");
                            $textrun->addText($secondText);
                            $textrun->addText('2', ['superScript' => true]);

                            $gdtt[$index] = $donGiaDatRound;
                            $sttTmp++;
                        }
                    }
                }
                if (isset($asset->composite_land_remaning_slug) && ($asset->composite_land_remaning_slug == 'theo-phuong-phap-doc-lap')) {
                    foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                        if (!$item->is_transfer_facility) {
                            $textRun = $section->addTextRun(['align' => 'both', 'keepNext' => true]);
                            $textRun->addText('❖ ', ['bold' => false]);
                            $textRun->addText('Xác định giá đất ' . $item->landTypePurpose->acronym . ' thị trường:', ['bold' => true]);

                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $donGiaDat = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_price');
                            $round = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_round');
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            // $donGiaDatRound = CommonService::roundAssetPrice($donGiaDat, $round);

                            $firstText = "     + Đất " . $item->landTypePurpose->acronym . ": " . number_format($donGiaDat, 0, ',', '.') . " đ/m";
                            $secondText = "– Làm tròn: " . number_format($donGiaDatRound, 0, ',', '.') . " đ/m";
                            $textrun = $section->addTextRun('rightTab');
                            $textrun->addText($firstText);
                            $textrun->addText('2', ['superScript' => true]);
                            $textrun->addText(" \t ");
                            $textrun->addText($secondText);
                            $textrun->addText('2', ['superScript' => true]);

                            $textRun = $section->addTextRun(['align' => 'both']);
                            $textRun->addText('Chi tiết xem Bảng điều chỉnh Quyền sử dụng đất CLN kèm theo', ['bold' => false, 'italic' => true, 'color' => 'FF0000']);

                            $gdtt[$index] = $donGiaDatRound;
                        }
                    }
                }
            }

            if ($isZoning) {
                $textRun = $section->addTextRun(['align' => 'both', 'keepNext' => true]);
                $textRun->addText('❖ ', ['bold' => false]);
                $textRun->addText('Xác định giá đất vi phạm quy hoạch:', ['bold' => true]);

                if (!isset($asset->planning_violation_price_slug) || ($asset->planning_violation_price_slug == 'theo-gia-dat-qd-ubnd')) {
                    $sttTmp = 0;
                    foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                        if ($item->is_zoning) {
                            if (!$sttTmp) {
                                $textRun = $section->addTextRun(['align' => 'both']);
                                $textRun->addText('     - Phần diện tích đất thuộc ' . $item->type_zoning . ' (hạn chế khả năng sử dụng) nên '.mb_strtoupper($company->acronym).' ước tính theo đơn giá đất quyết định UBND:', ['bold' => false]);
                            }

                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $donGiaDat = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
                            $round = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            // if ($item->is_transfer_facility) {
                            //     $donGiaDatRound = CommonService::roundViolationAssetPrice($asset, $donGiaDat);
                            // } else {
                            //     $donGiaDatRound = CommonService::roundViolationCompositeAssetPrice($asset, $donGiaDat);
                            // }

                            $firstText = '     + Đất ' . ($item->landTypePurpose->acronym ?? '') . ', ' . (CommonService::mbUcfirst($item->positionType->description) ?? '') . ': ' . number_format($donGiaDat, 0, ',', '.') . 'đ//m';
                            $secondText = "– Làm tròn: " . number_format($donGiaDatRound, 0, ',', '.') . " đ/m";
                            $textrun = $section->addTextRun('rightTab');
                            $textrun->addText($firstText);
                            $textrun->addText('2', ['superScript' => true]);
                            $textrun->addText(" \t ");
                            $textrun->addText($secondText);
                            $textrun->addText('2', ['superScript' => true]);
                            //$textRun = $section->addTextRun(['align' => 'both']);
                            //$textRun->addText('     + Đất ' . ($item->landTypePurpose->acronym ?? '') . ', ' . (CommonService::mbUcfirst($item->positionType->description) ?? '') . ': ' . number_format($donGiaDat, 0, ',', '.') . 'đ/'.$m2, ['bold' => false]);
                            $sttTmp++;
                        }
                    }
                }
                if (isset($asset->planning_violation_price_slug) && ($asset->planning_violation_price_slug == 'theo-ty-le-gia-dat-thi-truong')) {
                    $sttTmp = 0;
                    foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                        if ($item->is_zoning) {
                            if (!$sttTmp) {
                                $textRun = $section->addTextRun(['align' => 'both']);
                                $textRun->addText('     - Phần diện tích đất vi phạm quy hoạch (hạn chế khả năng sử dụng) nên '.mb_strtoupper($company->acronym).' ước tính bằng ' . $asset->planning_violation_price_value . '% đơn giá đất theo thị trường:', ['bold' => false]);
                            }
                            $priceTmp = isset($gdtt[$index]) ? $gdtt[$index] : 0;
                            $resultTmp = number_format($priceTmp * $asset->planning_violation_price_value / 100, 0, ',', '.');
                            $formular = '     Đất ' . $item->landTypePurpose->acronym . ' = ' . number_format($priceTmp, 0, ',', '.') . 'đ/' . $m2 . ' x ' . $asset->planning_violation_price_value . '% = ' . $resultTmp . 'đ/' . $m2 . '';
                            $textRun = $section->addTextRun(['align' => 'both']);
                            $textRun->addText($formular, ['bold' => false]);

                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $donGiaDat = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
                            $round = CommonService::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            // if ($item->is_transfer_facility) {
                            //     $donGiaDatRound = CommonService::roundViolationAssetPrice($asset, $donGiaDat);
                            // } else {
                            //     $donGiaDatRound = CommonService::roundViolationCompositeAssetPrice($asset, $donGiaDat);
                            // }

                            /* if($item->is_transfer_facility || !isset($asset->composite_land_remaning_slug)||($asset->composite_land_remaning_slug!='theo-phuong-phap-doc-lap')) {
                                $donGiaDat = CommonService::roundAssetPrice($asset, $donGiaDat);
                            } */

                            $firstText = '     + Đất ' . ($item->landTypePurpose->acronym ?? '') . ', ' . (CommonService::mbUcfirst($item->positionType->description) ?? '') . ': ' . number_format($donGiaDat, 0, ',', '.') . 'đ//m';
                            $secondText = "– Làm tròn: " . number_format($donGiaDatRound, 0, ',', '.') . " đ/m";
                            $textrun = $section->addTextRun('rightTab');
                            $textrun->addText($firstText);
                            $textrun->addText('2', ['superScript' => true]);
                            $textrun->addText(" \t ");
                            $textrun->addText($secondText);
                            $textrun->addText('2', ['superScript' => true]);
                            //$textRun = $section->addTextRun(['align' => 'both']);
                            //$textRun->addText('     + Đất ' . ($item->landTypePurpose->acronym ?? '') . ', ' . (CommonService::mbUcfirst($item->positionType->description) ?? '') . ': ' . number_format($donGiaDat, 0, ',', '.') . 'đ/'.$m2, ['bold' => false]);
                            $sttTmp++;
                        }
                    }
                }
            }
        }

        //Footer
        $comName = mb_strtoupper($company->acronym);
        $createdName = isset($certificate->createdBy) ? CommonService::withoutAccents($certificate->createdBy->name) : '';
        if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $certificate->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        $reportID = 'HSTD_' . $certificate->id;
        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $cell = $table->addCell(4500);
        $textrun = $cell->addTextRun();
        $textrun->addText($comName  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID, array('size' => 8), array('align' => 'left'));
        $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right'));

        $reportUserName = CommonService::getUserReport();
        $reportName = '3_PL1' . '_' . $reportUserName . '_' . $reportID . '_' . $comName;
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $fileName = $reportName . '_' . $downloadTime . '_' . $downloadDate;

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'comparison_brief/' . $now->format('Y') . '/' . $now->format('m');
        if(!File::exists(storage_path('app/public/'. $path))){
            File::makeDirectory(storage_path('app/public/'. $path), 0755, true);
        }
        try {
            $objWriter->save(storage_path('app/public/'. $path. '/'. $fileName .'.docx'));
        } catch (\Exception $e) {
            throw $e;
        }
        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/'. $fileName .'.docx');
        $data['file_name'] = $fileName;
        return $data;
    }
}
