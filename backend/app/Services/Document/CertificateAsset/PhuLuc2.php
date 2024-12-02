<?php

namespace App\Services\Document\CertificateAsset;

use App\Http\ResponseTrait;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use App\Contracts\BuildingPriceRepository;
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class PhuLuc2
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
    public function generateDocx($object, $company, $appraises, $format, $buildingPriceRepository)
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(2),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(2),
            'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5), 'right' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5))
        ]);
        $styleTable = [
            'borderSize' => 1,
            'align' => JcTable::START
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
            'marginTop' => 500,
            'marginBottom' => 500,
            'marginRight' => 1000,
            'marginLeft' => 1000
        ];
        $keepNext = ['keepNext' => true];

        $this->setFormat($phpWord);
        $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';
        $rowHeader = array('tblHeader' => true);
        $section = $phpWord->addSection($styleSection);
        // $table = $section->addTable('$styleTable');
        // $table->addRow(1000);
        // $cell = $table->addCell(2000, ['valign' => 'center']);
        // // if (($data = @file_get_contents($company->link)) !== false) {
        //     //$cell->addImage($company->link, ['width' => 50, 'height' => 50]);
        //     $cell->addImage(
        //         public_path("uploads/img/company_logo.png"),
        //         array(
        //             'width'         => 54,
        //             'height'        => 33,
        //             'wrappingStyle' => 'behind',
        //             'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
        //             'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
        //             'posVertical'    => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
        //             'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
        //             'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
        //         )
        //     );
        // // }
        // $cell = $table->addCell(4000, ['valign' => 'center']);
        // $cell->addText(CommonService::formatCompanyName($company), ['bold' => true], ['align' => 'left']);
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
        $section->addText('PHỤ LỤC 2 KÈM THEO BÁO CÁO THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);
        $section->addText('NHÀ CỬA VẬT KIẾN TRÚC', ['italic' => true, 'size' => 14], ['align' => 'center']);

        $onlyOneAsset = (count($appraises) > 1) ? false : true;
        foreach ($appraises as $key => $appraise) {

            if ($onlyOneAsset) {
                $textRun = $section->addTextRun(['align' => 'both']);
                $textRun->addText('     ' . ($key + 1) . '. Tài sản thẩm định :', ['bold' => true, 'size' => 14]);
            } else {

                $textRun = $section->addTextRun('Heading2');
                $textRun->addText('Tài sản thẩm định ' . ($key + 1) . ': ', ['bold' => true, 'size' => 14]);
            }
            if ($appraise->assetType->description == "ĐẤT TRỐNG") {
                $textRun->addText('Tài sản thẩm định không có công trình xây dựng', ['size' => 13, 'bold' => false]);
                continue;
            }

            $textRun->addText('Công trình xây dựng tọa lạc tại ' . ($appraise->ward ? $appraise->ward->name : ''), ['size' => 13, 'bold' => false]);
            $textRun->addText(', ' . ($appraise->district ? $appraise->district->name : ''), ['size' => 13, 'bold' => false]);
            $textRun->addText(', ' . ($appraise->province ? $appraise->province->name : ''), ['size' => 13, 'bold' => false]);

            $section->addText('❖ Về nguyên giá của nhà cửa, vật kiến trúc:', ['bold' => true, 'size' => 13], ['align' => 'left']);
            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('- Giá trị công trình xây dựng ' . mb_strtoupper($company->acronym) . ' căn cứ vào đặc điểm kết cấu, kiến trúc, khẩu độ, chiều cao, công năng sử dụng, vật liệu sử dụng….trên cơ sở những thông tin, tài liệu thu thập và phương pháp thẩm định giá được lựa chọn tại phần 3, mục VIII của Báo cáo này, mức giá ước tính như sau:');

            // $section->addTextBreak(1);

            $section->addText('BẢNG TỔNG HỢP THÔNG TIN TSTĐG VÀ TSSS', null, ['align' => 'center']);
            $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
            $cellRowContinue = array('vMerge' => 'continue');
            $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
            $cellHCentered = array('align' => 'center');
            $cellVCentered = array('valign' => 'center');

            $constructionCompany1 = $appraise->constructionCompany[0] ?? null;
            $constructionCompany2 = $appraise->constructionCompany[1] ?? null;
            $constructionCompany3 = $appraise->constructionCompany[2] ?? null;

            $buildingType = isset($appraise->tangibleAssets[0]) ? CommonService::mbUcfirst($appraise->tangibleAssets[0]->buildingType->description) : '';
            $usefulYear = isset($appraise->tangibleAssets[0]) ? CommonService::mbUcfirst($appraise->tangibleAssets[0]->duration) : '';

            $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
            $table = $section->addTable($styleTable);
            $table->addRow(400, $rowHeader);
            $table->addCell(2000, $cellRowSpan)->addText('Tên tài sản', ['bold' => true], $cellHCentered);
            $table->addCell(500, $cellRowSpan)->addText('Đvt', ['bold' => true], $cellHCentered);
            $table->addCell(1500, $cellRowSpan)->addText(isset($constructionCompany1->constructionCompany) ? $constructionCompany1->constructionCompany->name : '', ['bold' => true], $cellHCentered);
            $table->addCell(1500, $cellRowSpan)->addText(isset($constructionCompany2->constructionCompany) ? $constructionCompany2->constructionCompany->name : '', ['bold' => true], $cellHCentered);
            $table->addCell(1500, $cellRowSpan)->addText(isset($constructionCompany3->constructionCompany) ? $constructionCompany3->constructionCompany->name : '', ['bold' => true], $cellHCentered);
            $table->addCell(1500, $cellRowSpan)->addText('Đơn giá Quyết định', ['bold' => true], $cellHCentered);
            $table->addCell(2000, $cellRowSpan)->addText('Đơn giá ước tính', ['bold' => true], $cellHCentered);
           
            $remainQualitySlug = 'trung-binh-cong';
            $appraisalCLCL = $appraise->appraisal_clcl;
            if (!empty($appraisalCLCL)) {
                $remainQualitySlug = $appraisalCLCL->slug_value;
            }
            foreach($appraise->tangibleAssets as $tangibleAsset){
                $unitPrice1 = isset($tangibleAsset->constructionCompany[0]) ? $tangibleAsset->constructionCompany[0]->unit_price_m2 : 0;
                $unitPrice2 = isset($tangibleAsset->constructionCompany[1]) ? $tangibleAsset->constructionCompany[1]->unit_price_m2 : 0;
                $unitPrice3 = isset($tangibleAsset->constructionCompany[2]) ? $tangibleAsset->constructionCompany[2]->unit_price_m2 : 0;
                $table->addRow();
                // $table->addCell(2000, $cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->buildingType->description), ['bold' => true], $cellHCentered);
                $table->addCell(2000, $cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), ['bold' => true], $cellHCentered);
                $table->addCell(500, $cellRowSpan)->addText($m2, ['bold' => true], $cellHCentered);
                $table->addCell(1500, $cellRowSpan)->addText(number_format($unitPrice1, 0, ',', '.'), ['bold' => false], $cellHCentered);
                $table->addCell(1500, $cellRowSpan)->addText(number_format($unitPrice2, 0, ',', '.'), ['bold' => false], $cellHCentered);
                $table->addCell(1500, $cellRowSpan)->addText(number_format($unitPrice3, 0, ',', '.'), ['bold' => false], $cellHCentered);
                // $dgqd = isset($tangibleAsset) ? $buildingPriceRepository->getAverageBuildPriceV2($appraise->tangibleAssets[0]) : 0;
                $dgqd = isset($tangibleAsset->total_desicion_average) ? $tangibleAsset->total_desicion_average : 0;
                $table->addCell(1500, $cellRowSpan)->addText(number_format($dgqd, 0, ',', '.'), ['bold' => false], $cellHCentered);
                $unitPrice = round(($unitPrice1 + $unitPrice2 + $unitPrice3) / 3, -4, PHP_ROUND_HALF_DOWN);
                if ($unitPrice) {
                    $table->addCell(2000, $cellRowSpan)->addText(number_format($unitPrice, 0, ',', '.'), ['bold' => false], $cellHCentered);
                } else {
                    $table->addCell(2000, $cellRowSpan)->addText("Không biết", ['bold' => false], $cellHCentered);
                }
            }


            $section->addText('❖ Chất lượng còn lại nhà cửa, vật kiến trúc: ', ['bold' => true, 'size' => 13], ['align' => 'left']);

            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('- Căn cứ theo biên bản kiểm kê và kết quả khảo sát hiện trạng. ' . mb_strtoupper($company->acronym) . ' đánh giá chất lượng còn lại của công trình xây dựng như sau:');
            $countTangible = count($appraise->tangibleAssets);

            if ($remainQualitySlug == 'trung-binh-cong' || $remainQualitySlug == 'tuoi-doi') {
                $section->addText('✔ Phương pháp 1: Phương pháp tuổi đời (PP1):', ['bold' => true, 'size' => 13, 'italic' => true], ['align' => 'left']);

                $table = $section->addTable($styleTable);
                $table->addRow(400, $rowHeader);
                $table->addCell(3000, $cellRowSpan)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext) );
                // $table->addCell(1000, $cellRowSpan)->addText('Cấp', ['bold' => true], $cellHCentered);
                $table->addCell(1500, $cellRowSpan)->addText('Năm sử dụng', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(2250, $cellRowSpan)->addText('Thời gian đã sử dụng', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(2250, $cellRowSpan)->addText('Niên hạn theo qui định', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowSpan)->addText('CLCL (%)', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $level = '';

                $stt = 1;
                foreach($appraise->tangibleAssets as $tangibleAsset){
                    // if (isset($tangibleAsset->buildingCategory->description)) {
                    //     $level = CommonService::mbUcfirst($tangibleAsset->buildingCategory->description);
                    // }
                    // if (isset($tangibleAsset->rate->description)) {
                    //     $level = CommonService::mbUcfirst($tangibleAsset->rate->description);
                    // }
                    // if (isset($tangibleAsset->aperture->description)) {
                    //     $level = CommonService::mbUcfirst($tangibleAsset->aperture->description);
                    // }
                    $startUsingYear = $tangibleAsset->start_using_year ?? '';
                    $usingYear = '';
                    if ($startUsingYear != '') {
                        $usingYear = Carbon::now()->year - (int)$startUsingYear;
                    }
                    $usefulYear = isset($tangibleAsset->duration) ? CommonService::mbUcfirst($tangibleAsset->duration) : '';

                    $clcl = $tangibleAsset->remaining_quality ?? 0;
                    $table->addRow();
                    $table->addCell(3000, $cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), null,($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    // $table->addCell(1000, $cellRowSpan)->addText($level, ['bold' => false], $cellHCentered);
                    $table->addCell(1500, $cellRowSpan)->addText($startUsingYear, ['bold' => false], ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(2250, $cellRowSpan)->addText($usingYear, ['bold' => false], ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(2250, $cellRowSpan)->addText($usefulYear, ['bold' => false], ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(1500, $cellRowSpan)->addText($clcl, ['bold' => false], ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));

                    $stt ++;
                }
            }
            
            if ($remainQualitySlug == 'trung-binh-cong' || $remainQualitySlug == 'chuyen-gia') {

                $section->addText('✔ Phương pháp 2: Phương pháp chuyên gia (PP2): ', ['bold' => true, 'size' => 13, 'italic' => true], ['align' => 'left']);
                $table = $section->addTable($styleTable);
                $table->addRow(400, $rowHeader);
                $table->addCell(1500, $cellRowSpan)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(7500, ['gridSpan' => 10, 'valign' => 'center'])->addText('Phần kết cấu chính', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowSpan)->addText('CLCL (%)', ['bold' => true], array_merge($cellHCentered, $keepNext));

                $table->addRow();
                $table->addCell(1500, $cellRowContinue)->addText(null,null,['keepNext' => true]);
                $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Móng, cột', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tường', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Nền, sàn', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Kết cấu mái', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Mái', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowContinue)->addText(null,null,['keepNext' => true]);

                $table->addRow();
                $table->addCell(1500, $cellRowContinue)->addText(null,null,['keepNext' => true]);
                $table->addCell(750, $cellVCentered)->addText('p', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('h', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('p', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('h', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('p', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('h', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('p', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('h', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('p', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(750, $cellVCentered)->addText('h', null,  array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellVCentered)->addText('H= Σ ph / Σ p', null,  array_merge($cellHCentered, $keepNext));

                $stt =1;
                foreach($appraise->tangibleAssets as $tangibleAsset){
                    $p1 = $tangibleAsset->comparisonTangibleFactor->p1 ?? 0;
                    $h1 = $tangibleAsset->comparisonTangibleFactor->h1 ?? 0;
                    $p2 = $tangibleAsset->comparisonTangibleFactor->p2 ?? 0;
                    $h2 = $tangibleAsset->comparisonTangibleFactor->h2 ?? 0;
                    $p3 = $tangibleAsset->comparisonTangibleFactor->p3 ?? 0;
                    $h3 = $tangibleAsset->comparisonTangibleFactor->h3 ?? 0;
                    $d4 = $tangibleAsset->comparisonTangibleFactor->d4 ?? 0;
                    $h4 = $tangibleAsset->comparisonTangibleFactor->h4 ?? 0;
                    $p5 = $tangibleAsset->comparisonTangibleFactor->p5 ?? 0;
                    $h5 = $tangibleAsset->comparisonTangibleFactor->h5 ?? 0;

                    $table->addRow();
                    $table->addCell(1500, $cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($p1 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($h1 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($p2 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($h2 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($p3 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($h3 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($d4 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($h4 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($p5 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $table->addCell(750, $cellVCentered)->addText($h5 . '%', null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $clcl2 = ($p1 + $p2 + $p3 + $d4 + $p5) != 0 ? round(($p1 * $h1 + $p2 * $h2 + $p3 * $h3 + $d4 * $h4 + $p5 * $h5) / ($p1 + $p2 + $p3 + $d4 + $p5), 0) : 0;
                    $table->addCell(1500, $cellRowSpan)->addText($clcl2, null, ($stt = $countTangible) ? $cellHCentered : array_merge($cellHCentered, $keepNext));
                    $stt++;
                }
            }
            if ($remainQualitySlug == 'trung-binh-cong') {

                $section->addText('✔ Chất lượng còn lại lựa chọn:', ['bold' => true, 'size' => 13], ['align' => 'left', 'keepNext' => true]);
                $table = $section->addTable($styleTable);
                $table->addRow(400, $rowHeader);
                $table->addCell(3000, $cellRowSpan)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowSpan)->addText('Năm sử dụng', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowSpan)->addText('Theo PP1', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowSpan)->addText('Theo PP2', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowSpan)->addText('Theo bình quân', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellRowSpan)->addText('CLCL lựa chọn', ['bold' => true], array_merge($cellHCentered, $keepNext));

                foreach($appraise->tangibleAssets as $tangibleAsset){
                    $startUsingYear = $tangibleAsset->start_using_year ?? '';
                    $usingYear = '';
                    if ($startUsingYear != '') {
                        $usingYear = Carbon::now()->year - (int)$startUsingYear;
                    }
                    $clcl = $tangibleAsset->remaining_quality ?? 0;

                    $p1 = $tangibleAsset->comparisonTangibleFactor->p1 ?? 0;
                    $h1 = $tangibleAsset->comparisonTangibleFactor->h1 ?? 0;
                    $p2 = $tangibleAsset->comparisonTangibleFactor->p2 ?? 0;
                    $h2 = $tangibleAsset->comparisonTangibleFactor->h2 ?? 0;
                    $p3 = $tangibleAsset->comparisonTangibleFactor->p3 ?? 0;
                    $h3 = $tangibleAsset->comparisonTangibleFactor->h3 ?? 0;
                    $d4 = $tangibleAsset->comparisonTangibleFactor->d4 ?? 0;
                    $h4 = $tangibleAsset->comparisonTangibleFactor->h4 ?? 0;
                    $p5 = $tangibleAsset->comparisonTangibleFactor->p5 ?? 0;
                    $h5 = $tangibleAsset->comparisonTangibleFactor->h5 ?? 0;
                    $clcl2 = ($p1 + $p2 + $p3 + $d4 + $p5) != 0 ? round(($p1 * $h1 + $p2 * $h2 + $p3 * $h3 + $d4 * $h4 + $p5 * $h5) / ($p1 + $p2 + $p3 + $d4 + $p5), 0) : 0;

                    $table->addRow();
                    $table->addCell(3000, $cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), null, array_merge($cellHCentered, $keepNext));
                    $table->addCell(1500, $cellRowSpan)->addText($startUsingYear, ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell(1500, $cellRowSpan)->addText($clcl, ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell(1500, $cellRowSpan)->addText($clcl2, ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $table->addCell(1500, $cellRowSpan)->addText(round(($clcl + $clcl2) / 2), ['bold' => false], array_merge($cellHCentered, $keepNext));
                    $clclChoosed = CommonService::getClclChoosed($tangibleAsset, $appraise->appraisal_clcl);
                    $table->addCell(1500, $cellRowSpan)->addText($clclChoosed, ['bold' => false], array_merge($cellHCentered, $keepNext));
                }
            }
            if ( ($key < (count($appraises) - 1)) && ($appraises[$key+1]->assetType->description != "ĐẤT TRỐNG")) {
                $section->addPageBreak();
            }
        }

        //Footer
        $comName = mb_strtoupper($company->acronym);
        $createdName = isset($object->createdBy) ? CommonService::withoutAccents($object->createdBy->name) : '';
        if (isset($object->document_date) && !empty(trim($object->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $object->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        $reportID = 'HSTD_' . $object->id;
        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $cell = $table->addCell(4500);
        $textrun = $cell->addTextRun();
        $textrun->addText($comName  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID, array('size' => 8), array('align' => 'left'));
        $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right'));

        $reportUserName = CommonService::getUserReport();
		$reportName = '4_PL2' . '_' . $reportUserName . '_' . $reportID . '_' . $comName;
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
