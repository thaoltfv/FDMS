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
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class KeHoachTDG
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

    public function generateDocx($company, $certificate, $format, $appraises, $priceEstimatePrint): array
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
            'footerHeight' => 370,
            'marginTop' => Converter::inchToTwip(0.7),
            'marginBottom' => Converter::inchToTwip(0.28),
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
        $row3->addCell(3500, $cellVCentered)->addText('----------o0o---------', null, $cellHCentered);
        $row3->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        $row3->addCell(5700, $cellVCentered)->addText('----------o0o---------', null, $cellHCentered);
        $styleImageLogo = [
            'height' => 33,
            'space' => [
                'line' => 1000,
            ],
        ];
        $imgName = env('STORAGE_IMAGES', 'images') . '/' . 'company_logo.png';
        $row5 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
        $row5->addCell(3500, $cellVCentered)->addImage(storage_path('app/public/' . $imgName), $styleImageLogo);;
        $row5->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        $row5->addCell(5700, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        $row4 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
        $row4->addCell(3500, $cellVCentered)->addText('', null, $cellHCentered);
        $row4->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
            $document_date = date_create($certificate->document_date);
            $document_date_string = ' ngày ' . date_format($document_date, "d") . ' tháng ' . date_format($document_date, "m") . ' năm ' . date_format($document_date, "Y");
            $row4->addCell(5700, $cellVCentered)->addText("TP Hồ Chí Minh, " . $document_date_string, ['italic' => true], $cellHCentered);
        } else {
            $row4->addCell(5700, $cellVCentered)->addText("TP Hồ Chí Minh, ngày " . '  ' . " tháng " . '  ' . " năm " . '    ', ['italic' => true], $cellHCentered);
        }

        $section->addText(
            "KÍNH GỬI: " . htmlspecialchars($certificate->petitioner_name),
            ['bold' => true, 'size' => '12'],
            ['align' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12), 'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10)]
        );
        $textRun = $section->addTextRun(['align' => 'center']);
        $textRun->addText('(', ['italic' => true]);
        $textRun->addText('Địa chỉ:', ['underline' => 'single']);
        $textRun->addText($certificate->petitioner_address ? htmlspecialchars($certificate->petitioner_address) : '...', ['italic' => true]);
        $textRun->addText(')', ['italic' => true]);

        $indentleftNumber =
            ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.05)]];
        $textRun = $section->addTextRun($indentleftNumber);
        $textRun->addText('Căn cứ khoản 6, Điều 3 tại Chuẩn mực TĐGVN về Phạm vi công việc thẩm định giá và khoản 5, Điều 10 tại Chuẩn mực TĐGVN về Cơ sở giá trị thẩm định giá được ban hành kèm theo', null);
        $textRun->addText('Thông tư số 30/2024/TT-BTC, Thông tư ban hành các chuẩn mực thẩm định giá Việt Nam về quy tắc đạo đức nghề nghiệp thẩm định giá, phạm vi công việc thẩm định giá, cơ sở giá trị thẩm định giá, hồ sơ thẩm định giá của Bộ trưởng Bộ Tài chính ban ành ngày ngày 16/5/2024;', ['bold' => true, 'italic' => true]);
        $document_date_string = "";

        if (isset($certificate->document_num)) {
            $document_date_string .= ' ' . $certificate->document_num;
        } else {
            $document_date_string .= '...';
        }
        if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
            $document_date = date_create($certificate->document_date);
            $document_date_string = ' ngày ' . date_format($document_date, "d") . '/' . date_format($document_date, "m") . '/' . date_format($document_date, "Y");
        } else {
            $document_date_string .= ' ngày  .../.../...';
        }

        $textRun = $section->addTextRun($indentleftNumber);
        $textRun->addText('Căn cứ Hợp đồng cung cấp dịch vụ thẩm định giá tài sản số ' . $document_date_string . ' ký kết giữa ' . ($company->name ? htmlspecialchars($company->name) : 'Công ty TNHH Thẩm định giá NOVA ') . ' và ' . htmlspecialchars($certificate->petitioner_name) . ' về việc thẩm định giá tài sản là ', null);
        $textRun->addText('Thông tư số 30/2024/TT-BTC, Thông tư ban hành các chuẩn mực thẩm định giá Việt Nam về quy tắc đạo đức nghề nghiệp thẩm định giá, phạm vi công việc thẩm định giá, cơ sở giá trị thẩm định giá, hồ sơ thẩm định giá của Bộ trưởng Bộ Tài chính ban hành ngày ngày 16/5/2024;', ['bold' => true, 'italic' => true]);

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row->addCell(9700)->addText("Bên yêu cầu thẩm định giá: " . htmlspecialchars($certificate->petitioner_name), null, $indentleftSymbol);

        $row2 = $table->addRow();
        $row2->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row2->addCell(9700)->addText("Tài sản thẩm định giá: " . $addressHSTD, null, $indentleftSymbol);

        $row3 = $table->addRow();
        $row3->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row3->addCell(9700)->addText("Mục đích Thẩm định giá: " . (isset($certificate->appraisePurpose) ? $certificate->appraisePurpose->name . '.' : ''), null, $indentleftSymbol);

        // $row4 = $table->addRow();
        // $row4->addCell(200)->addText(" -", null, ['align' => 'left']);
        // $row4->addCell(9700)->addText("Bên sử dụng Chứng thư thẩm định giá: " . htmlspecialchars($certificate->petitioner_name), null, $indentleftSymbol);

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('2.  Tổ thẩm định, các công việc dự kiến', ['bold' => true]);

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row->addCell(9700, array('gridSpan' => 2))->addText("Họ, tên người thực hiện hoạt động thẩm định giá: " . (isset($certificate->appraiser) ? $certificate->appraiser->name : ''), null, $indentleftSymbol);

        $row2 = $table->addRow();
        $row2->addCell(200)->addText(" ", null, ['align' => 'left']);
        $row2->addCell(9700, array('gridSpan' => 2))->addText("Họ, tên người thu thập thông tin: " .  (isset($certificate->appraiserPerform) ? $certificate->appraiserPerform->name : ''), null, $indentleftSymbol);

        $row3 = $table->addRow();
        $row3->addCell(200)->addText(" ", null, ['align' => 'left']);
        $row3->addCell(9700, array('gridSpan' => 2))->addText("Các công việc thực hiện (dự kiến): Thực hiện toàn bộ công việc Thẩm định giá theo các quy định pháp luật hiện hành: khảo sát hiện trạng tài sản, thu thập thông tin, ước tính giá trị tài sản thẩm định giá, lập hồ sơ thẩm định giá, ký tên Hồ sơ thẩm định giá.", null, $indentleftSymbol);

        $row4 = $table->addRow();
        $row4->addCell(200)->addText(" -", ['bgColor' => 'FFFF00'], ['align' => 'left']);
        $row4->addCell(9700, array('gridSpan' => 2))->addText("Thời gian thực hiện (dự kiến):", ['bgColor' => 'FFFF00'], $indentleftSymbol);

        $row4 = $table->addRow();
        $row4->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row4->addCell(9700, array('gridSpan' => 2))->addText("Các nội dung cần trưng cầu ý kiến chuyên gia (nếu có): Có/Không.", null, $indentleftSymbol);

        $row4 = $table->addRow();
        $row4->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row4->addCell(9700, array('gridSpan' => 2))->addText("Các yêu cầu hỗ trợ khác (nếu có): …………………………………………/Không đề xuất.", null, $indentleftSymbol);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row13 = $table->addRow();
        $row13->addCell(3300)->addText('Người duyệt', ['bold' => true], ['align' => 'center']);
        $row13->addCell(3300)->addText('Người kiểm tra', ['bold' => true], ['align' => 'center']);
        $row13->addCell(3300)->addText('Người lập', ['bold' => true], ['align' => 'center']);

        $row14 = $table->addRow();
        $row14->addCell(3300)->addText('Tổng Giám đốc', ['bold' => true], ['align' => 'center']);
        $row14->addCell(3300)->addText('Thẩm Định viên', ['bold' => true], ['align' => 'center']);
        $row14->addCell(3300)->addText('Chuyên viên thẩm định giá', ['bold' => true], ['align' => 'center']);

        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $table->addCell(9900, array('borderTopSize' => 1, 'borderTopColor' => '000000')) // Add a top border to the cell
            ->addPreserveText('Đc: 728-730 Võ Văn Kiệt, Phường 1, Quận 5, TP.HCM <w:br/>Tel: (028) 3920 6779   -  Fax: (028) 3920 6778<w:br/>Web: www.thamdinhnova.com - Email: thamdinhnova@gmail.com
                ', array('size' => 8), array('align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0, 'lineHeight' => 1.35));
        $reportUserName = CommonService::getUserReport();
        $reportName = 'KHTDG' . '_' . htmlspecialchars($certificate->petitioner_name);
        $reportName = str_replace(['%', '@', '!', '#', '&', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], ' ', $reportName); // replace invalid characters with underscore

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
        Log::info('Path', ['path' => $path, 'fileName' => $fileName]);
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
