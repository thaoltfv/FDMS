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
        $row4 = $table->addRow(400, array('tblHeader' => false, 'cantSplit' => false));
        $row4->addCell(3500, $cellVCentered)->addText('Số: ' . (isset($certificate->certificate_num) ? $certificate->certificate_num  : ''), null, $cellHCentered);
        $row4->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        $row4->addCell(5700, $cellVCentered)->addText("Tp. Hồ Chí Minh, ngày " . '  ' . " tháng " . '  ' . " năm " . '    ', ['italic' => true], $cellHCentered);

        $section->addText(
            "KẾ HOẠCH THẨM ĐỊNH GIÁ",
            ['bold' => true, 'size' => '12'],
            ['align' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12), 'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10)]
        );

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('1.  Những thông tin chung về Khách hàng yêu cầu và Tài sản thẩm định giá', ['bold' => true]);
        $isApartment = in_array('CC', $certificate->document_type ?? []);
        // $addressHSTD = 'Bất động sản là ';
        $addressHSTD = '';
        if (isset($priceEstimatePrint)) {
            foreach ($priceEstimatePrint as $index => $item) {
                $addressHSTD .=  ($index == 0 ?  htmlspecialchars($item->appraise_asset) : 'và ' . htmlspecialchars($item->appraise_asset));
            }
        } else {
            if ($isApartment) {
                foreach ($certificate->apartmentAssetPrint as $index => $item) {
                    $addressHSTD .= ($index == 0 ?  htmlspecialchars($item->appraise_asset) : 'và ' . htmlspecialchars($item->appraise_asset));
                }
            } else {
                foreach ($certificate->appraises as $index => $item) {
                    $addressHSTD .=  ($index == 0 ?  htmlspecialchars($item->appraise_asset) : 'và ' . htmlspecialchars($item->appraise_asset));
                }
            }
        }

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row->addCell(9700)->addText("Khách hàng yêu cầu thẩm định giá: " . htmlspecialchars($certificate->petitioner_name), null, $indentleftSymbol);

        $row2 = $table->addRow();
        $row2->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row2->addCell(9700)->addText("Tài sản thẩm định giá: Giá trị " . $addressHSTD, null, $indentleftSymbol);

        $row3 = $table->addRow();
        $row3->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row3->addCell(9700)->addText("Mục đích Thẩm định giá: " . (isset($certificate->appraisePurpose) ? $certificate->appraisePurpose->name . '.' : ''), null, $indentleftSymbol);

        $row4 = $table->addRow();
        $row4->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row4->addCell(9700)->addText("Bên sử dụng Chứng thư thẩm định giá: " . htmlspecialchars($certificate->petitioner_name), null, $indentleftSymbol);

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('2.  Phương thức, cách thức tiến hành thẩm định giá', ['bold' => true]);

        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row->addCell(9700, array('gridSpan' => 2))->addText("Phương thức thẩm định giá: Thực hiện toàn bộ quy trình Thẩm định giá theo quy định pháp luật hiện hành:  khảo sát hiện trạng tài sản, thu thập thông tin, ước tính giá trị tài sản thẩm định giá, lập và ký tên Hồ sơ thẩm định giá.", null, $indentleftSymbol);

        $row2 = $table->addRow();
        $row2->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row2->addCell(9700, array('gridSpan' => 2))->addText("Phương pháp thẩm định giá: Phương pháp so sánh.", null, $indentleftSymbol);

        $row3 = $table->addRow();
        $row3->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row3->addCell(9700, array('gridSpan' => 2))->addText("Nguồn thông tin: dự kiến thu thập các thông tin giao dịch thực tế trên thị trường, trên báo chí, trên mạng internet và một số nguồn thông tin khác nếu có; phân tích, xử lý thông tin và nhận định hoặc biện luận lựa chọn thông tin phù hợp.", null, $indentleftSymbol);

        $row4 = $table->addRow();
        $row4->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row4->addCell(9700, array('gridSpan' => 2))->addText("Tiến độ thực hiện công việc dự kiến như sau:", null, $indentleftSymbol);

        $surveyTime = "";

        if (isset($certificate->survey_time) && !empty(trim($certificate->survey_time))) {
            $survey_time = date_create($certificate->survey_time);
            // $surveyTime =  'Ngày ' . $survey_time->format('d') . " tháng " . $survey_time->format('m') . " năm " . $survey_time->format('Y') . ' lúc ' . $survey_time->format('H') . ' giờ ' . $survey_time->format('i') . ' phút';
            $surveyTime =   $survey_time->format('d') . "/" . $survey_time->format('m') . "/" . $survey_time->format('Y');
        }
        $row5 = $table->addRow();
        $row5->addCell(200)->addText("", null, ['align' => 'left']);
        $row5->addCell(1500)->addText("o", null, ['align' => 'right']);
        $row5->addCell(8200)->addText("Khảo sát hiện trạng tài sản: " .  $surveyTime, null, $indentleftSymbol);

        // $row6 = $table->addRow();
        // $row6->addCell(200)->addText("", null, ['align' => 'left']);
        // $row6->addCell(1500)->addText("o", null, ['align' => 'right']);
        // $row6->addCell(8200)->addText("Thu thập thông tin thị trường:", null, $indentleftSymbol);



        $row7 = $table->addRow();
        $row7->addCell(200)->addText("", null, ['align' => 'left']);
        $row7->addCell(1500)->addText("o", null, ['align' => 'right']);
        $row7->addCell(8200)->addText("Lập hồ sơ và báo cáo Thẩm định giá: " . ($certificate->document_date ? date('d/m/Y', strtotime($certificate->document_date)) : ''), null, $indentleftSymbol);

        $row8 = $table->addRow();
        $row8->addCell(200)->addText("", null, ['align' => 'left']);
        $row8->addCell(1500)->addText("o", null, ['align' => 'right']);
        $row8->addCell(8200)->addText("Cấp chứng thư Thẩm định giá: " . ($certificate->certificate_date ? date('d/m/Y', strtotime($certificate->certificate_date)) : ''), null, $indentleftSymbol);

        $row9 = $table->addRow();
        $row9->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row9->addCell(9700, array('gridSpan' => 2))->addText(
            "Họ, tên người có trách nhiệm tham gia khảo sát, lập biên bản hiện trạng tài sản, thu thập thông tin, lập và ký tên Hồ sơ thẩm định giá:" .
                (isset($certificate->appraiser) ? ' Thẩm định viên ' . $certificate->appraiser->name . ',' : '') .
                (isset($certificate->appraiserPerform) ? ' Chuyên viên thẩm định  ' . $certificate->appraiserPerform->name . '.' : ''),
            null,
            $indentleftSymbol
        );

        $row10 = $table->addRow();
        $row10->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row10->addCell(9700, array('gridSpan' => 2))->addText("Phương tiện cần thiết: Di chuyển bằng xe máy, ghi ảnh bằng điện thoại cá nhân.", null, $indentleftSymbol);

        $row11 = $table->addRow();
        $row11->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row11->addCell(9700, array('gridSpan' => 2))->addText("Các nội dung cần trưng cầu ý kiến chuyên gia (nếu có): Không đề xuất.", null, $indentleftSymbol);

        $row12 = $table->addRow();
        $row12->addCell(200)->addText(" -", null, ['align' => 'left']);
        $row12->addCell(9700, array('gridSpan' => 2))->addText("Các yêu cầu hỗ trợ khác (nếu có): Không đề xuất.", null, $indentleftSymbol);

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
