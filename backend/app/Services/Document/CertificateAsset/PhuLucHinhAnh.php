<?php

namespace App\Services\Document\CertificateAsset;

use App\Http\ResponseTrait;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class PhuLucHinhAnh
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
        $this->setFormat($phpWord);
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
			'footerHeight'=>300,
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(42.5),
			'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(42.5),
			'marginRight' => 1000,
			'marginLeft' => 1000
        ];
        $rowHeader = array('tblHeader' => true);

        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellHCentered = array('align' => 'center');
        $cellVCentered = array('valign' => 'center');

		$width2 = \PhpOffice\PhpWord\Shared\Converter::cmToPoint(8.2);
		$height2 = \PhpOffice\PhpWord\Shared\Converter::cmToPoint(5.5);


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
        $section->addText('PHỤ LỤC ẢNH TÀI SẢN THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);

        if (isset($certificate->certificate_num) && !empty(trim($certificate->certificate_num))) {
            $section->addText('(Kèm theo báo cáo Thẩm định giá số ' . $certificate->certificate_num . '/BC-ĐNI, ngày ' . date("d/m/Y", strtotime($certificate->certificate_date)) . ')', ['italic' => true, 'size' => 12], ['align' => 'center']);
        } else {
            $section->addText('(Kèm theo báo cáo Thẩm định giá số       /BC-ĐNI, ngày ' . date("d/m/Y", strtotime($certificate->certificate_date)) . ')', ['italic' => true, 'size' => 12], ['align' => 'center']);
        }

        $section->addTextBreak(1);
        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Khách hàng yêu cầu TĐG: ', ['bold' => true, 'size' => 13, 'underLine' => true]);
        $textRun->addText($certificate->petitioner_name ?? '', ['bold' => true, 'size' => 13]);
        // $textRun = $section->addTextRun(['align' => 'both']);
        // $textRun->addText('Tài sản thẩm định giá: ', ['bold' => true, 'size' => 13, 'underLine' => true]);

        $onlyOneAsset = (count($assets) > 1) ? false : true;
        if ($onlyOneAsset == false) {
            $textRun = $section->addTextRun(['align' => 'both']);
            $textRun->addText('Tài sản thẩm định giá: ', ['bold' => true, 'size' => 13, 'underLine' => true]);
            foreach ($assets as $key => $asset) {
                if (($key + 1) == 1) {
                    $textRun->addText($asset->appraise_asset ?? '', ['size' => 13]);
                } else {
                    $textRun->addText(', ' . $asset->appraise_asset ?? '', ['size' => 13]);
                }
            }
        }

        foreach ($assets as $key => $asset) {
            if (($key + 1) > 1) {
                $section->addPageBreak();
            }
            $result = null;
            foreach ($asset->pic as $value) {
                if (isset($value->picType)) {
                    $result[$value->picType->description][] = $value;
                }
            }


            if ($onlyOneAsset) {
                $textRun = $section->addTextRun(['align' => 'both']);
                $textRun->addText('     ' . ($key + 1) . '. Tài sản thẩm định: ', ['bold' => true, 'size' => 13]);
            } else {
                $textRun = $section->addTextRun('Heading2');
                $textRun->addText('Tài sản thẩm định ' . ($key + 1) . ': ', ['bold' => true, 'size' => 13]);
            }

            $textRun->addText($asset->appraise_asset ?? '', ['size' => 13,'bold'=>false]);
            if ($onlyOneAsset) {
                $section->addText('     ' . ($key + 1) . '.1. Sơ đồ vị trí tài sản thẩm định giá: ', ['bold' => true, 'size' => 13]);
            } else {
                $section->addTitle('Sơ đồ vị trí tài sản thẩm định giá ' . ($key + 1) . ': ',3);
                // $section->addText('     ' . ($key + 1) . '.1. Sơ đồ vị trí tài sản thẩm định giá ' . ($key + 1) . ': ', ['bold' => true, 'size' => 13]);
            }

            $phpWord->addTableStyle('Colspan Rowspan', $styleTableImage);
            $table = $section->addTable($styleTableImage);
            $table->addRow(800, ['align' => 'center']);
            $cell = $table->addCell(10000, ['align' => 'center']);
            if (isset($result['HÌNH BẢN ĐỒ'][0]->link)) {
                $cell->addImage(isset($result['HÌNH BẢN ĐỒ'][0]->link) ? $result['HÌNH BẢN ĐỒ'][0]->link : '', ['width' => 480, 'height' => 185, 'align' => 'center']);
            }

            if ($onlyOneAsset) {
                $section->addText('     ' . ($key + 1) . '.2. Hình ảnh hiện trạng tài sản thẩm định giá : ', ['bold' => true, 'size' => 13]);
            } else {
                // $section->addText('     ' . ($key + 1) . '.2. Hình ảnh hiện trạng tài sản thẩm định giá ' . ($key + 1) . ': ', ['bold' => true, 'size' => 13]);
                $section->addTitle('Hình ảnh hiện trạng tài sản thẩm định giá ' . ($key + 1) . ': ', 3);
            }

            $phpWord->addTableStyle('Colspan Rowspan', $styleTableImage);

            if (isset($result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'])) {
                $table = $section->addTable($styleTableImage);

                for ($i = 0; $i < count($result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ']); $i += 2) {
                    $table->addRow(800, ['align' => 'center']);
                    $cell = $table->addCell(10, $cellColSpan);
                    $cell->addText('', null, ['align' => 'center', 'keepNext' => true]);
                    $cell = $table->addCell(5000, ['align' => 'center']);
                    if (isset($result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link)) {
                        $cell->addImage(isset($result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link) ? $result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link : '', ['width' => $width2, 'height' => $height2, 'align' => 'left']);
                    }
                    $cell = $table->addCell(5000, ['align' => 'center']);
                    if (isset($result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link)) {
                        $cell->addImage(isset($result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link) ? $result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link : '', ['width' => $width2, 'height' => $height2, 'align' => 'right']);
                    }
                }
                $table->addRow(null, ['align' => 'center']);
                $cell = $table->addCell(10, $cellColSpan);
                $cell->addText('', null, ['align' => 'center']);
                $cell = $table->addCell(10000, $cellColSpan);
                $cell->addText('Đường tiếp giáp TSTĐG.', null, ['align' => 'center']);
            }

            if (isset($result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'])) {
                $table = $section->addTable($styleTableImage);

                for ($i = 0; $i < count($result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ']); $i += 2) {
                    $table->addRow(800, ['align' => 'center']);
                    $cell = $table->addCell(10, $cellColSpan);
                    $cell->addText('', null, ['align' => 'center', 'keepNext' => true]);
                    $cell = $table->addCell(5000, ['align' => 'center']);
                    if (isset($result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link)) {
                        $cell->addImage(isset($result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link) ? $result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link : '', ['width' => $width2, 'height' => $height2, 'align' => 'left']);
                    }
                    $cell = $table->addCell(5000, ['align' => 'center']);
                    if (isset($result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link)) {
                        $cell->addImage(isset($result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link) ? $result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link : '', ['width' => $width2, 'height' => $height2, 'align' => 'right']);
                    }
                }
                $table->addRow(null, ['align' => 'center']);
                $cell = $table->addCell(10, $cellColSpan);
                $cell->addText('', null, ['align' => 'center']);
                $cell = $table->addCell(10000, $cellColSpan);
                $cell->addText('Hiện trạng tổng thể TSTĐG.', null, ['align' => 'center']);
            }

            if (isset($result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'])) {
                $table = $section->addTable($styleTableImage);

                for ($i = 0; $i < count($result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ']); $i += 2) {
                    $table->addRow(800, ['align' => 'center']);
                    $cell = $table->addCell(10, $cellColSpan);
                    $cell->addText('', null, ['align' => 'center', 'keepNext' => true]);
                    $cell = $table->addCell(5000, ['align' => 'center']);
                    if (isset($result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link)) {
                        $cell->addImage(isset($result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link) ? $result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'][$i]->link : '', ['width' => $width2, 'height' => $height2, 'align' => 'left']);
                    }
                    $cell = $table->addCell(5000, ['align' => 'center']);
                    if (isset($result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link)) {
                        $cell->addImage(isset($result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link) ? $result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'][$i + 1]->link : '', ['width' => $width2, 'height' => $height2, 'align' => 'right']);
                    }
                }
                $table->addRow(null, ['align' => 'center']);
                $cell = $table->addCell(10, $cellColSpan);
                $cell->addText('', null, ['align' => 'center']);
                $cell = $table->addCell(10000, $cellColSpan);
                $cell->addText('Hiện trạng chi tiết tài sản thẩm định.', null, ['align' => 'center']);
            }
        }

        //Footer
        $comName = mb_strtoupper($company->acronym);
        $createdName = CommonService::withoutAccents($certificate->createdBy->name);
        if(isset($certificate->document_date)&&!empty(trim($certificate->document_date))) {
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
		$reportName = '5_PL3' . '_' . $reportUserName . '_' . $reportID . '_' . $comName;
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
