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

class TBHanChe
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
            // ['align' => 'both', 'indentation' => ['left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.13), 'firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)]]

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
        $textRun->addText('Căn cứ khoản 6, Điều 3 tại Chuẩn mực TĐGVN về Phạm vi công việc thẩm định giá và khoản 5, Điều 10 tại Chuẩn mực TĐGVN về Cơ sở giá trị thẩm định giá được ban hành kèm theo', null);
        $textRun->addText(' Thông tư số 30/2024/TT-BTC, Thông tư ban hành các chuẩn mực thẩm định giá Việt Nam về quy tắc đạo đức nghề nghiệp thẩm định giá, phạm vi công việc thẩm định giá, cơ sở giá trị thẩm định giá, hồ sơ thẩm định giá của Bộ trưởng Bộ Tài chính ban ành ngày ngày 16/5/2024;', ['bold' => true, 'italic' => true]);
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

        $section->addText('Căn cứ Hợp đồng cung cấp dịch vụ thẩm định giá tài sản số ' . $document_date_string . ' ký kết giữa ' . ($company->name ? htmlspecialchars($company->name) : 'Công ty TNHH Thẩm định giá NOVA ') . ' và ' . htmlspecialchars($certificate->petitioner_name) . ' về việc thẩm định giá tài sản là  ' . htmlspecialchars($addressHSTD) . '.', null, 'indentParagraph');
        $section->addText('Căn cứ các Hồ sơ, tài liệu, dữ liệu do khách hàng cung cấp cho Công ty TNHH Thẩm định giá NOVA;', null, 'indentParagraph');
        $section->addText('Căn cứ các thông tin về đặc điểm pháp lý, kinh tế - kỹ thuật, thông tin về thị trường và các thông tin khác liên quan đến tài sản thẩm định giá.', null, 'indentParagraph');
        $section->addText('Công ty TNHH Thẩm định giá NOVA xin thông báo đến ' . htmlspecialchars($certificate->petitioner_name)  . ' các nội dung như sau:', null, 'indentParagraph');
        $textRun = $section->addTextRun();
        $textRun->addText('1.  Cơ sở giá trị thẩm định giá: ', ['bold' => true]);
        $textRun->addText('Cơ sở giá trị thị trường.', ['bold' => false]);
        $section->addText('2.   Những hạn chế và điều khoản loại trừ trách nhiệm đối với hồ sơ thẩm định giá', ['bold' => true]);
        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);

        $row = $table->addRow(300);
        $row->addCell(600)->addText("2.1.", ['bold' => true], ['align' => 'left']);
        $row->addCell(9300)->addText('Những hạn chế của hồ sơ thẩm định giá', ['bold' => true], ['align' => 'both']);

        $row = $table->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Bên yêu cầu thẩm định giá chịu trách nhiệm về tính chính xác, hợp pháp của các hồ sơ pháp lý, thông tin cung cấp cho Công ty TNHH Thẩm định giá Nova. Bên yêu cầu thẩm định giá trực tiếp hoặc ủy quyền hợp pháp cho người có đủ hiểu biết về tài sản thẩm định giá hướng dẫn chuyên viên của công ty TNHH Thẩm định giá Nova thực hiện khảo sát, thu thập thông tin về tài sản thẩm định giá. Công ty TNHH Thẩm định giá Nova không đối chiếu hồ sơ pháp lý khách hàng cung cấp với bản chính và không chịu trách nhiệm về chứng thư thẩm định giá trong trường hợp khách hàng cung cấp thông tin sai lệch dẫn tới sai lệch trong kết quả thẩm định giá.', ['bold' => false], ['align' => 'both']);

        $row = $table->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Hiện trạng của tài sản thẩm định giá được ghi nhận tại thời điểm khảo sát hiện trạng tài sản. Công ty TNHH Thẩm định giá NOVA không chịu trách nhiệm nếu có phát sinh các hư hỏng, phá bỏ, thay đổi kết cấu hiện trạng của tài sản hay thay đổi chủ sở hữu trong quá trình sử dụng sau thời điểm khảo sát hiện trạng tài sản thẩm định giá.', ['bold' => false], ['align' => 'both']);

        $row = $table->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Chứng thư thẩm định giá, báo cáo thẩm định giá chỉ có giá trị sử dụng trong thời gian hiệu lực theo đúng mục đích thẩm định giá gắn với đúng thông tin tài sản, số lượng tài sản tại Hợp đồng thẩm định giá. Bên yêu cầu hoặc bên thứ ba sử dụng kết quả thẩm định giá có trách nhiệm đối chiếu số lượng, đặc điểm tài sản đã mô tả trong báo cáo thẩm định giá với tài sản yêu cầu thẩm định giá trước khi sử dụng chứng thư thẩm định giá. Trường hợp có sự sai khác về số lượng, đặc điểm tài sản thì khách hàng hoặc bên thứ ba sử dụng kết quả thẩm định giá không được sử dụng chứng thư thẩm định giá và thông báo cho Công ty TNHH Thẩm định giá Nova phối hợp giải quyết.', ['bold' => false], ['align' => 'both']);

        $row = $table->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Chứng thư thẩm định giá, báo cáo thẩm định giá và các phụ lục kèm theo là những phần không thể tách rời trong quá trình sử dụng kết quả tư vấn thẩm định giá. Chứng thư thẩm định giá chỉ có giá trị sử dụng với bản chính, theo số lượng phát hành ghi trong chứng thư.', ['bold' => false], ['align' => 'both']);

        $row = $table->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Kết quả thẩm định giá là mức giá Công ty TNHH Thẩm định giá Nova tư vấn giá trị tài sản, được sử dụng làm một trong những căn cứ để Bên yêu cầu hoặc tổ chức, cá nhân có liên quan được ghi tại Hợp đồng thẩm định giá tham khảo, xem xét, phê duyệt giá của tài sản. Chứng thư thẩm định giá, báo cáo thẩm định giá không phải là văn bản bắt buộc bất cứ bên nào tham gia giao dịch phải thực hiện theo giá trị tư vấn.', ['bold' => false], ['align' => 'both']);

        $table2 = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);

        $row = $table2->addRow(300);
        $row->addCell(600)->addText("2.2.", ['bold' => true], ['align' => 'left']);
        $row->addCell(9300)->addText('Những điều khoản loại trừ trách nhiệm đối với hồ sơ thẩm định giá', ['bold' => true], ['align' => 'both']);

        $row = $table2->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Công ty TNHH Thẩm định giá Nova sẽ không chịu bất kỳ trách nhiệm nào đối với kết quả thẩm định giá, chứng thư thẩm định giá, báo cáo thẩm định giá nếu Bên yêu cầu thẩm định giá hoặc bên thứ ba sử dụng kết quả thẩm định giá có tên trong Hợp đồng thẩm định giá vi phạm một trong các hành vi sau đây: ', ['bold' => false], ['align' => 'both']);

        $row = $table2->addRow(300);
        $row->addCell(600)->addText("", null, ['align' => 'left']);
        $row->addCell(9300)->addText('+ Cố ý cung cấp thông tin sai lệch về tài sản thẩm định giá;', ['bold' => false], ['align' => 'both']);

        $row = $table2->addRow(300);
        $row->addCell(600)->addText("", null, ['align' => 'left']);
        $row->addCell(9300)->addText('+ Sử dụng chứng thư thẩm định giá đã hết hiệu lực; sử dụng chứng thư thẩm định giá không theo đúng mục đích thẩm định giá gắn với đặc điểm, số lượng tài sản thẩm định giá ghi tại hợp đồng thẩm định giá;', ['bold' => false], ['align' => 'both']);

        $row = $table2->addRow(300);
        $row->addCell(600)->addText("", null, ['align' => 'left']);
        $row->addCell(9300)->addText('+ Mua chuộc, hối lộ; câu kết, thỏa thuận để làm sai lệch giá trị tài sản thẩm định giá nhằm vụ lợi, trục lợi; thông đồng về giá, thẩm định giá.', ['bold' => false], ['align' => 'both']);

        $row = $table2->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Kết quả thẩm định giá trên chỉ có giá trị khi các bên tham gia tuân thủ và hoàn thành các điều khoản trong hợp đồng cung cấp dịch vụ thẩm định giá. Trong trường hợp khách hàng không thực hiện đầy đủ các nghĩa vụ được ghi trong Hợp đồng cung cấp dịch vụ thẩm định giá đã ký kết với  Công ty TNHH Thẩm định giá Nova thì Hợp đồng trên mặc nhiên vô hiệu; Chứng thư thẩm định giá, Báo cáo thẩm định giá kèm theo Hợp đồng trên sẽ không có giá trị pháp lý.', ['bold' => false], ['align' => 'both']);

        $row = $table2->addRow(300);
        $row->addCell(600)->addText("-", null, ['align' => 'left']);
        $row->addCell(9300)->addText('Người sử dụng chứng thư thẩm định giá hợp pháp chỉ là Bên yêu cầu thẩm định giá hoặc bên thứ ba sử dụng kết quả thẩm định giá được thể hiện trong Văn bản yêu cầu thẩm định giá hoặc Hợp đồng thẩm định giá. Các chủ thể khác sử dụng chứng thư thẩm định giá khi chưa có sự đồng ý bằng văn bản của Công ty TNHH Thẩm định giá Nova là hành vi không hợp pháp, kết quả thẩm định giá, chứng thư thẩm định giá, báo cáo thẩm định giá sẽ không có hiệu lực.', ['bold' => false], ['align' => 'both']);
        // $textRun->addText('   2.  Giả thiết đặc biệt: ', ['bold' => true]);
        // $textRun->addText('Phụ lục kèm theo.', ['bold' => false]);
        // $textRun = $section->addTextRun();
        // $textRun->addText('   3.  Những điều khoản loại trừ và hạn chế của thẩm định giá: ', ['bold' => true]);
        // $textRun->addText('Phụ lục kèm theo.', ['bold' => false]);
        $section->addText('Công ty TNHH Thẩm định giá NOVA xin thông báo đến ' . htmlspecialchars($certificate->petitioner_name) . ' được biết, để thống nhất, xác nhận các nội dung nêu trên.', null, 'indentParagraph');
        $section->addText('Trân trọng thông báo, kính chào và hợp tác!', null, 'indentParagraph');

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
        $row3->addCell(5000)->addText("- Như trên;", ['italic' => true],  'indentParagraph');
        $row3->addCell(5000)->addText("", ['bold' => false], ['align' => 'center']);

        $row4 = $table->addRow(300);
        $row4->addCell(5000)->addText("- Lưu hồ sơ TĐG;", ['italic' => true], 'indentParagraph');
        $row4->addCell(5000)->addText("", ['bold' => false], ['align' => 'center']);

        $row5 = $table->addRow(300);
        $row5->addCell(5000)->addText("", null, ['align' => 'center']);
        $row5->addCell(5000)->addText(mb_strtoupper($appraiserManager, 'UTF-8'), ['bold' => true], ['align' => 'center']);

        // $section->addPageBreak();

        // $section->addText('❖   XÁC NHẬN CỦA BÊN YÊU CẦU THẨM ĐỊNH GIÁ', ['bold' => true]);
        // $section->addText('Xác nhận đã được thông báo và thống nhất các nội dụng nêu trên. ', ['bold' => false]);

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

        // $section->addPageBreak();
        // $section->addText("PHỤ LỤC:", ['bold' => true], ['align' => 'center']);
        // $section->addText("CÁC GIẢ THIẾT, GIẢ THIẾT ĐẶC BIỆT VÀ", ['bold' => true], ['align' => 'center']);
        // $section->addText("NHỮNG LOẠI TRỪ, HẠN CHẾ CỦA THẨM ĐỊNH GIÁ:", ['bold' => true], ['align' => 'center']);


        // $table3 = $section->addTable([
        //     'align' => JcTable::START,
        //     'width' => 100 * 50,
        //     'unit' => 'pct'
        // ]);
        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("1.", ['bold' => true], ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Các giả thiết và giả thiết đặc của thẩm định giá", ['bold' => true], ['align' => 'left']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("❖", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Giả thiết", ['italic' => true], ['align' => 'left']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Không có.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("❖", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Giả thiết đặc biệt", ['italic' => true], ['align' => 'left']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Không có.", [], ['align' => 'both']);


        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("2.", ['bold' => true], ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Những hạn chế và loại trừ của thẩm định giá", ['bold' => true], ['align' => 'left']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("❖", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Những hạn chế và loại trừ của kết quả thẩm định giá", ['italic' => true], ['align' => 'left']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Kết quả thẩm định giá trong chứng thư này chỉ có đúng với tài sản có đặc điểm pháp lý, kinh tế – kỹ thuật, số lượng và hiện trạng như đã mô tả trong báo cáo thẩm định giá tại thời điểm và địa điểm thẩm định giá. Khách hàng / bên thứ ba sử dụng kết quả thẩm định giá có trách nhiệm đối chiếu, đặc điểm tài sản thẩm định giá đã mô tả trong báo cáo này với tài sản cần thẩm định giá của mình trước khi sử dụng chứng thư thẩm định giá. Trường hợp có sự sai khác về đặc điểm tài sản cần thẩm định giá thì khách hàng / bên thứ ba sử dụng kết quả thẩm định phải dừng ngay việc sử dụng chứng thư thẩm định giá và thông báo cho Công ty TNHH Thẩm định giá Nova.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Bên yêu cầu thẩm định giá chịu trách nhiệm về tính chính xác, hợp pháp của các hồ sơ pháp lý, thông tin cung cấp cho Công ty TNHH Thẩm định giá Nova. Bên yêu cầu thẩm định giá trực tiếp hoặc ủy quyền hợp pháp cho người có đủ hiểu biết về tài sản thẩm định giá hướng dẫn chuyên viên của công ty TNHH Thẩm định giá Nova thực hiện khảo sát, thu thập thông tin về tài sản thẩm định giá. Công ty TNHH Thẩm định giá Nova không đối chiếu hồ sơ pháp lý khách hàng cung cấp với bản chính, không chịu trách nhiệm về chứng thư thẩm định giá trong trường hợp khách hàng cung cấp thông tin sai lệch dẫn tới sai lệch trong kết quả thẩm định giá.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Hiện trạng của tài sản thẩm định giá được ghi nhận tại thời điểm khảo sát hiện trạng tài sản. Công ty TNHH Thẩm định giá NOVA không chịu trách nhiệm nếu có phát sinh các hư hỏng, phá bỏ, thay đổi kết cấu hiện trạng của tài sản hay thay đổi chủ sở hữu trong quá trình sử dụng sau thời điểm khảo sát hiện trạng tài sản thẩm định giá.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Chứng thư thẩm định giá được lập trong điều kiện thị trường bình thường tại thời điểm thẩm định giá. Công ty TNHH Thẩm định giá Nova không chịu trách nhiệm trong trường hợp giá cả thị trường của tài sản có biến động bất thường, bất khả kháng xảy ra tại thời điểm sau thời điểm thẩm định giá.", [], ['align' => 'both']);


        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("❖", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Những điều khoản loại trừ của chứng thư thẩm định giá", ['italic' => true], ['align' => 'left']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Chứng thư thẩm định giá, báo cáo thẩm định giá và các phụ lục kèm theo là những phần không thể tách rời trong quá trình sử dụng kết quả tư vấn thẩm định giá. Chứng thư thẩm định giá chỉ có giá trị sử dụng với bản chính, theo số lượng đã phát hành ghi trong chứng thư.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Chứng thư thẩm định giá có giá trị sử dụng trong thời gian có hiệu lực, theo mục đích thẩm định giá duy nhất đã thỏa thuận trong hợp đồng / giấy yêu cầu thẩm định giá gắn với đúng thông tin tài sản, số lượng tài sản tại hợp đồng thẩm định giá. Công ty TNHH Thẩm định giá Nova không chịu trách nhiệm trong trường hợp khách hàng / bên sử dụng kết quả thẩm định giá sử dụng sai mục đích, thời hiệu của chứng thư thẩm định giá.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Kết quả thẩm định giá trong chứng thư là mức giá Công ty TNHH Thẩm định giá Nova tư vấn để khách hàng hoặc bên thứ ba sử dụng kết quả thẩm định giá tham khảo, ra quyết định theo mục đích thẩm định giá đã yêu cầu. Chứng thư thẩm định giá không phải là văn bản bắt buộc bất cứ bên nào tham gia giao dịch phải thực hiện theo giá trị tư vấn.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Người sử dụng chứng thư thẩm định giá hợp pháp là khách hàng hoặc bên thứ ba đã được chỉ định trong giấy yêu cầu thẩm định giá / hợp đồng thẩm định giá. Các chủ thể khác sử dụng chứng thư thẩm định giá khi chưa có sự đồng ý bằng văn bản của Công ty TNHH Thẩm định giá Nova và khách hàng là hành vi bất hợp pháp.", [], ['align' => 'both']);

        // $rowtb3 = $table3->addRow(300);
        // $rowtb3->addCell(500)->addText("-", null, ['align' => 'left']);
        // $rowtb3->addCell(9400)->addText("Chứng thư thẩm định giá được sử dụng hợp pháp khi khách hàng thực hiện đầy đủ và toàn vẹn các thỏa thuận đã nêu trong hợp đồng tư vấn dịch vụ thẩm định giá tài sản. Công ty TNHH Thẩm định giá Nova không chịu trách nhiệm khi khách hàng cố ý sử dụng chứng thư thẩm định giá khi chưa hoàn tất nghĩa vụ thanh toán phí dịch vụ thẩm định giá.", [], ['align' => 'both']);


        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $table->addCell(9900)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 10), array('align' => 'center'));

        // $table->addCell(9900, array('borderTopSize' => 1, 'borderTopColor' => '000000')) // Add a top border to the cell
        //     ->addPreserveText('Đc: 728-730 Võ Văn Kiệt, Phường 1, Quận 5, TP.HCM <w:br/>Tel: (028) 3920 6779   -  Fax: (028) 3920 6778<w:br/>Web: www.thamdinhnova.com - Email: thamdinhnova@gmail.com
        //         ', array('size' => 8), array('align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0, 'lineHeight' => 1.35));


        $reportName = 'TBHCLT' . '_' . ($certificate->petitioner_name);
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
