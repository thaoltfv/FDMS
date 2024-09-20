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
use App\Models\CertificateHasRealEstate;
use App\Models\CertificateApartment;
use App\Models\CertificateApartmentAppraisalBase;

class TBGiaThiet
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
            'indentParagraph',
            ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.23), 'firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]
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
            // ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.05)]];
            ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.13), 'firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.1)]];

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
            'align' => 'center'
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
        $document_date_string = "";
        if (isset($certificate->document_num)) {
            $document_date_string .=  $certificate->document_num;
        } else {
            $document_date_string .= '...';
        }
        if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
            $document_date = date_create($certificate->document_date);
            $document_date_string .= ' ngày ' . date_format($document_date, "d") . '/' . date_format($document_date, "m") . '/' . date_format($document_date, "Y");
        } else {
            $document_date_string .= ' ngày  .../.../...';
        }
        $textRunHead = $row4->addCell(3500, $cellVCentered)->addTextRun(['align' => 'left']);
        // $textRunHead->addText('Số: Theo số HĐ/TB-1 <w:br/>', null, ['align' => 'left']);
        $textRunHead->addText('Số: Theo số ' . ($certificate->document_num ? $certificate->document_num : 'HĐ/TB-1') . ' <w:br/>', null, ['align' => 'left']);
        $textRunHead->addText('“Về việc thông báo giả thiết đặc biệt của hồ sơ thẩm định giá tài sản.”', ['italic' => true, []]);
        $row4->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        if (isset($certificate->certificate_date) && !empty(trim($certificate->certificate_date))) {
            // $document_date = date_create($certificate->certificate_date);
            $document_date_string_2 = ' ngày ' . (intval(date_format($document_date, "d")) - 1 < 10 ? '0' . (intval(date_format($document_date, "d")) - 1) : intval(date_format($document_date, "d")) - 1) . ' tháng ' . date_format($document_date, "m") . ' năm ' . date_format($document_date, "Y");
            $row4->addCell(5700, $cellVCentered)->addText("TP Hồ Chí Minh, " . $document_date_string_2, ['italic' => true], $cellHCentered);
        } else {
            $row4->addCell(5700, $cellVCentered)->addText("TP Hồ Chí Minh, ngày " . '  ' . " tháng " . '  ' . " năm " . '    ', ['italic' => true], $cellHCentered);
        }
        // if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
        //     $document_date = date_create($certificate->document_date);
        //     $document_date_string = ' ngày ' . (intval(date_format($document_date, "d")) - 1 < 10 ? '0' . (intval(date_format($document_date, "d")) - 1) : intval(date_format($document_date, "d")) - 1) . ' tháng ' . date_format($document_date, "m") . ' năm ' . date_format($document_date, "Y");
        //     $row4->addCell(5700, $cellVCentered)->addText("TP Hồ Chí Minh, " . $document_date_string, ['italic' => true], $cellHCentered);
        // } else {
        //     $row4->addCell(5700, $cellVCentered)->addText("TP Hồ Chí Minh, ngày " . '  ' . " tháng " . '  ' . " năm " . '    ', ['italic' => true], $cellHCentered);
        // }

        $section->addText(
            "KÍNH GỬI: " . mb_strtoupper(htmlspecialchars($certificate->petitioner_name), 'UTF-8'),
            ['bold' => true, 'size' => '12'],
            ['align' => 'center', 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12), 'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10)]
        );
        $textRun = $section->addTextRun(['align' => 'center']);
        $textRun->addText('(', ['italic' => true]);
        $textRun->addText('Địa chỉ', ['underline' => 'single']);
        $textRun->addText(':');
        $textRun->addText($certificate->petitioner_address ? ' ' .  htmlspecialchars($certificate->petitioner_address) : '...', ['italic' => true]);
        $textRun->addText(')', ['italic' => true]);


        $textRun = $section->addTextRun('indentParagraph');
        $textRun->addText('Căn cứ khoản 5, Điều 10 tại Chuẩn mực TĐGVN về Cơ sở giá trị thẩm định giá được ban hành kèm theo', null);
        $textRun->addText(' Thông tư số 30/2024/TT-BTC, Thông tư ban hành các chuẩn mực thẩm định giá Việt Nam về quy tắc đạo đức nghề nghiệp thẩm định giá, phạm vi công việc thẩm định giá, cơ sở giá trị thẩm định giá, hồ sơ thẩm định giá của Bộ trưởng Bộ Tài chính ban hành ngày 16/5/2024;', ['bold' => true, 'italic' => true]);


        $isApartment = in_array('CC', $certificate->document_type ?? []);
        $addressHSTD = "";
        if ($certificate->realEstate && count($certificate->realEstate) > 0) {
            if ($isApartment) {
                foreach ($certificate->realEstate as $index => $item) {
                    if ($item->apartment) {
                        $addressHSTD .= ($index == 0 ?  htmlspecialchars($item->apartment->appraise_asset) . ' tại ' .  htmlspecialchars($item->apartment->full_address) : ' và ' . htmlspecialchars($item->apartment->appraise_asset)  . ' tại ' .  htmlspecialchars($item->apartment->full_address));
                        // $apartment = $item->apartment;
                        // $appraiseApproaches[$apartment->apartmentAppraisalBase->approach->id] = $apartment->apartmentAppraisalBase;
                    }
                }
                // foreach ($appraiseApproaches as $item) {
                //     array_push($appraiseMethodUsed, $item->methodUsed->name);
                // }
                // $appraiseMethodUsedStr = implode(', ', $appraiseMethodUsed);
                // $appraiseMethodUsedStr = mb_strtolower($appraiseMethodUsedStr, 'utf8');
            } else {
                foreach ($certificate->realEstate as $index => $item) {
                    if ($item->appraises) {
                        $addressHSTD .= ($index == 0 ?  htmlspecialchars($item->appraises->appraise_asset) . ' tại ' .  htmlspecialchars($item->appraises->full_address) : ' và ' . htmlspecialchars($item->appraises->appraise_asset)  . ' tại ' .  htmlspecialchars($item->appraises->full_address));
                        // $appraise = $item->appraises;
                        // $appraiseApproaches[$appraise->appraiseApproach->id] = $appraise;
                    }
                }
                // foreach ($appraiseApproaches as $item) {
                //     array_push($appraiseMethodUsed, $item->appraiseMethodUsed->name);
                // }
                // $appraiseMethodUsedStr = implode(', ', $appraiseMethodUsed);
                // $appraiseMethodUsedStr = mb_strtolower($appraiseMethodUsedStr, 'utf8');
            }
        }

        $section->addText('Căn cứ Hợp đồng cung cấp dịch vụ thẩm định giá tài sản số ' . $document_date_string . ' ký kết giữa ' . ($company->name ? htmlspecialchars($company->name) : 'Công ty TNHH Thẩm định giá NOVA ') . ' và ' . htmlspecialchars($certificate->petitioner_name) . ' về việc thẩm định giá tài sản là ' . htmlspecialchars($addressHSTD) . '.', null, 'indentParagraph');
        $section->addText('Căn cứ các Hồ sơ, tài liệu, dữ liệu do khách hàng cung cấp cho Công ty TNHH Thẩm định giá NOVA;', null, 'indentParagraph');
        $section->addText('Căn cứ các thông tin về đặc điểm pháp lý, kinh tế - kỹ thuật, thông tin về thị trường và các thông tin khác liên quan đến tài sản thẩm định giá.', null, 'indentParagraph');
        $section->addText('Công ty TNHH Thẩm định giá NOVA xin thông báo đến ' . htmlspecialchars($certificate->petitioner_name) . ' nội dung về Giả thiết đặc biệt của hồ sơ như sau:.', null, 'indentParagraph');
        $section->addText('❖ Giả thiết đặc biệt:', ['bold' => true], 'indentParagraph');
        $isApartment = in_array('CC', $certificate->document_type);
        if ($isApartment) {
            $apartment = CertificateHasRealEstate::where('certificate_id', $certificate->id)->first();
            if ($apartment) {
                $asset = CertificateApartment::where('real_estate_id', $apartment->real_estate_id)->first();
                if ($asset) {
                    $description = CertificateApartmentAppraisalBase::where('apartment_asset_id', $asset->id)->first();
                    if ($description) {
                        $giathiet = $description->description;
                    }
                }
            } else {
                $giathiet = $certificate->document_description;
            }
            $section->addText('    ' . str_replace("\n", '<w:br/>    ', htmlspecialchars($giathiet)), null, ['align' => 'left', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.23)]]);
        } else {
            $section->addText('    ' . str_replace("\n", '<w:br/>    ', htmlspecialchars(json_decode($certificate)->real_estate[0]->appraises->document_description)), null,  ['align' => 'left', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.23)]]);
        }
        $section->addText('Công ty TNHH Thẩm định giá Nova xin thông báo đến ' . htmlspecialchars($certificate->petitioner_name) . ' được biết, xem xét thống nhất, ký xác nhận các nội dung nêu trên để Công ty cho cấp, in phát hành chứng thư thẩm định giá, báo cáo thẩm định giá.', null, 'indentParagraph');
        $section->addText('Trân trọng thông báo, kính chào và hợp tác!', null, 'indentParagraph');


        // $section->addText('- ' . htmlspecialchars($certificate->document_description), null, 'indentParagraph');

        // $table3 = $section->addTable([
        //     'align' => JcTable::START,
        //     'width' => 100 * 50,
        //     'unit' => 'pct'
        // ]);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("❖", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Giả thiết đặc biệt", ['italic' => true, 'bold' => true], ['align' => 'left']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText(htmlspecialchars($certificate->document_description), [], ['align' => 'both']);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $positionName = "TỔNG GIÁM ĐỐC";
        $appraiserManager = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->name : $certificate->appraiserManager->name;
        if (isset($certificate->appraiserConfirm->name)) {
            $positionName = "KT. TỔNG GIÁM ĐỐC";
            // $cell32->addText($certificate->appraiserConfirm->appraisePosition->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        }
        $row = $table->addRow(300);
        $row->addCell(5000)->addText("", null, ['align' => 'center']);
        $row->addCell(5000)->addText($positionName, ['bold' => true], ['align' => 'center']);

        $row2 = $table->addRow(300);
        $row2->addCell(5000)->addText("Nơi nhận:", ['bold' => true, 'underline' => 'single'], 'indentParagraph');
        $row2->addCell(5000)->addText("", ['bold' => false], ['align' => 'center']);

        $row3 = $table->addRow(300);
        $row3->addCell(5000)->addText("- Như trên;", ['italic' => true], 'indentParagraph');
        $row3->addCell(5000)->addText("", ['bold' => false], ['align' => 'center']);

        $row4 = $table->addRow(300);
        $row4->addCell(5000)->addText("- Lưu hồ sơ TĐG;", ['italic' => true], 'indentParagraph');
        $row4->addCell(5000)->addText("", ['bold' => false], ['align' => 'center']);

        $row5 = $table->addRow(300);
        $row5->addCell(5000)->addText("", null, ['align' => 'center']);
        $row5->addCell(5000)->addText(mb_strtoupper($appraiserManager, 'UTF-8'), ['bold' => true], ['align' => 'center']);

        $section->addPageBreak();

        $section->addText('❖   XÁC NHẬN CỦA KHÁCH HÀNG YÊU CẦU THẨM ĐỊNH GIÁ', ['bold' => true]);
        $section->addText('Xác nhận đã được thông báo và thống nhất các nội dụng nêu trên. ', ['bold' => false]);
        $section->addText('....................................................................................................................................................................', ['bold' => false]);
        $section->addText('....................................................................................................................................................................', ['bold' => false]);
        $section->addText('....................................................................................................................................................................', ['bold' => false]);
        $section->addText('....................................................................................................................................................................', ['bold' => false]);
        $section->addText('....................................................................................................................................................................', ['bold' => false]);
        $section->addText('....................................................................................................................................................................', ['bold' => false]);

        // $table2 = $section->addTable([
        //     'align' => JcTable::START,
        //     'width' => 100 * 50,
        //     'unit' => 'pct'
        // ]);
        // $rowtb2 = $table2->addRow(300);
        // $rowtb2->addCell(5000)->addText("", null, ['align' => 'center']);
        // $rowtb2->addCell(5000)->addText("BÊN YÊU CẦU THẨM ĐỊNH GIÁ", ['bold' => true], ['align' => 'center']);
        // $rowtb2 = $table2->addRow(1000);
        // $rowtb2->addCell(5000)->addText("", null, ['align' => 'center']);
        // $rowtb2->addCell(5000)->addText("", ['bold' => true], ['align' => 'center']);
        // $rowtb2 = $table2->addRow(300);
        // $rowtb2->addCell(5000)->addText("", null, ['align' => 'center']);
        // $rowtb2->addCell(5000)->addText(mb_strtoupper(htmlspecialchars($certificate->petitioner_name), 'UTF-8'), ['bold' => true], ['align' => 'center']);



        $reportName = 'TBGTDB' . '_' . ($certificate->petitioner_name);
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
