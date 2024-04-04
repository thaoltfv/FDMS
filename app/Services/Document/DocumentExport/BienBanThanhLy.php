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

class BienBanThanhLy
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
    public function generateDocx($company, $certificate, $format, $appraises): array
    {

        $phpWord = new PhpWord();
        $this->setFormat($phpWord);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);
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
            'footerHeight' => 0,
            'marginTop' => Converter::inchToTwip(0.4),
            // 'marginBottom' => Converter::inchToTwip(0.28),
            'marginBottom' => Converter::inchToTwip(0),

            'marginRight' => Converter::inchToTwip(0.4),
            'marginLeft' => Converter::inchToTwip(0.8)
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
        $row2->addCell(3500, $cellVCentered)->addText('THẨM ĐỊNH GIÁ NOVA', ['bold' => true], $cellHCentered);
        $row2->addCell(1000, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);
        $row2->addCell(5700, $cellVCentered)->addText('Độc lập – Tự do - Hạnh phúc', ['bold' => true], $cellHCentered);
        $row3 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
        $row3->addCell(3500, $cellVCentered)->addText('Số: ' . (isset($certificate->document_num) ? $certificate->document_num  : ''), null, $cellHCentered);
        $row3->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        $row3->addCell(5700, $cellVCentered)->addText('----------o0o---------', null, $cellHCentered);

        $section->addText(
            "BIÊN BẢN THANH LÝ HỢP ĐỒNG THẨM ĐỊNH GIÁ",
            ['bold' => true, 'size' => '14'],
            ['align' => 'center']
        );
        if ($certificate->document_date) {
            $timestamp = strtotime($certificate->document_date);
            $day = date('d', $timestamp);
            $month = date('m', $timestamp);
            $year = date('Y', $timestamp);
            $formattedDateDocumentDate = "ngày $day tháng $month năm $year";
        } else {
            $formattedDateDocumentDate = '';
        }
        $section->addText(
            "(Hợp đồng số: " . (isset($certificate->document_num) ? $certificate->document_num  : '') .
                (isset($formattedDateDocumentDate) ? ' ' . $formattedDateDocumentDate : '') . ")",
            ['italic' => true, 'size' => '12'],
            ['align' => 'center']
        );

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);

        $row = $table->addRow();
        $row->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row->addCell(9700)->addText("Căn cứ Hợp đồng số: " . (isset($certificate->document_num) ? $certificate->document_num . ' '  : '') .
            $formattedDateDocumentDate, null, $indentleftSymbol);

        if ($certificate->certificate_date) {
            $timestamp = strtotime($certificate->certificate_date);
            $day = date('d', $timestamp);
            $month = date('m', $timestamp);
            $year = date('Y', $timestamp);
            $formattedDateCertificateDate = "ngày $day tháng $month năm $year";
        } else {
            $formattedDateCertificateDate = '';
        }
        $row2 = $table->addRow();
        $row2->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row2->addCell(9700)->addText("Căn cứ Chứng thư Thẩm định giá số: " . (isset($certificate->certificate_num) ? $certificate->certificate_num . ' '  : '') .
            $formattedDateCertificateDate, null, $indentleftSymbol);

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
        $row1->addCell(8000, $cellVTop)->addText($certificate->petitioner_name, ['bold' => true],  $alignBoth);

        $row2 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
        => false));
        $row2->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
        $row2->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row2->addCell(8000, $cellVTop)->addText($certificate->petitioner_address, null,  $alignBoth);

        $row3 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
        => false));
        $row3->addCell(1800, $cellVTop)->addText('-    Mã số thuế', null,  $alignBoth);
        $row3->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row3->addCell(8000, $cellVTop)->addText('', null,  $alignBoth);

        $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
        => false));
        $row4->addCell(1800, $cellVTop)->addText('-    Đại diện', null,  $alignBoth);
        $row4->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row4->addCell(8000, $cellVTop)->addText('', null,  $alignBoth);

        $row5 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false, 'spaceBefore' => 300));
        $row5->addCell(1800, $cellVTop)->addText('BÊN B', ['bold' => true,],  $alignBoth);
        $row5->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row5->addCell(8000, $cellVTop)->addText('CÔNG TY TNHH THẨM ĐỊNH GIÁ NOVA', ['bold' => true],  $alignBoth);

        $row6 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
        => false));
        $row6->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
        $row6->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row6->addCell(8000, $cellVTop)->addText('Số 728-730 Võ Văn Kiệt, Phường 1, Quận 5, TP.HCM', null,  $alignBoth);

        $row7 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row7->addCell(1800, $cellVTop)->addText('-    Điện thoại', null,  $alignBoth);
        $row7->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row7->addCell(8100, $cellVTop)->addText('(028) 3920 6779 – Fax: (028) 3920 6778', null,  $alignBoth);

        $row8 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row8->addCell(1800, $cellVTop)->addText('-   Mã số thuế', null,  $alignBoth);
        $row8->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row8->addCell(8000, $cellVTop)->addText('0314514140', null,  $alignBoth);


        $row9 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row9->addCell(1800, $cellVTop)->addText('-   Tài khoản số', null,  $alignBoth);
        $row9->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $row9->addCell(8000, $cellVTop)->addText('3101 00024 27729 tại Ngân hàng TMCP Đầu tư và Phát triển Việt Nam – CN Hồ Chí Minh – PGD Trần Hưng Đạo', null,  $alignBoth);

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
        $row10 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row10->addCell(1800, $cellVTop)->addText('-   Đại diện', null,  $alignBoth);
        $row10->addCell(100, $cellVTop)->addText(':', null,  $alignBoth);
        $textRun = $row10->addCell(8000, $cellVTop)->addTextRun($alignBoth);
        $textRun->addText('Ông ', ['bold' => false]);
        $textRun->addText($daidien, ['bold' => true]);
        $textRun->addText(' – Chức vụ:' . $chucvu, ['bold' => false]);
        $section->addText(
            "Bên A xác nhận đã tiếp nhận và nghiệm thu chứng thư Thẩm định giá số " . (isset($certificate->certificate_num) ? $certificate->certificate_num . ' '  : '') .
                $formattedDateDocumentDate . '. Hai bên thống nhất cùng tiến hành thanh lý Hợp đồng số: ' . (isset($certificate->document_num) ? $certificate->document_num . ' '  : '') .
                $formattedDateCertificateDate . '.',
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]
        );


        $alignCenter =
            ['align' => 'center'];
        if ((isset($certificate->apartmentAssetPrint) && count($certificate->apartmentAssetPrint) > 0) ||
            (isset($certificate->appraises) && count($certificate->appraises) > 0)
        ) {
            $table = $section->addTable([
                'borderSize' => 1,
                'align' => JcTable::START,
                'width' => 100 * 50,
                'unit' => 'pct'
            ]);
            $row1 = $table->addRow(100, array(
                'tblHeader' => false,
                'cantSplit' => true
            ));
            $row1->addCell(400, array('vMerge' => 'restart', 'valign' => 'center'))->addText('STT', ['bold' => true],  $alignCenter);
            $row1->addCell(3200, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Tài sản thẩm định giá', ['bold' => true], $alignCenter);
            $row1->addCell(3800, array('gridSpan' => 3,  'valign' => 'center'))->addText("Kết quả thẩm định giá", ['bold' => true], $alignCenter);
            $row1->addCell(1600, array('vMerge' => 'restart',  'valign' => 'center'))->addText("Giá dịch vụ đã bao gồm VAT(đồng)", ['bold' => true], $alignCenter);

            $row2 = $table->addRow();
            $row2->addCell(400, array('vMerge' => 'continue'));
            $row2->addCell(3200, array('vMerge' => 'continue'));
            $row2->addCell(1200, $cellVCentered)->addText('Số chứng thư', ['bold' => true],  $alignCenter);
            $row2->addCell(1200, $cellVCentered)->addText('Ngày', ['bold' => true], $alignCenter);
            $row2->addCell(1400, $cellVCentered)->addText('Tổng giá trị tài sản thẩm định giá', ['bold' => true], array(
                'align' => 'center'
            ));
            $row2->addCell(1600, array('vMerge' => 'continue'));
        }
        $total = 0;
        $isApartment = in_array('CC', $certificate->document_type ?? []);


        if ($isApartment) {
            foreach ($certificate->apartmentAssetPrint as $index => $item) {
                $row3 = $table->addRow();
                $total += $certificate->service_fee ?? 0;
                $textServiceFee = isset($certificate->service_fee) ? $this->formatNumberFunction($certificate->service_fee, 2, ',', '.') : '';
                $row3->addCell(400, $cellVCentered)->addText($index + 1, null,  $alignCenter);
                $cell = $row3->addCell(3200, $cellVCentered);
                $table = $cell->addTable();
                $row = $table->addRow();
                $row->addCell(100)->addText('');
                $row->addCell(3000)->addText($item->appraise_asset, null, $alignBoth);
                $row->addCell(100)->addText('');

                $cell = $row3->addCell(3200, $cellVCentered);
                $table = $cell->addTable();
                $row = $table->addRow();
                $row->addCell(100)->addText('');
                $row->addCell(3000)->addText((isset($certificate->document_num) ? $certificate->document_num . ' '  : ''), null, $alignBoth);
                $row->addCell(100)->addText('');

                $row3->addCell(1200, $cellVCentered)->addText(($certificate->certificate_date ? date('d/m/Y', strtotime($certificate->certificate_date)) : ''), null, $alignCenter);
                $row3->addCell(1400, $cellVCentered)->addText('Kèm theo CT', null, $alignCenter);
                $row3->addCell(1600, $cellVCentered)->addText($textServiceFee, null, $alignCenter);
            }
        } else {
            foreach ($certificate->appraises as $index => $item) {
                $row3 = $table->addRow();
                $total += $certificate->service_fee ?? 0;
                $textServiceFee = isset($certificate->service_fee) ? $this->formatNumberFunction($certificate->service_fee, 2, ',', '.') : '';
                $row3->addCell(400, $cellVCentered)->addText($index + 1, null,  $alignCenter);
                $cell = $row3->addCell(3200, $cellVCentered);
                $table = $cell->addTable();
                $row = $table->addRow();
                $row->addCell(100)->addText('');
                $row->addCell(3000)->addText($item->appraise_asset, null, $alignBoth);
                $row->addCell(100)->addText('');

                $cell = $row3->addCell(3200, $cellVCentered);
                $table = $cell->addTable();
                $row = $table->addRow();
                $row->addCell(100)->addText('');
                $row->addCell(3000)->addText((isset($certificate->document_num) ? $certificate->document_num . ' '  : ''), null, $alignBoth);
                $row->addCell(100)->addText('');

                $row3->addCell(1200, $cellVCentered)->addText(($certificate->certificate_date ? date('d/m/Y', strtotime($certificate->certificate_date)) : ''), null, $alignCenter);
                $row3->addCell(1400, $cellVCentered)->addText('Kèm theo CT', null, $alignCenter);
                $row3->addCell(1600, $cellVCentered)->addText($textServiceFee, null, $alignCenter);
            }
        }


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row4->addCell(3400, $cellVCentered)->addText('Tổng số tiền phải thanh toán', null,  $alignBoth);
        $row4->addCell(100, $cellVCentered)->addText(':', null,  $alignBoth);
        $row4->addCell(6300, $cellVCentered)->addText(isset($total) ? $this->formatNumberFunction($total, 2, ',', '.') . ' đồng' : '', ['bold' => true],  $alignBoth);

        $row5 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row5->addCell(9900, array('valign' => 'center', 'gridSpan' => 3))->addText('(Bằng chữ: ' . (isset($total)  ? ucfirst(CommonService::convertNumberToWords($total)) . ' đồng ./.' : '') . ')', ['italic' => true],  $alignCenter);

        $section->addText(
            "Hợp đồng số: " . (isset($certificate->document_num) ? $certificate->document_num . ' '  : '') .
                $formattedDateCertificateDate . ' 2024 được thanh lý khi bên A thanh toán hết số tiền phải thanh toán cho Bên B thì trách nhiệm và nghĩa vụ của hai bên được chấm dứt.',
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]
        );
        $section->addText(
            "Biên bản lập thành 04 bản, có giá trị pháp lý như nhau, bên A giữ 02 bản, bên B giữ 02 bản",
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]
        );

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
        $row2->addCell(4950)->addText("CHẤP HÀNH VIÊN", ['bold' => true], ['align' => 'center']);
        $row2->addCell(4950)->addText($chucvu, ['bold' => true], ['align' => 'center']);

        $row3 = $table->addRow(1000);
        $row3->addCell(4950)->addText("");
        $row3->addCell(4950)->addText("");

        $row4 = $table->addRow();
        $row4->addCell(4950)->addText('', ['bold' => true], ['align' => 'center']);
        $row4->addCell(4950)->addText($daidien, ['bold' => true], ['align' => 'center']);


        $filename = (isset($certificate->certificate_num) ? strstr($certificate->certificate_num, '/', true) : '');
        $reportName = 'Bien Ban Thanh Ly' . (isset($filename) ? '_CT' . htmlspecialchars($filename) : '');
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
