<?php

namespace App\Services\Document;

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

class BaoCao
{
    use ResponseTrait;
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
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 0) //, 'spaceBefore' => 300, 'spaceAfter' => 100
        );
        $phpWord->addTitleStyle(
            2,
            array('size' => '13', 'bold' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 1) //, 'spaceBefore' => 200, 'spaceAfter' => 100
        );
        $phpWord->addTitleStyle(
            3,
            array('size' => '13', 'bold' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 2) //, 'spaceBefore' => 150, 'spaceAfter' => 100
        );

        $phpWord->addParagraphStyle(
            'leftTab',
            array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('left', 5000)))
        );
    }
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function generateDocx($company, $certificate, $appraises, $format): array
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);
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

        $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';

        $indentFistLine = ['indentation' => ['firstLine' => 360]];
        $keepNext = ['keepNext' => true];
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellHCentered = array('align' => 'center');
        $cellVCentered = array('valign' => 'center');

        $this->setFormat($phpWord);
        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3),
            'align' => 'both',
            'indentation' => [
                'left' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5),
                'right' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5)
            ],
            'space' => [
                'line' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(16), 'rule' => 'exact'
            ]
        ]);
        $section = $phpWord->addSection([
            'footerHeight' => 300,
            'marginTop' => Converter::inchToTwip(.8),
            'marginBottom' => Converter::inchToTwip(.8),
            'marginRight' => Converter::inchToTwip(.8),
            'marginLeft' => Converter::inchToTwip(1.2)
        ]);

        $tableBasicStyle = array(
            'borderSize' => 'none',
            'cellMargin'  => Converter::inchToTwip(0),
        );

        $rowHeader = [
            'tblHeader' => true,
            'cantSplit' => true
        ];

        $cantSplit = ['cantSplit' => true];

        $table1 = $section->addTable($tableBasicStyle);
        $table1->addRow(Converter::inchToTwip(.1));
        $cell11 = $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'center']);
        // check fecth image
        // if (($data = @file_get_contents($company->link)) !== false) {
        $imgName = env('STORAGE_IMAGES', 'images') . '/' . 'company_logo.png';
        $cell11->addImage(
            storage_path('app/public/' . $imgName),
            array(
                'width'         => 54,
                'height'        => 33,
                'wrappingStyle' => 'behind',
                'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
                'posVertical'    => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
            )
        );
        // }
        # watermark
        $header = $section->addHeader();
        $header->addWatermark(
            storage_path('app/public/' . $imgName),
            array(
                'width' => 200,
                'marginTop' => 200,
                'marginLeft' => 120,
                'posHorizontal' => 'absolute',
                'posVertical' => 'absolute',
            )
        );
        $cell12 = $table1->addCell(Converter::inchToTwip(2.6), ['valign' => 'center']);
        $cell12->addText(CommonService::formatCompanyName($company), ['bold' => true, 'size' => '12'], ['align' => 'center']);
        $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'center']);
        $cell13 = $table1->addCell(Converter::inchToTwip(3.8), ['valign' => 'center']);
        $cell13->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '12'], ['align' => 'center']);
        $cell13->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true, 'size' => '13'], ['align' => 'center']);

        $section->addText('', [], ['borderBottomSize' => 20, 'underline' => 'dash']);

        $table2 = $section->addTable($tableBasicStyle);
        $table2->addRow(Converter::inchToTwip(.1));
        $documentNum = $certificate->document_num;

        $cell21 = $table2->addCell(Converter::inchToTwip(2));
        if (isset($certificate->certificate_num) && !empty(trim($certificate->certificate_num))) {
            $cell21->addText("Số:    " . $certificate->certificate_num . "    /BC-ĐNI", ['bold' => false, 'size' => '13'], ['align' => 'center', 'spaceAfter' => Converter::pixelToTwip(8)]);
        } else {
            $cell21->addText("Số:      /BC-ĐNI", ['bold' => false, 'size' => '13'], ['align' => 'center', 'spaceAfter' => Converter::pixelToTwip(8)]);
        }
        $cell22 = $table2->addCell(Converter::inchToTwip(4));

        if (isset($certificate->certificate_date) && !empty(trim($certificate->certificate_date))) {
            $certificateDate = date_create($certificate->certificate_date);
            $cell22->addText("Ngày " . $certificateDate->format('d') . " tháng " . $certificateDate->format('m') . " năm " . $certificateDate->format('Y'), ['bold' => false, 'size' => '13'], ['align' => 'right', 'spaceAfter' => Converter::pixelToTwip(8)]);
        } else {
            $certificateDate = null;
            $cell22->addText("Ngày      tháng      năm      ", ['bold' => false, 'size' => '13'], ['align' => 'right', 'spaceAfter' => Converter::pixelToTwip(8)]);
        }

        $section->addText('BÁO CÁO KẾT QUẢ THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 18], ['align' => 'center']);
        if (isset($certificate->certificate_num) && !empty(trim($certificate->certificate_num))) {
            $section->addText('(Kèm theo Chứng thư Thẩm định giá số ' . $certificate->certificate_num . '/CT-ĐNI, ngày ' . (isset($certificateDate) ? date_format($certificateDate, "d/m/Y") : '                  ') . ')', ['italic' => true, 'size' => 13], ['align' => 'center']);
        } else {
            $section->addText('(Kèm theo Chứng thư Thẩm định giá số      /CT-ĐNI, ngày ' . (isset($certificateDate) ? date_format($certificateDate, "d/m/Y") : '                  ') . ')', ['italic' => true, 'size' => 13], ['align' => 'center']);
        }
        $section->addTitle('THÔNG TIN CƠ BẢN:', 1);

        $section->addTitle('Thông tin về khách hàng thẩm định giá:', 2);
        $section->addListItem('Khách hàng: ' . $certificate->petitioner_name, 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' . htmlspecialchars($certificate->petitioner_address), 0, null, 'bullets');
        $section->addTitle('Thông tin về doanh nghiệp thẩm định giá:', 2);
        $acronym1 = !empty($company->acronym) ? ' (' . mb_strtoupper($company->acronym) . ')' : '';
        $acronym = !empty($company->acronym) ? mb_strtoupper($company->acronym) : mb_strtoupper($company->name);
        $section->addListItem('Doanh nghiệp: ' . htmlspecialchars($company->name) . $acronym1, 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' . htmlspecialchars($company->address), 0, null, 'bullets');
        $section->addListItem("Điện thoại: " . $company->phone_number . "\tFax: " . $company->fax_number, 0, null, 'bullets', 'leftTab');
        $section->addListItem('Họ và tên Tổng Giám đốc: ' . ((isset($certificate->appraiserManager) && isset($certificate->appraiserManager->name)) ? $certificate->appraiserManager->name : ''), 0, null, 'bullets');
        $section->addListItem('Họ và tên Thẩm định viên: ' . ((isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : ''), 0, null, 'bullets');
        $section->addListItem('Kiểm soát viên: Trần Văn Luân', 0, null, 'bullets');
        $section->addListItem('Người lập báo cáo: ' . (isset($certificate->createdBy->name) ? $certificate->createdBy->name : ''), 0, null, 'bullets');
        $section->addTitle('Thông tin tài sản thẩm định giá:', 2);

        $appraiseAssetType = "Quyền sử dụng đất";
        $type1 = 0; //Đất trống
        $type2 = 0; //Đất có nhà
        $type3 = 0; //Chung cư
        foreach ($appraises as $index => $appraise) {
            if ($appraise->assetType->description == "ĐẤT TRỐNG") $type1 = 1;
            if ($appraise->assetType->description == "ĐẤT CÓ NHÀ") $type2 = 1;
            if ($appraise->assetType->description == "CHUNG CƯ") $type3 = 1;
        }
        if ($type1 && $type2 && $type3) {
            $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc và Chung cư";
        } else if ($type1 && $type3) {
            $appraiseAssetType = "Quyền sử dụng đất và Chung cư";
        } else if (($type1 && $type2) || ($type2)) {
            $appraiseAssetType = "Quyền sử dụng đất và nhà cửa vật kiến trúc";
        }
        $listTmp = $section->addListItemRun(0, 'bullets', ['align' => 'both']);
        $listTmp->addText('Loại tài sản: ', ['bold' => true], []);
        $listTmp->addText($appraiseAssetType . '.', null, ['align' => 'both']);

        $appraiseAssetName = "";
        foreach ($appraises as $index => $appraise) {
            $appraiseAssetName .= ($index) ? ' và ' : '';
            $appraiseAssetName .= (isset($appraise->appraise_asset)) ? $appraise->appraise_asset : '';
        }
        $listTmp = $section->addListItemRun(0, 'bullets', ['align' => 'both']);
        $listTmp->addText('Tên tài sản: ', ['bold' => true], []);
        $listTmp->addText(htmlspecialchars($appraiseAssetName) . '.', null, ['align' => 'both']);

        $section->addTitle('Thông tin về cuộc thẩm định giá:', 2);
        $listTmp = $section->addListItemRun(0, 'bullets', ['align' => 'both']);
        $listTmp->addText('Hợp đồng thẩm định giá: ', ['bold' => true], []);

        $documentNumTmp = (isset($certificate->document_num) && !empty(trim($certificate->document_num))) ? $certificate->document_num : '        ';
        if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
            $documentDate = date_create($certificate->document_date);
            $listTmp->addText('Số ' . $documentNumTmp . '/' . date_format($documentDate, "Y") . ' ngày ' . date_format($documentDate, "d") . ' tháng ' . date_format($documentDate, "m") . ' năm ' . date_format($documentDate, "Y") . ' giữa ' . $company->name . ' và ' . $certificate->petitioner_name . '. ', null, ['align' => 'both']);
        } else {
            $listTmp->addText('Số ' . $documentNumTmp . '/         ngày        tháng        năm         giữa ' . $company->name . ' và ' . $certificate->petitioner_name . '. ', null, ['align' => 'both']);
        }

        $appraiseDate = date_create($certificate->appraise_date);
        $listTmp = $section->addListItemRun(0, 'bullets', ['align' => 'both']);
        $listTmp->addText('Thời điểm thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText(date_format($appraiseDate, "m/Y") . '.', null, []);

        $appraisePurpose = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        $listTmp = $section->addListItemRun(0, 'bullets', ['align' => 'both']);
        $listTmp->addText('Mục đích thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText($appraisePurpose . '.', null, []);

        $section->addTitle('Thuật ngữ và những từ viết tắt:', 2);
        $section->addListItem('TSSS – Tài sản so sánh.', 0, null, 'bullets');
        $section->addListItem('TSTĐ – Tài sản thẩm định', 0, null, 'bullets');
        $section->addListItem('CTXD – Công trình xây dựng.', 0, null, 'bullets');
        $section->addListItem('CLCL – Chất lượng còn lại.', 0, null, 'bullets');
        $section->addListItem('QSHN – Quyền sở hữu nhà', 0, null, 'bullets');
        $section->addListItem('QSDĐ – Quyền sử dụng đất.', 0, null, 'bullets', $keepNext);
        $section->addListItem('GCN – Giấy chứng nhận', 0, null, 'bullets', $keepNext);
        $section->addListItem('TĐ – Thẩm định', 0, null, 'bullets', $keepNext);
        $section->addListItem('SS – So sánh.', 0, null, 'bullets');

        $section->addTitle('CÁC CĂN CỨ PHÁP LÝ THẨM ĐỊNH GIÁ:', 1);

        $section->addTitle('Văn bản pháp luật về thẩm định giá:', 2);
        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable($styleTable);
        $table->addRow(400, $rowHeader);
        $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVCentered)->addText('Loại văn bản', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText(' Số, ngày', ['bold' => true], $cellHCentered);
        $table->addCell(5000, $cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $cellHCentered);
        $index = 0;
        if (isset($certificate->legalDocumentsOnValuation))
            foreach ($certificate->legalDocumentsOnValuation as $doc) {
                $doc = $doc->toArray();
                $index += 1;
                $table->addRow(400, $cantSplit);
                $table->addCell(600, $cellVCentered)->addText($index, null, $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', null, ['align' => 'left']);
                $table->addCell(2000, $cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', null, ['align' => 'left']);
                $table->addCell(5000, $cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '', null, ['align' => 'both']);
            }


        $section->addTitle('Văn bản pháp luật về đất đai:', 2);
        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable($styleTable);
        $table->addRow(400, $rowHeader);
        $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVCentered)->addText('Loại văn bản', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText(' Số, ngày', ['bold' => true], $cellHCentered);
        $table->addCell(5000, $cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $cellHCentered);
        $index = 0;
        foreach ($certificate->legalDocumentsOnLand as $doc) {
            $doc = $doc->toArray();
            $index += 1;
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellVCentered)->addText($index, null, $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', null, ['align' => 'both']);
            $table->addCell(2000, $cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', null, ['align' => 'left']);
            $table->addCell(5000, $cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '', null, ['align' => 'both']);
        }


        $section->addTitle('Văn bản pháp luật về xây dựng:', 2);
        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable($styleTable);
        $table->addRow(400, $rowHeader);
        $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVCentered)->addText('Loại văn bản', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText(' Số, ngày', ['bold' => true], $cellHCentered);
        $table->addCell(5000, $cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $cellHCentered);
        $index = 0;
        foreach ($certificate->legalDocumentsOnConstruction as $doc) {
            $doc = $doc->toArray();
            $index += 1;
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellVCentered)->addText($index, null, $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', null, ['align' => 'both']);
            $table->addCell(2000, $cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', null, ['align' => 'left']);
            $table->addCell(5000, $cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '', null, ['align' => 'both']);
        }


        $section->addTitle('Văn bản pháp luật của địa phương:', 2);
        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable($styleTable);
        $table->addRow(400, $rowHeader);
        $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVCentered)->addText('Loại văn bản', ['bold' => true], $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText(' Số, ngày', ['bold' => true], $cellHCentered);
        $table->addCell(5000, $cellVCentered)->addText('Nội dung văn bản', ['bold' => true], $cellHCentered);
        $index = 0;
        foreach ($certificate->legalDocumentsOnLocal as $doc) {
            $doc = $doc->toArray();
            $index += 1;
            $table->addRow(400, $cantSplit);
            $table->addCell(600, $cellVCentered)->addText($index, null, $cellHCentered);
            $table->addCell(2000, $cellVCentered)->addText(isset($doc['document_type']) ? $doc['document_type'] : '', null, ['align' => 'both']);
            $table->addCell(2000, $cellVCentered)->addText(isset($doc['date']) ? $doc['date'] : '', null, ['align' => 'left']);
            $table->addCell(5000, $cellVCentered)->addText(isset($doc['content']) ? CommonService::nl2br($doc['content']) : '', null, ['align' => 'both']);
        }

        $section->addTitle('PHÁP LÝ TÀI SẢN THẨM ĐỊNH GIÁ:', 1);
        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable($styleTable);
        $table->addRow(400, $rowHeader);
        $oneLaw = false;
        if (count($appraises) == 1)
            if (count($appraise->appraiseLaw) == 1)
                $oneLaw = true;
        if (!$oneLaw)
            $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVCentered)->addText('Loại văn bản', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVCentered)->addText(' Số, ngày', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(5000, $cellVCentered)->addText('Nội dung văn bản', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $table->addCell(2000, $cellVCentered)->addText('Cơ quan cấp, xác nhận', ['bold' => true], array_merge($cellHCentered, $keepNext));
        $index = 0;

        foreach ($appraises as $appraise) {
            foreach ($appraise->appraiseLaw as $doc) {
                $index += 1;
                $table->addRow(400, $cantSplit);
                if (!$oneLaw)
                    $table->addCell(600, $cellVCentered)->addText($index, null, array_merge($cellHCentered, $keepNext));
                if (isset($doc->description) && !empty($doc->description)) {
                    $lawTypeTitle = $doc->description;
                } else {
                    $lawTypeTitle = isset($doc->law->content) ? $doc->law->content : '';
                }
                $table->addCell(2000, $cellVCentered)->addText($lawTypeTitle, null, ['align' => 'both', 'keepNext' => true]);
                $soPL = isset($doc->date) ? $doc->date : '';
                $ngayPL = isset($doc->law_date) ? ' ngày ' . date_format(date_create($doc->law_date), "d/m/Y")  : '';
                $table->addCell(2000, $cellVCentered)->addText($soPL . $ngayPL, null, ['align' => 'center', 'keepNext' => true]);
                $table->addCell(5000, $cellVCentered)->addText(isset($doc->content) ? CommonService::nl2br($doc->content) : '', null, ['align' => 'both', 'keepNext' => true]);
                $table->addCell(2000, $cellVCentered)->addText(isset($doc->certifying_agency) ? CommonService::nl2br($doc->certifying_agency) : '', null, ['align' => 'both']);
            }
        }

        $section->addTitle('ĐẶC ĐIỂM TÀI SẢN THẨM ĐỊNH GIÁ', 1);

        $tableBasicStyle = array(
            'borderSize' => 'none',
            'cellMargin'  => Converter::inchToTwip(0),
        );
        $onlyOneAsset = (count($appraises) > 1) ? false : true;
        foreach ($appraises as $stt => $appraise) {
            if ($onlyOneAsset) {
                $section->addTitle('Tài sản thẩm định giá:', 2);
            } else {
                $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1) . ':', 2);
            }
            $section->addTitle('Quyền sử dụng đất.', 3);

            if ((!isset($appraise->appraiseLaw)) || (!isset($appraise->appraiseLaw[0]))) continue;
            $appraiseLaw = $appraise->appraiseLaw[0];

            $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
            $table = $section->addTable($styleTable);

            $table->addRow(400, $rowHeader);
            $table->addCell(600, $cellVCentered)->addText('Stt', ['bold' => true], array_merge($cellHCentered, $keepNext));
            $table->addCell(2000, $cellVCentered)->addText('Chỉ tiêu', ['bold' => true], $cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'gridSpan' => 2])->addText('Đặc điểm kinh tế - kỹ thuật', ['bold' => true], $cellHCentered);

            //row - begin
            $rowThirdWidth = Converter::inchToTwip(3);
            $rowFourthWidth = Converter::inchToTwip(4);
            $table->addRow(400, $cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('1', null, $cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Pháp lý', null, ['align' => 'left']);
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Số địa chính', null, ['align' => 'left']);

            $stoSthua = [];
            foreach ($appraise->appraiseLaw as $appraiseLaw) {
                if (isset($appraiseLaw->landDetails)) {
                    foreach ($appraiseLaw->landDetails as $item) {
                        if (isset($item->doc_no) & isset($item->land_no))
                            $stoSthua[$item->doc_no][] = $item->land_no;
                    }
                    $stoSthuaString = "";
                    $isFirst = 0;
                    if (!empty($stoSthua)) {
                        foreach ($stoSthua as $docNo => $landNos) {
                            if ($isFirst) {
                                $stoSthuaString .= ", ";
                            }
                            $isFirst++;
                            $landNos = array_unique($landNos);
                            $stoSthuaString .= "thửa đất số " . implode(", ", $landNos) . " tờ bản đồ số " . $docNo;
                        }
                    }
                }
            }

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                //->addText('Thửa đất số ' . $appraiseLaw->land_no . ', tờ bản đồ số ' . $appraiseLaw->doc_no . ', ' . $appraise->ward->name . ', ' . $appraise->district->name . ', ' . $appraise->province->name . '.', null, ['align' => 'left']);
                ->addText(CommonService::mbUcfirst($stoSthuaString) . ', ' . $appraise->ward->name . ', ' . $appraise->district->name . ', ' . $appraise->province->name . '.', null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $totalArea = 0;
            $totalViolateArea = 0.00;
            $desciptionZoning = 'Không.';
            $typeZoningDefault = str_replace(' ', '', mb_strtolower($appraise->properties[0]->propertyDetail[0]->type_zoning));
            $existLandTypePurpose = [];
            $mdsd = "";
            $sttTmp = 0;
            $isZoning = false;
            $countZoning = 0;
            foreach ($appraise->properties as $property) {
                foreach ($property->propertyDetail as $item) {
                    $totalArea += floatval($item->total_area);
                    if (isset($item->is_zoning) && $item->is_zoning) {
                        if ($countZoning == 0) {
                            $totalViolateArea = $item->planning_area;
                            $isZoning = true;
                            $desciptionZoning = 'Thửa đất có ' . number_format(floatval($totalViolateArea), 2, ',', '.') . $m2  . ' thuộc ' . $item->type_zoning;
                        } else {
                            if ($isZoning == true) {
                                $typeZoning = str_replace(' ', '', mb_strtolower($item->type_zoning));
                                if (strcmp($typeZoningDefault, $typeZoning) == 0) {
                                    $totalViolateArea = $totalViolateArea + $item->planning_area;
                                    $desciptionZoning = 'Thửa đất có ' .  number_format(floatval($totalViolateArea), 2, ',', '.') . $m2  . ' thuộc ' . $item->type_zoning;
                                } else {
                                    $totalViolateArea = $item->planning_area;
                                    $desciptionZoning = $desciptionZoning . ', ' . number_format(floatval($totalViolateArea), 2, ',', '.') . $m2  . ' thuộc ' . $item->type_zoning;
                                }
                            } else {
                                $totalViolateArea = $item->planning_area;
                                $desciptionZoning = 'Thửa đất có ' . number_format(floatval($totalViolateArea), 2, ',', '.') . $m2  . ' thuộc ' . $item->type_zoning;
                            }
                        }
                    }
                    if (isset($item->landTypePurpose->description) && !isset($existLandTypePurpose[$item->landTypePurpose->acronym])) {
                        $existLandTypePurpose[$item->landTypePurpose->acronym] = 1;
                        $mdsd .= ($sttTmp) ? ', ' : '';
                        $mdsd .= CommonService::mbUcfirst($item->landTypePurpose->description) . ' (' . $item->landTypePurpose->acronym . ')';
                        $sttTmp++;
                    }
                    $countZoning += 1;
                }
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Diện tích lô đất', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(number_format($totalArea, 2, ',', '.') . $m2 . '.', null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);

            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Mục đích sử dụng', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText($mdsd, null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $thsd = "";
            $stt = 0;
            $existLandTypePurpose = [];
            //foreach ($appraise->appraiseLaw as $appraiseLaw) {
            if (isset($appraise->appraiseLaw[0])) {
                $appraiseLaw = $appraise->appraiseLaw[0];
                // dd($appraiseLaw);
                $thsd = $appraiseLaw->duration;
                // foreach ($appraiseLaw->lawDetails as $item) {
                //     if ((isset($item->expiry_type)) && isset($item->landTypePurpose->description) && !isset($existLandTypePurpose[$item->landTypePurpose->acronym])) {
                //         $thsd .= ($stt) ? ', ' : '';
                //         $stt++;
                //         $existLandTypePurpose[$item->landTypePurpose->acronym] = 1;
                //         if ($item->expiry_type) {
                //             $thsd .= CommonService::mbUcfirst($item->landTypePurpose->description) . ' (' . $item->landTypePurpose->acronym . ") lâu dài";
                //         } else {
                //             //$thsd .= CommonService::mbUcfirst($item->landTypePurpose->description) . ' (' . $item->landTypePurpose->acronym . ") tới " . date('d/m/Y', strtotime($item->expiry_date));
                //             $thsd .= CommonService::mbUcfirst($item->landTypePurpose->description) . ' (' . $item->landTypePurpose->acronym . ") tới " . (isset($item->expiry_date) ? $item->expiry_date : '');
                //         }
                //     }
                // }
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Thời hạn sử dụng', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText($thsd, null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Nguồn gốc sử dụng', null, ['align' => 'left']);

            $cell = $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none']);

            //foreach ($appraise->appraiseLaw as $appraiseLaw) {
            if (isset($appraise->appraiseLaw[0])) {
                $appraiseLaw = $appraise->appraiseLaw[0];
                $textRun = $cell->addTextRun(['align' => 'left']);
                //$textRun->addText('Số, ngày: '.$appraiseLaw->origin_of_use, null, ['align' => 'left']);
                // $textRun->addText($appraiseLaw->date . ': ' . $appraiseLaw->origin_of_use, null, ['align' => 'left']);
                $textRun->addText($appraiseLaw->origin_of_use, null, ['align' => 'left']);
            }
            //$table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
            //    ->addText(CommonService::mbUcfirst($appraiseLaw->origin_of_use) . '.', null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $zoning = "";
            $stt = 0;
            $existLandTypePurpose = [];
            /* foreach ($appraiseLaw->lawDetails as $item) {
                if (isset($item->landTypePurpose->description)&&!isset($existLandTypePurpose[$item->landTypePurpose->acronym])) {
                    $existLandTypePurpose[$item->landTypePurpose->acronym] = 1;
                    $zoning .= ($stt) ? ', ' : '';
                    $zoning .= CommonService::mbUcfirst($item->landTypePurpose->description) . ' (' . $item->landTypePurpose->acronym . ') - ' . ((isset($item->is_zoning) && $item->is_zoning) ? 'có' : 'không');
                    $stt++;
                }
            } */
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Quy hoạch', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                // ->addText(((isset($isZoning) && $isZoning) ? 'Có' : 'Không') . '.', null, ['align' => 'left']);
                ->addText($desciptionZoning, null, ['align' => 'left']);
            //row - end

            //row - begin
            $table->addRow(400, $cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('2', null, $cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Vị trí', null, ['align' => 'left']);
            $coordinates = explode(",", $appraise->coordinates);
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ X', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText($coordinates[0], null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ Y', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText($coordinates[1], null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $positionType = "";
            foreach ($appraise->properties as $index => $property) {
                $positionType .= ($index) ? ', ' : '';
                /* if (isset($property->propertyTurningTime) && count($property->propertyTurningTime)) {
                    $itemTmp = $property->propertyTurningTime->last();
                    $material = (isset($itemTmp->material->description) ? mb_strtolower($itemTmp->material->description) : '');
                    $mainRoadLength = (isset($itemTmp->main_road_length) ? ($itemTmp->main_road_length) : '');
                    $mainRoadDistance = (isset($itemTmp->main_road_distance) ? ($itemTmp->main_road_distance) : '');
                    $mainRoad = (isset($appraise->street->name) ? ($appraise->street->name) : '');
                    if (stripos($mainRoad, "đường") !== false) {
                        $mainRoad = CommonService::mbLcfirst($mainRoad);
                    } else {
                        $mainRoad = 'đường ' . CommonService::mbCaseTitle($mainRoad);
                    }
                    $positionType .= "Tiếp giáp " . $material . " rộng khoảng " . $mainRoadLength . " m cách tuyến " . $mainRoad . " " . $mainRoadDistance . " m";
                } else {
                    $positionType .= $property->description;
                } */
                $positionType .= (isset($property->description) ? $property->description : '');
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Khả năng tiếp cận', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(htmlspecialchars($positionType), null, ['align' => 'left']);
            //row - end

            //row - begin
            $table->addRow(400, $cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('3', null, $cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Giao thông', null, ['align' => 'left']);
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Kết cấu, bề rộng', null, ['align' => 'left']);

            $roadCellTmp = $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none']);

            foreach ($appraise->properties as $property) {
                if (isset($property->propertyTurningTime) && count($property->propertyTurningTime)) {
                    $mainRoad = "";
                    $itemTmp = $property->propertyTurningTime->last();
                    $mainRoad = (isset($itemTmp->material->description) ? CommonService::mbUcfirst($itemTmp->material->description) : '') . ' rộng khoảng ' . number_format($itemTmp->main_road_length, 2, ',', '.') . ' m ';
                    $mainRoad = ($index) ? $mainRoad : '' . $mainRoad;
                    $roadCellTmp->addText($mainRoad, ['bold' => false], ['align' => 'left']);
                    /* foreach ($property->propertyTurningTime as $index => $item) {
                        //$mainRoad = $item->turning . ' đường ' . mb_strtolower($item->material->description) . ' rộng khoảng  ' . number_format($item->main_road_length, 2, ',', '.') . ' (m) ' . ((!$index) ? 'có' : 'không') . ' đấu nối đường chính cách đường chính ' . number_format($item->main_road_distance, 2, ',', '.') . ' m';
                        $mainRoad = $item->turning . ' ' . mb_strtolower($item->material->description) . ' rộng khoảng ' . number_format($item->main_road_length, 2, ',', '.') . ' (m) ' . ((!$index) ? 'có' : 'không') . ' đấu nối đường chính cách đường chính ' . number_format($item->main_road_distance, 2, ',', '.') . ' m';
                        $mainRoad = ($index) ? $mainRoad : '' . $mainRoad;
                        $roadCellTmp->addText($mainRoad, ['bold' => false], ['align' => 'left']);
                    } */
                } else {
                    $mainRoad = "";
                    $mainRoad .= (isset($property->material->description) ? CommonService::mbUcfirst($property->material->description) : '') . ' rộng khoảng ' . (isset($property->main_road_length) ? number_format($property->main_road_length, 2, ',', '.') . ' m' : '');
                    if (!empty($mainRoad)) {
                        $roadCellTmp->addText('' . CommonService::mbUcfirst($mainRoad), ['bold' => false], ['align' => 'left']);
                    }
                }
            }
            //row - end

            //row - begin
            $table->addRow(400, $cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('4', null, $cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Kinh doanh, hạ tầng', null, ['align' => 'left']);
            $business = "";
            foreach ($appraise->properties as $index => $property) {
                $business .= ($index) ? ', ' : '';
                $business .= (isset($property->business) && isset($property->business->description)) ? $property->business->description : '';
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Kinh doanh', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(CommonService::mbUcfirst($business), null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $conditions = "";
            foreach ($appraise->properties as $index => $property) {
                $conditions .= ($index) ? ', ' : '';
                $conditions .= (isset($property->conditions) && isset($property->conditions->description)) ? $property->conditions->description : '';
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Hạ tầng', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(CommonService::mbUcfirst($conditions), null, ['align' => 'left']);
            //row - end

            //row - begin
            $table->addRow(400, $cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('5', null, $cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Hình dáng, kích thước', null, ['align' => 'left']);
            $landShape = "";
            foreach ($appraise->properties as $index => $property) {
                $landShape .= ($index) ? ', ' : '';
                $landShape .= (isset($property->landShape) && isset($property->landShape->description)) ? $property->landShape->description : '';
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Hình dáng', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText(CommonService::mbUcfirst($landShape), null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $front_side_width = "";
            foreach ($appraise->properties as $index => $property) {
                $front_side_width .= ($index) ? ', ' : '';
                $front_side_width .= (isset($property->front_side_width)) ? $property->front_side_width : '';
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Chiều rộng mặt tiền', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText('Mặt tiền rộng ' . number_format($front_side_width, 2, ',', '.') . ' m.', null, ['align' => 'left']);

            $table->addRow(400, $cantSplit);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
            $insight_width = "";
            foreach ($appraise->properties as $index => $property) {
                $insight_width .= ($index) ? ', ' : '';
                $insight_width .= (isset($property->insight_width)) ? $property->insight_width : '';
            }
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Chiều sâu', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText('Chiều sâu ' . number_format($insight_width, 2, ',', '.') . ' m.', null, ['align' => 'left']);
            //row - end

            //row - begin
            $table->addRow(400, $cantSplit);
            $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('6', null, $cellHCentered);
            $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Hiện trạng', null, ['align' => 'left']);
            $tangible = (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) ? "Có" : "Không có";
            $table->addCell($rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Công trình', null, ['align' => 'left']);

            $table->addCell($rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText($tangible . ' công trình xây dựng trên đất.', null, ['align' => 'left']);
            //row - end

            if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                $section->addTitle('Nhà cửa, vật kiến trúc:', 3);

                $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
                $table = $section->addTable($styleTable);

                $table->addRow(400, $cantSplit);
                $table->addCell(2000, $cellVCentered)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(8000, $cellVCentered)->addText('Đặc điểm kinh tế - kỹ thuật', ['bold' => true], $cellHCentered);
                $table->addCell(2000, $cellVCentered)->addText('Số lượng ', ['bold' => true], $cellHCentered);

                //row - begin
                foreach ($appraise->tangibleAssets as $index => $tangibleAsset) {
                    $table->addRow(400, $cantSplit);
                    $structure = "";
                    $rate = "";
                    if (isset($tangibleAsset->buildingType->description)) {
                        $buildingType = $tangibleAsset->buildingType->description;
                        if ($buildingType == "NHÀ Ở RIÊNG LẺ") {
                            $structure .= "Nhà ở riêng lẻ - " . $tangibleAsset->floor . " tầng";
                            if (isset($tangibleAsset->buildingCategory->description))
                                $rate .= $tangibleAsset->buildingCategory->description;
                        } else if ($buildingType == "BIỆT THỰ") {
                            $structure .= "Biệt thự - " . $tangibleAsset->floor . " tầng";
                            if (isset($tangibleAsset->buildingCategory->description))
                                $rate .= $tangibleAsset->buildingCategory->description;
                        } else if ($buildingType == "NHÀ XƯỞNG (KHO)") {
                            $structure .= "Nhà xưởng(kho)";
                            // if (isset($tangibleAsset->factoryType->description))
                            //     $rate .= $tangibleAsset->factoryType->description;
                        } else {
                            $structure .= "Công trình khác";
                        }
                    }
                    // $buildingType = (isset($tangibleAsset->buildingType) && isset($tangibleAsset->buildingType->description)) ? CommonService::mbUcfirst($tangibleAsset->buildingType->description) : '';
                    $buildingType =  isset($tangibleAsset->tangible_name) ? CommonService::mbUcfirst($tangibleAsset->tangible_name) : '';

                    $table->addCell(2000, $cellVCentered)->addText($buildingType, null, $cellVCentered);
                    $cellTmp = $table->addCell(8000, ['valign' => 'center', 'align' => 'left']);
                    $cellTmp->addListItem('Cấu trúc: ' . $structure, 0, null, 'bullets');
                    if (!empty($rate)) $cellTmp->addListItem('Cấp công trình: ' . htmlspecialchars($rate), 0, null, 'bullets');
                    $cellTmp->addListItem('Kết cấu:', 0, null, 'bullets');
                    $cellTmp->addText('   ' . str_replace("\n", '<w:br/>   ', $tangibleAsset->contruction_description), null, ['valign' => 'center', 'align' => 'left']);
                    /* $cellTmp->addListItem('Móng, cột gạch.', 4, null, 'bullets');
                    $cellTmp->addListItem('Tường gạch, tô trát, sơn nước, ốp gạch ceramic.', 4, null, 'bullets');
                    $cellTmp->addListItem('Nền lát gạch ceramic.', 4, null, 'bullets');
                    $cellTmp->addListItem('Trần thạch cao, xà gồ sắt, mái lợp tôn.', 4, null, 'bullets');
                    $cellTmp->addListItem('Cửa sổ, cửa đi khung sắt kính.', 4, null, 'bullets');
                    $cellTmp->addListItem('Phòng bếp: Thành kệ gỗ, mặt bếp đặt tấm đá granite, tủ bếp, tủ treo gỗ.', 4, null, 'bullets');
                    $cellTmp->addListItem('Nhà vệ sinh: Nền gạch ceramic nhám, tường gạch ceramic, xí bệt.', 0, null, 'bullets'); */
                    $table->addCell(2000, $cellVCentered)->addText(number_format(floatval($tangibleAsset->total_construction_base), 2, ',', '.') . $m2, null, ['valign' => 'center', 'align' => 'center']);
                }
                //row - end
            }


            $section->addTitle('Sơ đồ vị trí và hình ảnh tài sản thẩm định giá:', 3);
            $section->addListItem('Chi tiết xem phụ luc ảnh kèm theo.', 0, null, 'bullets');
        }


        $section->addTitle('CƠ SỞ HÌNH THÀNH GIÁ TRỊ TÀI SẢN THẨM ĐỊNH GIÁ:', 1);

        $section->addTitle('Cơ sở giá trị của tài sản thẩm định giá:', 2);
        $appraiseBasisPropertyName = "";
        if (is_array($appraise->appraiseBasisProperty)) {
            foreach ($appraise->appraiseBasisProperty as $index => $item) {
                $appraiseBasisPropertyName .= ($index) ? ' và ' : '';
                $appraiseBasisPropertyName .= (isset($item->name)) ? $item->name : '';
            }
        } else {
            $appraiseBasisPropertyName .= (isset($appraise->appraiseBasisProperty)) ? $appraise->appraiseBasisProperty->name : '';
        }

        $section->addListItem('Căn cứ vào mục đích, tính chất đặc điểm của tài sản thẩm định giá, ' . $acronym . ' chọn ' . $appraiseBasisPropertyName . ' làm cơ sở thẩm định giá.', 0, null, 'bullets', $indentFistLine);
        $appraiseBasisPropertyDescription = "";
        if (is_array($appraise->appraiseBasisProperty)) {
            foreach ($appraise->appraiseBasisProperty as $index => $item) {
                $appraiseBasisPropertyDescription .= ($index) ? ' và ' : '';
                $appraiseBasisPropertyDescription .= (isset($item->description)) ? $item->description : '';
            }
        } else {
            $appraiseBasisPropertyDescription .= (isset($appraise->appraiseBasisProperty)) ? $appraise->appraiseBasisProperty->description : '';
        }

        $section->addListItem($appraiseBasisPropertyDescription, 0, null, 'bullets', $indentFistLine);

        $section->addTitle('Nguyên tắc thẩm định giá:', 2);
        foreach ($certificate->certificatePrinciple as $index => $item) {
            $section->addListItem($item->name, 0, null, 'bullets', $indentFistLine);
        }
        $section->addListItem('Các nguyên tắc khác (TĐGVN 04 Ban hành kèm theo thông tư số 158/2014/TT-BTC ngày 27/10/2014 của Bộ trưởng Bộ Tài Chính).', 0, null, 'bullets', $indentFistLine);


        $section->addTitle('PHƯƠNG THỨC TIẾN HÀNH THẨM ĐỊNH GIÁ:', 1);

        $section->addText('Toàn bộ công việc thẩm định giá được tiến hành theo quy trình thẩm định giá Việt Nam bao gồm 6 bước (TĐGVN 05 Ban hành kèm theo thông tư số 28/2015/TT-BTC ngày 06/03/2015 của Bộ trưởng Bộ Tài Chính). ', null, ['align' => 'both', 'indentation' => ['firstLine' => Converter::inchToTwip(0.2)]]);
        $listTmp = $section->addListItemRun(0, 'bullets', $indentFistLine);
        $listTmp->addText('Bước 1: ', ['bold' => true], []);
        $listTmp->addText('Tiếp nhận hồ sơ, hướng dẫn khách hàng viết yêu cầu thẩm định giá, giải thích quy trình, các thủ tục hồ sơ, tài liệu, chứng từ, ký kết hợp đồng. Nghiên cứu các tài liệu, hồ sơ do khách hàng cung cấp.', null, ['align' => 'both']);
        $listTmp = $section->addListItemRun(0, 'bullets', $indentFistLine);
        $listTmp->addText('Bước 2: ', ['bold' => true], []);
        $listTmp->addText('Lập kế hoạch Thẩm định giá.', null, ['align' => 'both']);
        $listTmp = $section->addListItemRun(0, 'bullets', $indentFistLine);
        $listTmp->addText('Bước 3: ', ['bold' => true], []);
        $listTmp->addText('Tiến hành Thẩm định hiện trạng tài sản theo hướng dẫn của khách hàng. Tổ Thẩm định giá đã kiểm tra, đối chiếu giữa thông tin về tài sản TĐG trong các chứng từ, hồ sơ pháp lý với thực tế tại hiện trường, ghi nhận những thông tin do khách hàng cung cấp.', null, ['align' => 'both']);
        $listTmp = $section->addListItemRun(0, 'bullets', $indentFistLine);
        $listTmp->addText('Bước 4: ', ['bold' => true], []);
        $listTmp->addText('Phân tích về tài sản, các tài sản so sánh, vận dụng các tài liệu, kiểm tra, đối chiếu các thông tin thu thập tại hiện trường với các tài liệu.', null, ['align' => 'both']);
        $listTmp = $section->addListItemRun(0, 'bullets', $indentFistLine);
        $listTmp->addText('Bước 5: ', ['bold' => true], []);
        $listTmp->addText('Ứng dụng nguyên tắc và phương pháp thẩm định giá để ước tính giá trị của tài sản thẩm định giá tại thời điểm và địa điểm thẩm định giá.', null, ['align' => 'both']);
        $listTmp = $section->addListItemRun(0, 'bullets', $indentFistLine);
        $listTmp->addText('Bước 6: ', ['bold' => true], []);
        $listTmp->addText('Hoàn chỉnh báo cáo, cấp chứng thư, thanh lý hợp đồng.', null, ['align' => 'both']);


        $section->addTitle('CÁC GIẢ THIẾT VÀ GIẢ THIẾT ĐẶC BIỆT:', 1);
        $section->addListItem($certificate->document_description, 0, null, 'bullets');
        //$section->addListItem('', 0, null, 'bullets');


        $section->addTitle('CÁCH TIẾP CẬN VÀ PHƯƠNG PHÁP THẨM ĐỊNH GIÁ:', 1);

        $section->addTitle('Thông tin tổng quan về thị trường, các thông tin về thị trường giao dịch của tài sản thẩm định giá.', 2);
        $section->addListItem('Thông tin tổng quan về thị trường: Tại thời điểm thẩm định giá, thị trường bất động sản tại khu vực không có nhiều biến động.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('Thực trạng và triển vọng cung cầu của nhóm (loại) tài sản thẩm định giá: Thị trường cung – cầu nhóm (loại) tài sản đang ở mức cân bằng.', 0, null, 'bullets', $indentFistLine);

        $section->addTitle('Căn cứ lựa chọn phương pháp:', 2);
        $certificatePrinciple = "";
        foreach ($certificate->certificatePrinciple as $index => $item) {
            $certificatePrinciple .= ($index) ? ' và ' : '';
            $certificatePrinciple .= (isset($item->name)) ? $item->name : '';
        }
        $section->addListItem('Tài sản thẩm định giá có đầy đủ giấy tờ pháp lý, phù hợp quy hoạch và sử dụng đúng mục đích công năng đem lại giá trị lớn nhất cho tài sản. Tài sản thẩm định đáp ứng với các yêu cầu theo ' . $certificatePrinciple . '.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('Điều kiện, tính chất thông tin thị trường: Dữ liệu thị trường về các tài sản giao dịch có đặc điểm tương đồng với TSTĐG tương đối phổ biến và đầy đủ nên việc sử dụng phương pháp so sánh để xác định giá trị tài sản thẩm định giá là phù hợp và đáng tin cậy.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('Ngoài ra nguồn dữ liệu và thông tin có thể sử dụng để xác định giá trị tài sản thẩm định giá theo phương pháp khác là rất hạn chế. Vì vậy, căn cứ mục đích thẩm định giá của tài sản ' . $acronym . ' sử dụng phương pháp so sánh là phù hợp.', 0, null, 'bullets', $indentFistLine);

        $section->addTitle('Cách tiếp cận, phương pháp thẩm định giá áp dụng:', 2);


        $appraiseApproaches = [];
        $hasTangibleAssets = false;
        foreach ($appraises as $appraise) {
            $appraiseApproaches[$appraise->appraiseApproach->id] = $appraise;
            if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                $hasTangibleAssets = true;
            }
        }
        if ($hasTangibleAssets)
            $section->addTitle('Đối với quyền sử dụng đất', 3);
        foreach ($appraiseApproaches as $item) {
            $section->addListItem("Cách tiếp cận: " . $item->appraiseApproach->name, 0, ['bold' => true, 'italic' => true], 'bullets');
            $section->addText($item->appraiseApproach->description, null, $indentFistLine);
            $section->addListItem("Phương pháp sử dụng: " . $item->appraiseMethodUsed->name, 0, ['bold' => true, 'italic' => true], 'bullets');
            $section->addText($item->appraiseMethodUsed->description, null, $indentFistLine);
        }

        if ($hasTangibleAssets) {
            $section->addTitle('Nhà cửa, vật kiến trúc:', 3);
            $section->addText('a) Về nguyên giá', ['italic' => true], $indentFistLine);
            $section->addText('- Cách tiếp cận thị trường.', ['italic' => true], $indentFistLine);
            $section->addText('Cách tiếp cận từ thị trường là cách thức xác định giá trị của tài sản thẩm định giá thông qua việc so sánh tài sản thẩm định giá với các tài sản giống hệt hoặc tương tự đã có các thông tin về giá trên thị trường.', null,  $indentFistLine);

            $section->addText('- Phương pháp sử dụng: Phương pháp so sánh.', ['italic' => true], $indentFistLine);
            $section->addText('Phương pháp so sánh là phương pháp thẩm định giá, xác định giá trị của tài sản thẩm định giá dựa trên cơ sở phân tích mức giá của các tài sản so sánh để ước tính, xác định giá trị của tài sản thẩm định giá.', null,  $indentFistLine);

            $section->addText('- Kết quả điều tra thu thập dữ liệu thị trường, phân tích đánh giá và ước tính giá trị nhà cửa, vật kiến trúc:', ['italic' => true],  $indentFistLine);
            $section->addText('+ ' .  $acronym . ' căn cứ theo Quyết định số 09-11/2019/QĐ-UBND ngày 11/3/2019 về việc Ban hành Quy định về giá nhà, giá vật kiến trúc trên địa bàn tỉnh Đồng Nai kết hợp khảo sát đơn giá xây dựng thực tế tại thì trường tỉnh Đồng Nai và tìm hiểu thông tin đơn giá xây dựng của các công ty xây dựng trên địa bàn tỉnh Đồng Nai. ', null, ['align' => 'both', 'indentation' => ['firstLine' => Converter::inchToTwip(0)]]);

            $section->addText('1. Quyết định số: 11/2019/ QĐ- UBND ngày 15/03/2019 của UBND tỉnh Đồng Nai V/v Ban hành qui định về giá bồi thường, hỗ trợ tài sản khi nhà nước thu hồi đất trên địa bàn tỉnh Đồng Nai.', null,  $indentFistLine);

            $constructionCompanies = [];
            //echo '<pre>';
            foreach ($appraises as $appraise) {
                foreach ($appraise->constructionCompany as $index => $item) {
                    if (!isset($item->constructionCompany->company_id)) continue;
                    $constructionCompanies[$item->constructionCompany->company_id] = $item;
                }
            }
            //exit;
            $stt = 1;
            foreach ($constructionCompanies as $index => $item) {
                $stt++;
                $section->addText(($stt) . '. ' . $item->constructionCompany->name . '.', null, $indentFistLine);
                $section->addText('- Địa chỉ: ' . htmlspecialchars($item->constructionCompany->address) . '.', null, $indentFistLine);
                $section->addText('- Điện thoại: ' . $item->constructionCompany->phone_number, null, $indentFistLine);
                $section->addText('- Giám đốc: ' . $item->constructionCompany->manager_name . '.', null, $indentFistLine);
            }
            $section->addText(($stt + 1) . '. Các tài liệu lưu trữ của ' .  $acronym . '.', null, $indentFistLine);
            $section->addText('b) Về đánh giá chất lượng còn lại', ['bold' => true], $indentFistLine);
            $section->addText('- Căn cứ theo biên bản kiểm kê và kết quả khảo sát hiện trạng của Tổ thẩm định giá. ' .  $acronym . ' đánh giá nguyên giá, chất lượng còn lại của công trình xây dựng:', null, $indentFistLine);
            $section->addText('❖ Phương pháp đánh giá chất lượng còn lại nhà cửa, vật kiến trúc: ' . $acronym . ' lựa chọn 2 phương pháp:', ['bold' => true], $indentFistLine);
            $section->addText('✔ Phương pháp 1: Phương pháp tuổi đời (PP1): ', ['italic' => true, 'bold' => true, 'size' => 13], $indentFistLine);
            $section->addText('- Vận dụng thông tư 45/2013/TT-BTC ngày 25 tháng 4 năm 2013 kết hợp khảo sát hiện trường tại thời điểm thẩm định giá. ', null, $indentFistLine);

            $table = $section->addTable(array_merge($tableBasicStyle));
            $table->addRow();
            $table->addCell(2500, $cellRowSpan)->addText("Tỷ lệ hao mòn vật lý (%)", [], $cellHCentered);
            $table->addCell(800, $cellRowSpan)->addText("=", [], $cellHCentered);
            $table->addCell(4500)->addText("Tuổi đời hiệu quả", [], ['borderBottomSize' => 6, 'underline' => 'dash', 'align' => 'center']);
            $table->addCell(800, $cellRowSpan)->addText("x", [], $cellHCentered);
            $table->addCell(800, $cellRowSpan)->addText("100%", [], $cellHCentered);
            $table->addRow();
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);
            $table->addCell(4000)->addText("Tuổi đời vật lý", [], $cellHCentered);
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);

            $section->addText('Tuổi đời vật lý = tuổi đời hiệu quả + Tuổi đời vật lý còn lại', null, $indentFistLine);
            $section->addText('Tuổi đời vật lý còn lại là thời gian ước tính còn lại mà tài sản có thể tiếp tục sử dụng trước khi chuyển sang trạng thái không còn sử dụng được do hư hỏng hoặc bào mòn vì các nguyên nhân vật lý.', null, $indentFistLine);
            $section->addText('✔ Phương pháp 2: Phương pháp chuyên gia (PP2): ', ['italic' => true, 'bold' => true, 'size' => 13], $indentFistLine);
            $section->addText('- Vận dụng thông tư liên tịch số 13/LB-TT ngày 18/8/1994 và thông tư 12/2012/TT-BXD ngày 28/12/2012 của BXD ban hành Quy chuẩn kỹ thuật quốc gia nguyên tắc phân loại, phân cấp công trình dân dụng, công nghiệp và hạ tầng kỹ thuật đô thị. ', null, $indentFistLine);

            $table = $section->addTable(array_merge($tableBasicStyle));
            $table->addRow();
            $table->addCell(2500, $cellRowSpan)->addText("Chất lượng còn lại của CTXD (%)", [], $cellHCentered);
            $table->addCell(800, $cellRowSpan)->addText("=", [], $cellHCentered);
            $table->addCell(4500)->addText("Σ (Tỷ lệ CLCL của kết cấu chính x tỷ lệ giá trị kết cấu chính)", [], ['borderBottomSize' => 6, 'underline' => 'dash', 'align' => 'center']);
            $table->addCell(800, $cellRowSpan)->addText("x", [], $cellHCentered);
            $table->addCell(800, $cellRowSpan)->addText("100%", [], $cellHCentered);
            $table->addRow();
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);
            $table->addCell(4000)->addText("Σ (Tỷ trọng kết cấu chính)", [], $cellHCentered);
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);
            $table->addCell(null, $cellRowContinue, [], $cellHCentered);

            $section->addText('Ghi chú:', ['bold' => true], $indentFistLine);
            $section->addText('p: Tỷ trọng của các kết cấu chính (%)', null, $indentFistLine);
            $section->addText('H = Σ ph / Σ p; Tỷ lệ chất lượng còn lại (%)', null, $indentFistLine);
        }

        $section->addTitle('Xác định giá trị tài sản cần thẩm định giá:', 2);
        foreach ($appraises as $stt => $appraise) {
            if ($onlyOneAsset) {
                if ($hasTangibleAssets)
                    $section->addTitle('Quyền sử dụng đất:', 3);
                $section->addText('- Chi tiết xem Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $indentFistLine);
                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $section->addTitle('Nhà cửa, vật kiến trúc:', 3);
                    $section->addText('- Chi tiết xem Phụ lục 2 kèm theo báo cáo kết quả thẩm định giá.', null, $indentFistLine);
                }
            } else {
                $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1), 3);
                if ($hasTangibleAssets)
                    $section->addText('a) Quyền sử dụng đất:', ['italic' => true], $indentFistLine);
                $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 1 kèm theo báo cáo kết quả thẩm định giá.', null, $indentFistLine);
                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $section->addText('b) Nhà cửa, vật kiến trúc:', ['italic' => true], $indentFistLine);
                    $section->addText('- Chi tiết xem mục ' . ($stt + 1) . ' - Phụ lục 2 kèm theo báo cáo kết quả thẩm định giá.', null, $indentFistLine);
                }
            }
        }


        $section->addTitle('KẾT QUẢ THẨM ĐỊNH GIÁ:', 1);
        $section->addText('Trên cơ sở các tài liệu do ' . $certificate->petitioner_name . ' cung cấp, với phương pháp thẩm định giá như trên được áp dụng trong tính toán, ' . $company->name . ' thông báo kết quả thẩm định giá như sau:', null, $indentFistLine);

        $totalEstimatePrice = [];
        foreach ($appraises as $stt => $appraise) {
            $totalEstimatePrice[$stt] = 0;
            $sttLevel = 2;
            if ($onlyOneAsset) {
                $sttLevel = 1;
                //$section->addTitle('Tài sản thẩm định giá ', 2);
            } else {
                $section->addTitle('Tài sản thẩm định giá ' . ($stt + 1), 2);
            }

            $onlyLandAsset = 1;
            if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                $onlyLandAsset = 0;
            }
            if (isset($appraise->otherAssets) && count($appraise->otherAssets)) {
                $onlyLandAsset = 0;
            }

            if ($onlyLandAsset) {
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($styleTable);
                $table->addRow(400, $rowHeader);
                $table->addCell(4000, $cellVCentered)->addText('Quyền sử dụng đất', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellVCentered)->addText('MĐSD', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellVCentered)->addText('Diện tích (' . $m2 . ')', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(2500, $cellVCentered)->addText('Đơn giá ', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(2500, $cellVCentered)->addText('Thành tiền', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $propertyDetailTotal = 0;
                $zoningAppraise = [];
                $noZoningAppraise = [];
                $totalArea = 0;

                foreach ($appraise->properties as $property) {
                    foreach ($property->propertyDetail as $item) {
                        if ($item->is_zoning) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
                            // $donGiaDatRound = CommonService::roundViolationAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            // if (!$item->is_transfer_facility) {
                            //     $donGiaDatRound = CommonService::roundViolationCompositeAssetPrice($appraise, $donGiaDat);
                            // }
                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $zoningAppraise[] = $rowTmp;
                        }
                        if (!$item->is_zoning || $item->total_area -  $item->planning_area > 0) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_round');
                            // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            if ($item->is_transfer_facility) {
                                if ($appraise->layer_cutting_procedure) {
                                    // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $appraise->layer_cutting_price);
                                    $donGiaDatRound = CommonService::roundPrice($appraise->layer_cutting_price, $round);
                                }
                            } else {
                                // $donGiaDatRound = CommonService::roundCompositeAssetPrice($appraise, $donGiaDat);
                                $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            }

                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $noZoningAppraise[] = $rowTmp;
                        }
                    }
                }
                $isFirst = 0;
                foreach ($noZoningAppraise as $item) {
                    $table->addRow(400, $cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $cellRowSpan)->addText('Phần diện tích đất phù hợp quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $cellRowContinue, [], $cellHCentered)->addText(null, null, ['keepNext' => true]);
                    }
                    $table->addCell(1500, $cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }
                $isFirst = 0;
                foreach ($zoningAppraise as $item) {
                    $table->addRow(400, $cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $cellRowSpan)->addText('Phần diện tích đất vi phạm quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $cellRowContinue, [], $cellHCentered);
                    }
                    $table->addCell(1500, $cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }
                $totalEstimatePrice[$stt] += $propertyDetailTotal;

                $table->addRow(400, $cantSplit);

                if ($onlyOneAsset) {
                    // $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1500, $cellVCentered)->addText(number_format($totalArea, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(1500, $cellVCentered)->addText('', null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(1000, $cellVCentered)->addText(number_format($propertyDetailTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                } else {
                    // $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);
                    $table->addCell(1500, $cellVCentered)->addText(number_format($totalArea, 2, ',', '.'), ['bold' => true], ['align' => 'right']);
                    $table->addCell(1500, $cellVCentered)->addText('', null, ['align' => 'right']);
                    $table->addCell(1000, $cellVCentered)->addText(number_format($propertyDetailTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
                }

                if ($onlyOneAsset) {
                    $totalAll = $propertyDetailTotal;
                    $totalAllRound = CommonService::roundCertificatePrice($certificate, $totalAll);
                    $table->addRow(400, $cantSplit);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
                    $table->addRow(400, $cantSplit);
                    $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 5))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($totalAllRound) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center', 'keepNext' => true]);
                }
            } else {
                $section->addTitle('Quyền sử dụng đất:', $sttLevel + 1);
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($styleTable);
                $table->addRow(400, $rowHeader);
                $table->addCell(4000, $cellVCentered)->addText('Quyền sử dụng đất', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellVCentered)->addText('MĐSD', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(1500, $cellVCentered)->addText('Diện tích (' . $m2 . ')', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(2500, $cellVCentered)->addText('Đơn giá ', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(2500, $cellVCentered)->addText('Thành tiền', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $propertyDetailTotal = 0;
                $zoningAppraise = [];
                $noZoningAppraise = [];
                $totalArea = 0;
                // dd($appraise->properties);
                foreach ($appraise->properties as $property) {
                    foreach ($property->propertyDetail as $item) {
                        if ($item->is_zoning) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
                            // $donGiaDatRound = CommonService::roundViolationAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            // if (!$item->is_transfer_facility) {
                            //     $donGiaDatRound = CommonService::roundViolationCompositeAssetPrice($appraise, $donGiaDat);
                            // }
                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $zoningAppraise[] = $rowTmp;
                        }
                        if (!$item->is_zoning || $item->total_area -  $item->planning_area > 0) {
                            $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                            $dientich = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_area');
                            $donGiaDat = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_price');
                            $round = CommonService::getCertificateAssetPrice($appraise, 'land_asset_purpose_' . $landTypePurpose . '_round');
                            // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $donGiaDat);
                            $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            if ($item->is_transfer_facility) {
                                if ($appraise->layer_cutting_procedure) {
                                    // $donGiaDatRound = CommonService::roundAssetPrice($appraise, $appraise->layer_cutting_price);
                                    $donGiaDatRound = CommonService::roundPrice($appraise->layer_cutting_price, $round);
                                }
                            } else {
                                // $donGiaDatRound = CommonService::roundCompositeAssetPrice($appraise, $donGiaDat);
                                $donGiaDatRound = CommonService::roundPrice($donGiaDat, $round);
                            }

                            $total = (round($dientich * $donGiaDatRound));
                            $propertyDetailTotal += $total;
                            $totalArea += $dientich;
                            $rowTmp = [];
                            $rowTmp[] = $landTypePurpose;
                            $rowTmp[] = number_format($dientich, 2, ',', '.');
                            $rowTmp[] = number_format($donGiaDatRound, 0, ',', '.');
                            $rowTmp[] = number_format($total, 0, ',', '.');

                            $noZoningAppraise[] = $rowTmp;
                        }
                    }
                }
                $isFirst = 0;
                foreach ($noZoningAppraise as $item) {
                    $table->addRow(400, $cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $cellRowSpan)->addText('Phần diện tích đất phù hợp quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $cellRowContinue, [], $cellHCentered)->addText(null, null, ['keepNext' => true]);
                    }
                    $table->addCell(1500, $cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }
                $isFirst = 0;
                foreach ($zoningAppraise as $item) {
                    $table->addRow(400, $cantSplit);
                    if (!$isFirst) {
                        $table->addCell(4000, $cellRowSpan)->addText('Phần diện tích đất vi phạm quy hoạch', null, ['valign' => 'center', 'align' => 'left', 'keepNext' => true]);
                    } else {
                        $table->addCell(null, $cellRowContinue, [], $cellHCentered)->addText(null, null, ['keepNext' => true]);
                    }
                    $table->addCell(1500, $cellVCentered)->addText($item[0], null, ['align' => 'center', 'keepNext' => true]);
                    $table->addCell(1500, $cellVCentered)->addText($item[1], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[2], null, ['align' => 'right', 'keepNext' => true]);
                    $table->addCell(2500, $cellVCentered)->addText($item[3], null, ['align' => 'right', 'keepNext' => true]);
                    $isFirst++;
                }
                $totalEstimatePrice[$stt] += $propertyDetailTotal;

                $table->addRow(400, $cantSplit);
                // $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);

                $table->addCell(1000, array('align' => 'left', 'gridSpan' => 2))->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($totalArea, 2, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText('', ['bold' => true], ['align' => 'right', 'keepNext' => true]);
                $table->addCell(1000, ['align' => 'right'])->addText(number_format($propertyDetailTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right', 'keepNext' => true]);

                $tangibleAssetTotal = 0;
                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $section->addTitle('Nhà cửa, vật kiến trúc:', $sttLevel + 1);
                    $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                    $table = $section->addTable($styleTable);
                    $table->addRow(400, $rowHeader);
                    $table->addCell(4000, $cellVCentered)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext));
                    $table->addCell(1000, $cellVCentered)->addText('Đvt', ['bold' => true], $cellHCentered);
                    $table->addCell(1000, $cellVCentered)->addText('Số lượng', ['bold' => true], $cellHCentered);
                    $table->addCell(1000, $cellVCentered)->addText('CLCL (%)', ['bold' => true], $cellHCentered);
                    $table->addCell(2000, $cellVCentered)->addText('Đơn giá', ['bold' => true], $cellHCentered);
                    $table->addCell(2000, $cellVCentered)->addText('Thành tiền', ['bold' => true], $cellHCentered);
                    /*
                    $unitPrice =  0;
                    foreach($appraise->constructionCompany as $index=>$constructionCompany) {
                        $unitPrice += (isset($constructionCompany->constructionCompany)&&isset($constructionCompany->constructionCompany->unit_price_m</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/></w:rPr><w:t xml:space="preserve">)) ? $constructionCompany->constructionCompany->unit_price_m</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr><w:b w:val="1"/><w:bCs w:val="1"/></w:rPr><w:t xml:space="preserve"> : 0;
                    }
                    */

                    //if(!empty($appraise->constructionCompany)) $unitPrice =  round($unitPrice/count($appraise->constructionCompany));
                    $appraisalCLCL = $appraise->appraisal_clcl;
                    $appraisalDgxd = $appraise->appraisal_dgxd;
                    foreach ($appraise->tangibleAssets as $index => $tangibleAsset) {
                        $table->addRow(400, $cantSplit);
                        $building_type =  isset($tangibleAsset->tangible_name) ? $tangibleAsset->tangible_name : '';
                        //$total = (round($tangibleAsset->total_construction_area*$tangibleAsset->remaining_quality/100*$unitPrice));
                        $unitPrice =  CommonService::getDgxdChoosed($tangibleAsset, $appraisalDgxd);
                        $clcl =  CommonService::getClclChoosed($tangibleAsset, $appraisalCLCL);
                        $total = (round($tangibleAsset->total_construction_base * $clcl / 100 * $unitPrice));
                        $tangibleAssetTotal += $total;
                        $table->addCell(1000, $cellVCentered)->addText(CommonService::mbUcfirst($building_type), null, ['align' => 'left']);
                        $table->addCell(1000, $cellVCentered)->addText($m2, null, $cellHCentered);
                        $table->addCell(1000, $cellVCentered)->addText(number_format($tangibleAsset->total_construction_base, 2, ',', '.'), null, ['align' => 'right']);
                        //$table->addCell(1000, $cellVCentered)->addText($tangibleAsset->remaining_quality, null, $cellHCentered);
                        $table->addCell(1000, $cellVCentered)->addText($clcl, null, ['align' => 'right']);
                        if ($unitPrice) {
                            $table->addCell(1000, $cellVCentered)->addText(number_format($unitPrice, 0, ',', '.'), null, ['align' => 'right']);
                        } else {
                            $table->addCell(1000, $cellVCentered)->addText("Không biết", null, ['align' => 'right']);
                        }

                        $table->addCell(1000, $cellVCentered)->addText(number_format($total, 0, ',', '.'), null, ['align' => 'right', 'keepNext' => true]);
                    }
                    //get from database
                    $tangibleAssetTotal = CommonService::getCertificateAssetPrice($appraise, 'tangible_asset_price');
                    $totalEstimatePrice[$stt] += $tangibleAssetTotal;
                    $table->addRow(400, $cantSplit);
                    //$table->addCell(1000, $cellVCentered)->addText('', null, $cellHCentered);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 5))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);
                    $table->addCell(1000, array('align' => 'right'))->addText(number_format($tangibleAssetTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
                }
                $otherAssetTotal = 0;
                if (isset($appraise->otherAssets) && count($appraise->otherAssets)) {
                    $section->addTitle('Tài sản khác', $sttLevel + 1);
                    $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                    $table = $section->addTable($styleTable);
                    $table->addRow(400, $rowHeader);
                    $table->addCell(4000, $cellVCentered)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext));
                    $table->addCell(1000, $cellVCentered)->addText('Đvt', ['bold' => true], $cellHCentered);
                    $table->addCell(1000, $cellVCentered)->addText('Số lượng', ['bold' => true], $cellHCentered);
                    $table->addCell(2000, $cellVCentered)->addText('Đơn giá', ['bold' => true], $cellHCentered);
                    $table->addCell(2000, $cellVCentered)->addText('Thành tiền', ['bold' => true], $cellHCentered);

                    foreach ($appraise->otherAssets as $index => $otherAsset) {
                        $table->addRow(400, $cantSplit);
                        $otherAssetTotal += $otherAsset->total_price;
                        $table->addCell(1000, $cellVCentered)->addText($otherAsset->name, null, ['align' => 'left']);
                        $table->addCell(1000, $cellVCentered)->addText(strtolower($otherAsset->dvt), null, $cellHCentered);
                        $table->addCell(1000, $cellVCentered)->addText(number_format($otherAsset->total, 2, ',', '.'), null, ['align' => 'right']);
                        $table->addCell(1000, $cellVCentered)->addText(number_format($otherAsset->unit_price, 0, ',', '.'), null, ['align' => 'right']);
                        $table->addCell(1000, $cellVCentered)->addText(number_format($otherAsset->total_price, 0, ',', '.'), null, ['align' => 'right', 'keepNext' => true]);
                    }
                    //get from database
                    $otherAssetTotal = CommonService::getCertificateAssetPrice($appraise, 'other_asset_price');
                    $totalEstimatePrice[$stt] += $otherAssetTotal;
                    $table->addRow(400, $cantSplit);
                    //$table->addCell(1000, $cellVCentered)->addText('', null, $cellHCentered);
                    $table->addCell(1000, array('align' => 'left', 'gridSpan' => 4))->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);
                    $table->addCell(1000, array('align' => 'right'))->addText(number_format($otherAssetTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
                }

                $section->addTitle('Tổng giá trị tài sản thẩm định giá:', $sttLevel + 1);
                $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
                $table = $section->addTable($styleTable);
                $table->addRow(400, $rowHeader);
                //$table->addCell(1000, $cellVCentered)->addText('Stt', ['bold' => true], $cellHCentered);
                $table->addCell(6000, $cellVCentered)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext));
                $table->addCell(4000, $cellVCentered)->addText('Giá trị thẩm định', ['bold' => true], $cellHCentered);
                $table->addRow(400, $cantSplit);
                //$table->addCell(1000, $cellVCentered)->addText('1', null, $cellHCentered);
                $table->addCell(1000, $cellVCentered)->addText('Quyền sử dụng đất', null, ['align' => 'left', 'keepNext' => true]);
                $table->addCell(1000, $cellVCentered)->addText(number_format($propertyDetailTotal, 0, ',', '.'), null, array('align' => 'right'));
                if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
                    $table->addRow(400, $cantSplit);
                    //$table->addCell(1000, $cellVCentered)->addText('2', null, $cellHCentered);
                    $table->addCell(1000, $cellVCentered)->addText('Nhà cửa, vật kiến trúc', null, ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $cellVCentered)->addText(number_format($tangibleAssetTotal, 0, ',', '.'), null, ['align' => 'right']);
                }
                if (isset($appraise->otherAssets) && count($appraise->otherAssets)) {
                    $table->addRow(400, $cantSplit);
                    //$table->addCell(1000, $cellVCentered)->addText('2', null, $cellHCentered);
                    $table->addCell(1000, $cellVCentered)->addText('Tài sản khác', null, ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $cellVCentered)->addText(number_format($otherAssetTotal, 0, ',', '.'), null, ['align' => 'right']);
                }
                $table->addRow(400, $cantSplit);
                //$table->addCell(1000, $cellVCentered)->addText('', null, $cellHCentered);

                if ($onlyOneAsset) {
                    $table->addCell(1000, $cellVCentered)->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
                } else {
                    $table->addCell(1000, $cellVCentered)->addText('Tổng cộng', ['bold' => true], ['align' => 'left']);
                }
                $table->addCell(1000, $cellVCentered)->addText(number_format($propertyDetailTotal + $tangibleAssetTotal + $otherAssetTotal, 0, ',', '.'), ['bold' => true], ['align' => 'right']);

                if ($onlyOneAsset) {
                    $totalAll = $propertyDetailTotal + $tangibleAssetTotal + $otherAssetTotal;
                    $totalAllRound = CommonService::roundCertificatePrice($certificate, $totalAll);
                    $table->addRow(400, $cantSplit);
                    $table->addCell(1000, $cellVCentered)->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
                    $table->addCell(1000, $cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
                    $table->addRow(400, $cantSplit);
                    $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 2))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($totalAllRound) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center']);
                }
            }
        }
        $roundNum = (float) 0;
        $count = 0;
        if (isset($appraises) && (count($appraises) > 1)) {
            $section->addTitle('Tổng giá trị tài sản thẩm định giá:', 2);
            $section->addText('Đvt: đồng.', ['italic' => true], ['align' => 'right', 'keepNext' => true]);
            $table = $section->addTable($styleTable);
            $table->addRow(400, $rowHeader);
            //$table->addCell(1000, $cellVCentered)->addText('Stt', ['bold' => true], $cellHCentered);
            $table->addCell(6000, $cellVCentered)->addText('Tên tài sản', ['bold' => true], array_merge($cellHCentered, $keepNext));
            $table->addCell(4000, $cellVCentered)->addText('Giá trị thẩm định', ['bold' => true], $cellHCentered);
            $totalAll = 0;

            // $roundNum=$appraises[0]->round_total;
            foreach ($appraises as $stt => $appraise) {
                $table->addRow(400, $cantSplit);
                //$table->addCell(1000, $cellVCentered)->addText($stt + 1, null, $cellHCentered);
                if ($onlyOneAsset) {
                    $table->addCell(1000, array('align' => 'left'))->addText('Tài sản thẩm định giá ', null, ['align' => 'left']);
                } else {
                    $table->addCell(1000, array('align' => 'left'))->addText('Tài sản thẩm định giá ' . ($stt + 1), null, ['align' => 'left', 'keepNext' => true]);
                }
                $totalAll += $totalEstimatePrice[$stt];
                $table->addCell(1000, array('align' => 'right'))->addText(number_format($totalEstimatePrice[$stt], 0, ',', '.'), null, array('align' => 'right', 'keepNext' => true));
            }

            $totalAllRound = CommonService::roundCertificatePrice($certificate, $totalAll);

            $table->addRow(400, $cantSplit);
            //$table->addCell(1000, $cellVCentered)->addText('', null, $cellHCentered);
            $table->addCell(1000, $cellVCentered)->addText('Tổng cộng', ['bold' => true], ['align' => 'left', 'keepNext' => true]);
            $table->addCell(1000, $cellVCentered)->addText(number_format($totalAll, 0, ',', '.'), ['bold' => true], ['align' => 'right']);
            $table->addRow(400, $cantSplit);
            //$table->addCell(1000, $cellVCentered)->addText('', null, $cellHCentered);
            $table->addCell(1000, $cellVCentered)->addText('Làm tròn', ['bold' => true, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
            //PHP_ROUND_HALF_DOWN
            $table->addCell(1000, $cellVCentered)->addText(number_format($totalAllRound, 0, ',', '.'), ['bold' => true, 'italic' => true], ['align' => 'right']);
            $table->addRow(400, $cantSplit);
            $table->addCell(1000, array('valign' => 'center', 'gridSpan' => 2))->addText('Bằng chữ: ' . CommonService::convertNumberToWords($totalAllRound) . ' đồng', ['bold' => true, 'italic' => true], ['align' => 'center']);
        }

        $section->addTitle('NHỮNG ĐIỀU KHOẢN LOẠI TRỪ VÀ HẠN CHẾ CỦA KẾT QUẢ THẨM ĐỊNH GIÁ:', 1);

        $section->addListItem('Kết quả thẩm định giá trên chỉ có giá trị cho tài sản có đặc điểm pháp lý, đặc điểm kỹ thuật được mô tả tại mục IV của báo cáo này, theo yêu cầu thẩm định giá của ' . (isset($certificate->petitioner_name) ? $certificate->petitioner_name : '') . ' tại thời điểm và địa điểm thẩm định giá.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('Các số liệu về tài sản ' . $company->name . ' căn cứ vào hồ sơ do khách hàng cung cấp và kết hợp khảo sát thực tế tại hiện trường dưới sự hướng dẫn của khách hàng và các bên có liên quan.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('Báo cáo chỉ có hiệu lực trong phạm vi số lượng và giá trị tài sản ghi tại mục IX của báo cáo này.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('Kết quả thẩm định giá trên chỉ được sử dụng cho một “mục đích thẩm định giá” duy nhất theo yêu cầu của khách hàng. Khách hàng phải hoàn toàn chịu trách nhiệm khi sử dụng sai mục đích đã yêu cầu.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('Khách hàng là chủ tài sản hoặc bên thứ ba yêu cầu thẩm định giá phải chịu hoàn toàn trách nhiệm về tính chính xác, hợp pháp các thông tin liên quan đến đặc điểm kỹ thuật, tính năng và tính pháp lý của tài sản thẩm định giá đã cung cấp cho ' . $company->name . ' tại thời điểm và địa điểm thẩm định giá.', 0, null, 'bullets', $indentFistLine);
        $section->addListItem('' . $company->name . ' không có trách nhiệm kiểm tra thông tin của những bản sao các giấy tờ liên quan đến tính chất pháp lý của tài sản yêu cầu thẩm định giá so với bản gốc. ', 0, null, 'bullets', array_merge($indentFistLine, $keepNext));
        // $section->addListItem('Thông tin quy hoạch được '.mb_strtoupper($company->acronym).' tham chiếu tại ứng dụng DNAILIS của Trung tâm Công nghệ thông tin Sở Tài nguyên và Môi trường Đồng Nai tại thời điểm Thẩm định giá, '.mb_strtoupper($company->acronym).' loại trừ trong trường hợp có cập nhật hoặc thay đổi sau thời điểm phát hành chứng thư.', 0, null, 'bullets', array_merge($indentFistLine, $keepNext));

        $section->addTextBreak(null, null, $keepNext);
        $table3 = $section->addTable($tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("THẨM ĐỊNH VIÊN VỀ GIÁ", ['bold' => true, 'size' => '13'], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        if (isset($certificate->appraiserConfirm->name)) {
            $cell32->addText("KT. TỔNG GIÁM ĐỐC", ['bold' => true, 'size' => '13'], ['align' => 'center', 'keepNext' => true]);
            $cell32->addText($certificate->appraiserConfirm->appraisePosition->description ?? '', ['bold' => true, 'size' => '13'], ['align' => 'center', 'keepNext' => true]);
        } else {
            $cell32->addText("" . $company->appraiser->appraisePosition->description ?? '', ['bold' => true, 'size' => '13'], ['align' => 'center', 'keepNext' => true]);
        }

        $table3->addRow(Converter::inchToTwip(1.5), $cantSplit);
        $table3->addCell(Converter::inchToTwip(4))->addText("", null, ['keepNext' => true]);
        $table3->addCell(Converter::inchToTwip(4))->addText("", null, ['keepNext' => true]);;
        $table3->addRow(Converter::inchToTwip(.1));
        $cell33 = $table3->addCell(Converter::inchToTwip(4));
        $bien171 = (isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : '';
        $cell33->addText($bien171, ['bold' => true, 'size' => '13'], ['align' => 'center', 'keepNext' => true]);
        $appraiserNumber =   isset($certificate->appraiser) ? $certificate->appraiser->appraiser_number : '';
        $cell33->addText("Số thẻ TĐV về giá: " .  $appraiserNumber, ['bold' => true, 'size' => '13'], ['align' => 'center']);
        $cell34 = $table3->addCell(Converter::inchToTwip(4));
        $appraiserManager = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->name : $certificate->appraiserManager->name;
        $appraiserManagerNumber = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->appraiser_number : $certificate->appraiserManager->appraiser_number;
        $bien172 = $appraiserManager;
        $cell34->addText($bien172, ['bold' => true, 'size' => '13'], ['align' => 'center', 'keepNext' => true]);
        $cell34->addText("Số thẻ TĐV về giá: " . $appraiserManagerNumber, ['bold' => true, 'size' => '13'], ['align' => 'center']);

        //Footer
        $comName =  $acronym;
        $createdName = isset($certificate->createdBy) ? CommonService::withoutAccents($certificate->createdBy->name) : '';
        if (isset($certificate->document_date) && !empty(trim($certificate->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $certificate->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        $reportID = 'HSTD_' . $certificate->id;
        $cvd = 'CVĐ_' . $certificate->document_num;
        $footer = $section->addFooter();
        $table = $footer->addTable();
        $table->addRow();
        $cell = $table->addCell(4500);
        $textrun = $cell->addTextRun();
        $textrun->addText($comName  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID . '/' . $cvd, array('size' => 8), array('align' => 'left'));
        $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right'));

        $reportUserName = CommonService::getUserReport();
        $reportName = '2_BC' . '_' . $reportUserName . '_' . $reportID . '_' . $comName;
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $fileName = $reportName . '_' . $downloadTime . '_' . $downloadDate;

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/' . 'comparison_brief/' . $now->format('Y') . '/' . $now->format('m');
        if (!File::exists(storage_path('app/public/' . $path))) {
            File::makeDirectory(storage_path('app/public/' . $path), 0755, true);
        }
        try {
            $objWriter->save(storage_path('app/public/' . $path . '/' . $fileName . '.docx'));
        } catch (\Exception $e) {
            throw $e;
        }
        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/' . $fileName . '.docx');
        $data['file_name'] = $fileName;
        return $data;
    }
}
