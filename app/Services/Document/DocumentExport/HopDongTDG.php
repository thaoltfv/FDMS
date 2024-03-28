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

class HopDongTDG
{
    use ResponseTrait;
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


        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            // 'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.13)),

            'align' => 'both'
        ]);
        $styleSection = [
            // 'footerHeight' => 300,
            'marginTop' => Converter::inchToTwip(0.75),
            // 'marginBottom' => Converter::inchToTwip(.6),
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
        $row3->addCell(3500, $cellVCentered)->addText('Số: ' . $certificate->document_num, null, $cellHCentered);
        $row3->addCell(1000, $cellVCentered)->addText(
            '',
            ['bold' => true,],
            $cellHCentered
        );
        $row3->addCell(5700, $cellVCentered)->addText('', ['bold' => true,], $cellHCentered);

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
        $section->addText(
            "Hôm nay, ngày " . date('d') . " tháng " . date('m') . " năm " . date('Y') . " tại văn phòng Công ty TNHH Thẩm định giá Nova, chúng tôi gồm có:",
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
        $row1->addCell(8100, $cellVTop)->addText(': ' . $certificate->petitioner_name, ['bold' => true],  $alignBoth);

        $row2 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
        => false));
        $row2->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
        $row2->addCell(8100, $cellVTop)->addText(': ' . $certificate->petitioner_address, null,  $alignBoth);

        $row3 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row3->addCell(1800, $cellVTop)->addText('-    Số CCCD', null,  $alignBoth);
        $row3->addCell(8100, $cellVTop)->addText(': ' . $certificate->petitioner_identity_card, null,  $alignBoth);

        $row4 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row4->addCell(1800, $cellVTop)->addText('-    Số điện thoại', null,  $alignBoth);
        $row4->addCell(8100, $cellVTop)->addText(': ' . $certificate->petitioner_phone, null,  $alignBoth);


        $row5 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row5->addCell(1800, $cellVTop)->addText('BÊN B', ['bold' => true,],  $alignBoth);
        $row5->addCell(8100, $cellVTop)->addText(': CÔNG TY TNHH THẨM ĐỊNH GIÁ NOVA', ['bold' => true],  $alignBoth);

        $row6 = $table->addRow(100, array('tblHeader' => false, 'cantSplit'
        => false));
        $row6->addCell(1800, $cellVTop)->addText('-    Địa chỉ', null,  $alignBoth);
        $row6->addCell(8100, $cellVTop)->addText(': Số 728-730 Võ Văn Kiệt, Phường 1, Quận 5, TP.HCM', null,  $alignBoth);

        $row7 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row7->addCell(1800, $cellVTop)->addText('-    Điện thoại bàn', null,  $alignBoth);
        $row7->addCell(8100, $cellVTop)->addText(': (028) 3920 6779 – Fax: (028) 3920 6778', null,  $alignBoth);

        $row8 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row8->addCell(1800, $cellVTop)->addText('-   Mã số thuế', null,  $alignBoth);
        $row8->addCell(8100, $cellVTop)->addText(': 0314514140', null,  $alignBoth);


        $row9 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row9->addCell(1800, $cellVTop)->addText('-   Tài khoản số', null,  $alignBoth);
        $row9->addCell(8100, $cellVTop)->addText(': 3101 00024 27729 tại Ngân hàng TMCP Đầu tư và Phát triển Việt Nam – CN Hồ Chí Minh – PGD Trần Hưng Đạo', null,  $alignBoth);
        $row9 = $table->addRow(100, array('tblHeader' => false, 'cantSplit' => false));
        $row9->addCell(1800, $cellVTop)->addText('-   Đại diện', null,  $alignBoth);
        $textRun = $row9->addCell(8100, $cellVTop)->addTextRun($alignBoth);
        $textRun->addText(': Ông ', ['bold' => false]);
        $textRun->addText($certificate->appraiserManager ? $certificate->appraiserManager->name : '', ['bold' => true]);
        $textRun->addText(' – Chức vụ: Tổng Giám đốc', ['bold' => false]);
        $section->addText(
            "Sau khi thương lượng, hai bên đồng ý ký kết hợp đồng cung cấp dịch vụ thẩm định giá tài sản với các điều kiện và điều khoản như sau",
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.23)]]
        );

        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 1: ', ['bold' => true, 'underline' => 'single']);
        $textRun->addText('Nội dung công việc thực hiện', ['bold' => true]);

        $section->addText(
            "1.1. Bên A yêu cầu Bên B thực hiện việc tư vấn thẩm định giá tài sản cho Bên A, chi tiết cụ thể như sau: ",
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.16)]]
        );

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
        $textRun->addText('Tài sản thẩm định giá : Quyền sở hữu căn hộ ');
        $textRun->addText('(Theo Giấy chứng nhận quyền sử dụng đất quyền sở hữu nhà ở và tài sản khác gắn liền với đất số CK 096662 số vào sổ cấp GCN:CS23305/DA ngày 30/05/2018 do Sở Tài Nguyên và Môi Trường thành phố Hồ Chí Minh cấp).', ['italic' => true]);
        $table = $section->addTable([
            'borderSize' => 1,
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row1 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));

        $alignCenter =
            ['align' => 'center'];
        $row1->addCell(800, $cellVCentered)->addText('STT', null,  $alignCenter);
        $row1->addCell(7500, $cellVCentered)->addText('Hạng mục', null, $alignCenter);
        $row1->addCell(1600, $cellVCentered)->addText("Diện tích sàn (m\u{00B2})", null, $alignCenter);

        $addressHSTD = '';
        foreach ($certificate->appraises as $index => $item) {
            $addressHSTD = $item->full_address;
            if ($item->tangibleAssets) {
                foreach ($item->tangibleAssets as $index2 => $item2) {
                    $row2 = $table->addRow(100, array(
                        'tblHeader' => false,
                        'cantSplit' => false
                    ));
                    $row2->addCell(800, $cellVTop)->addText('-', null,  $alignCenter);
                    $row2->addCell(7500, $cellVTop)->addText(' Quyền sở hữu căn hộ' . ($index2 > 0 ? ' ' . ($index2 + 1) . ' ' : ''), null, ['align' => 'left']);
                    $row2->addCell(1600, $cellVTop)->addText($item2->total_construction_base . ' ', null, ['align' => 'right']);
                }
            }
        }

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
        $row1->addCell(2600, $cellVTop)->addText('Địa điểm thẩm định giá', null,  $alignBoth);
        $row1->addCell(6100, $cellVTop)->addText(': ' . $addressHSTD, null,  $alignBoth);

        $appraise_date_formatted = 'Tháng ' . date('m/Y', strtotime($certificate->appraise_date));
        $row2 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row2->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row2->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row2->addCell(2600, $cellVTop)->addText('Thời điểm thẩm định giá ', null,  $alignBoth);
        $row2->addCell(6100, $cellVTop)->addText(': ' . $appraise_date_formatted . '.', null,  $alignBoth);

        $row3 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row3->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row3->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row3->addCell(2600, $cellVTop)->addText('Mục đích thẩm định giá', null,  $alignBoth);
        $row3->addCell(6100, $cellVTop)->addText(': ' . (isset($certificate->appraisePurpose) ? $certificate->appraisePurpose->name . '.' : ''), null,  $alignBoth);

        $row4 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row4->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row4->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row4->addCell(3000, $cellVTop)->addText('Phương pháp thẩm định giá', null, ['align' => 'left']);
        $row4->addCell(5700, $cellVTop)->addText(': Phương pháp so sánh quy định theo Tiêu chuẩn thẩm định giá Việt Nam.', null,  $alignBoth);

        $row5 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row5->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row5->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row5->addCell(3800, $cellVTop)->addText('Bên sử dụng kết quả thẩm định giá', null, ['align' => 'left']);
        $row5->addCell(4900, $cellVTop)->addText(': ' . $certificate->petitioner_name . '.', null,  $alignBoth);

        $row6 = $table->addRow(100, array(
            'tblHeader' => false,
            'cantSplit' => false
        ));
        $row6->addCell(1100, $cellVTop)->addText('➢', null,  ['align' => 'right']);
        $row6->addCell(100, $cellVTop)->addText('', null,  ['align' => 'right']);
        $row6->addCell(3900, $cellVTop)->addText('Số lượng chứng thư Bên A yêu cầu', null, ['align' => 'left']);
        $row6->addCell(4800, $cellVTop)->addText(': 02 bản chính bằng tiếng Việt.', null,  $alignBoth);

        $section->addText(
            "1.2. Bên B đồng ý thực hiện tư vấn thẩm định giá tài sản nêu trên cho Bên A.",
            null,
            ['align' => 'both', 'indentation' => ['firstLine' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.16)]]
        );


        $textRun = $section->addTextRun(['align' => 'both']);
        $textRun->addText('Điều 2: ', ['bold' => true, 'underline' => 'single']);
        $textRun->addText('Tiến trình thực hiện', ['bold' => true]);


        $table = $section->addTable([
            'align' => JcTable::START,
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        $row = $table->addRow();
        $row->addCell(600)->addText("2.1.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên A yêu cầu bên B thẩm định tài sản bằng điện thoại, bằng văn bản yêu cầu thẩm định.", null, ['align' => 'both']);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.2.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Bên A cung cấp đầy đủ hồ sơ pháp lý của tài sản và hướng dẫn người của Bên B thẩm định hiện trạng tài sản tại hiện trường. Bên A thanh toán đợt 1 cho bên B làm chi phí để bên B cử cán bộ và phương tiện phối hợp với bên A đi khảo sát thu thập thông tin và thẩm định hiện trường. Mức chi phí trên do hai bên thỏa thuận tùy thuộc vào loại tài sản và vị trí tài sản và được tính là chi phí không hoàn lại. Hình thức chi bằng chuyển khoản hoặc thanh toán tiền mặt tại thời điểm hiện trường.", null, ['align' => 'both']);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.3.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Tối đa 03 ngày làm việc, kể từ ngày bên B đã nhận đủ hồ sơ pháp lý và đi thẩm định hiện trường hoàn tất, bên B sẽ cử người gọi điện thoại thông báo kết quả sơ bộ cho bên A. Nếu bên A đồng ý phát hành chứng thư thẩm định giá thì bên A thanh toán đủ 100% phí thẩm định cho bên B, sau đó bên B sẽ cấp chứng thư cho bên A. ", null, ['align' => 'both']);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.4.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Sau khi nhận được kết quả sơ bộ mà bên A yêu cầu ngừng dịch vụ thì bên B sẽ hủy toàn bộ hồ sơ theo Quy trình lưu trữ nội bộ, Bên B sẽ không hoàn trả lại số tiền đã nhận của Bên A. ", null, ['align' => 'both']);

        $row = $table->addRow();
        $row->addCell(600)->addText("2.5.", null, ['align' => 'right']);
        $row->addCell(9300)->addText("Trường hợp Bên A có nhu cầu thẩm định giá bổ sung các tài sản ngoài danh mục thì các bên sẽ thỏa thuận cụ thể về thời gian, quy trình, mức phí thẩm định đối với các tài sản phát sinh. ", null, ['align' => 'both']);

        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $cell = $table->addCell(4500);
        $textrun = $cell->addTextRun();
        // $textrun->addText($comName  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID, array('size' => 8), $alignBoth);
        $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right'));

        $reportUserName = CommonService::getUserReport();
        $reportName = 'HDTDG' . '_' . htmlspecialchars($certificate->petitioner_name);
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
