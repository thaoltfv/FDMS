<?php
namespace App\Services\Document\Appraisal;

use App\Services\CommonService;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;

class ReportAppraisalNova extends ReportAppraisal
{
    protected $gridSpan2 = ['gridSpan' => 2];
    protected $locationDescription = '- Mô tả vị trí';
    protected $statusDescription = '';

    // I
    protected function step1Sub1($section, $certificate)
    {
        $section->addTitle('Thông tin về khách hàng thẩm định giá:', 2);
        $section->addListItem('Khách hàng: ' . $certificate->petitioner_name, 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' . $certificate->petitioner_address, 0, null, 'bullets');
        $section->addListItem('Bên sử dụng kết quả thẩm định giá: Khách hàng yêu cầu thẩm định giá', 0, null, 'bullets');
    }

    protected function step1Sub2($section, $certificate)
    {
        $comAcronym = !empty($this->companyAcronym) ?  ' (' . mb_strtoupper($this->companyAcronym) . ')' : '';
        $section->addTitle('Thông tin về doanh nghiệp thẩm định giá:', 2);
        $section->addListItem('Doanh nghiệp: ' . $this->companyName .  $comAcronym, 0, null, 'bullets');
        $section->addListItem('Địa chỉ: ' . $this->companyAddress, 0, null, 'bullets');
        $section->addListItem("Điện thoại: " . $this->companyPhone . "\tFax: " . $this->companyFax, 0, null, 'bullets', 'leftTab');
        $section->addListItem('Họ và tên người Đại diện pháp luật: ' . ((isset($certificate->appraiserManager) && isset($certificate->appraiserManager->name)) ? $certificate->appraiserManager->name : ''), 0, null, 'bullets');
        if(isset($certificate->appraiserConfirm->name)) {
            $section->addListItem('Họ và tên người được uỷ quyền Đại diện pháp luật: ' . $certificate->appraiserConfirm->name, 0, null, 'bullets');
        }
        $section->addListItem('Họ và tên Thẩm định viên: ' . ((isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : ''), 0, null, 'bullets');
        $section->addListItem('Người lập báo cáo: ' . (isset($certificate->createdBy->name) ? $certificate->createdBy->name : ''), 0, null, 'bullets');

    }
    protected function step1Sub4($section, $certificate)
    {
        $section->addTitle('Thông tin về cuộc thẩm định giá:', 2);
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Hợp đồng thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText( $this->contractCode . ' ' . $this->documentLongDateText . " giữa " . $this->companyName . " và " . $certificate->petitioner_name . '.');
        $appraiseDate = date_create($certificate->appraise_date);
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Thời điểm thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText('Tháng ' . date_format($appraiseDate, "m/Y") . '.', null, []);
        $appraisePurpose = isset($certificate->appraisePurpose->name) ? $certificate->appraisePurpose->name : '';
        if ($appraisePurpose === 'Vay vốn ngân hàng')
            $appraisePurpose = 'Tư vấn giá trị tài sản để ngân hàng tham khảo và xem xét quyết định hạn mức để cấp tín dụng';
        $listTmp = $section->addListItemRun(0, 'bullets');
        $listTmp->addText('Mục đích thẩm định giá: ', ['bold' => true], []);
        $listTmp->addText($appraisePurpose . '.', null, []);
        $this->step1Sub4NovaInfo($section);
    }
    private function step1Sub4NovaInfo($section)
    {
        $textRun = $section->addListItemRun(0, 'bullets');
        $textRun->addText('Các nguồn thông tin được sử dụng trong quá trình thẩm định giá và mức độ kiểm tra, thẩm định các nguồn thông tin đó:', ['bold' => true], $this->cellVCenteredKeepNext);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(4000, $this->cellVCentered)->addText('Các nguồn thông tin thu thập', ['bold' => true], $this->cellVCenteredKeepNext);
        $table->addCell(6000, $this->cellVCenteredKeepNext)->addText('Mức độ kiểm tra, thẩm định các nguồn thông tin', ['bold' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(10000, $this->gridSpan2)->addText(' 1. Đối với tài sản thẩm định giá', ['bold' => true, 'italic' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $cell =  $table->addCell(4000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Hồ sơ pháp lý của tài sản thẩm định giá do khách hàng cung cấp.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Tổ thẩm định giá tiến hành khảo sát hiện trạng tài sản thẩm định giá dưới sự hướng dẫn của khách hàng yêu cầu thẩm định giá hoặc người được khách hàng yêu cầu thẩm định giá ủy quyền.' . '.', null, $this->cellVCentered);
        $cell =  $table->addCell(6000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Hồ sơ pháp lý do khách hàng cung cấp bản photocopy được đính kèm báo cáo kết quả thẩm định giá này.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Diễn biến và kết quả khảo sát hiện trường được ghi nhận tại Biên bản thẩm định hiện trạng tài sản của '. $this->companyName .' được lưu trữ trong hồ sơ thẩm định giá. ' . '.', null, $this->cellVCentered);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(10000, $this->gridSpan2)->addText(' 2. Đối với các giao dịch chào mua / chào bán / thành công của các tài sản tương tự trên thị trường', ['bold' => true, 'italic' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $cell =  $table->addCell(4000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Nguồn cung cấp thông tin là những người tham gia giao dịch mua bán tài sản, người được ủy quyền chào mua / chào bán tài sản, các lực lượng trung gian trên thị trường có thông tin về giao dịch mua – bán tài sản trên thị trường (môi giới, văn phòng công chứng, văn phòng đăng ký quyền sử dụng đất)', null, null);
        $cell =  $table->addCell(6000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Chuyên viên thẩm định thu thập thông tin bằng cách phỏng vấn trực tiếp hoặc qua điện thoại người có thông tin hoặc có khả năng cung cấp thông tin kết hợp với khảo sát thực tế tài sản.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thẩm định viên kiểm tra đối với thông tin thu thập tại thời điểm thẩm định giá.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Bộ phận kiểm soát định kỳ kiểm tra thông tin thu thập tại thời điểm gần với thời điểm thẩm định giá.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Kết quả thu thập thông tin thông qua phỏng vấn trực tiếp được ghi nhận tại phiếu thu thập thông tin bất động sản được lưu trữ trong hồ sơ thẩm định giá.', null, null);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(10000, $this->gridSpan2)->addText(' 3. Đối với các thông tin khác', ['bold' => true, 'italic' => true], $this->cellVCenteredKeepNext);
        $table->addRow(400, $this->cantSplit);
        $cell =  $table->addCell(4000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin đăng tải trên các phương tiện truyền thông', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin trên các văn bản quy phạm pháp quy của nhà nước', null, null);
        $cell =  $table->addCell(6000, $this->cellVCentered);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin đăng tải trên các phương tiện truyền thông được chuyên viên thẩm định thu thập và kiểm tra qua nhiều nguồn đăng tải được nêu rõ trong báo cáo thẩm định giá. Thẩm định viên kiểm tra thông tin thu thập tại thời điểm thẩm định giá.', null, null);
        $listTmp = $cell->addListItemRun(0, 'bullets');
        $listTmp->addText('Thông tin trích dẫn từ các văn bản quy phạm pháp quy của nhà nước được sử dụng trên cơ sở trích dẫn trực tiếp nội dung cụ thể trong báo cáo thẩm định giá.', null, null);
    }
    //IV
    protected function assetCharacteristicsAppraiseLegal($table, $appraise)
    {
        $strToThua = $this->getToThua($appraise);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('1', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Pháp lý', null, $this->styleAlignLeft);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Số địa chính', null, $this->styleAlignLeft);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
        ->addText($strToThua . ', ' . $appraise->ward->name . ', ' . $appraise->district->name . ', ' . $appraise->province->name . '.', null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Vị trí hành chính', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($appraise->full_address, null, ['align' => 'left']);

        $propertyData = $this->getAppraisePropertyData($appraise);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);

        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Diện tích lô đất', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(number_format($propertyData['total_area'], 2, ',', '.') . $this->m2 . '.', null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Mục đích sử dụng', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($propertyData['mdsd'], null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $thsd = "";
        $stt = 0;
        $existLandTypePurpose = [];
        if (isset($appraise->appraiseLaw[0])) {
            $appraiseLaw = $appraise->appraiseLaw[0];
            $thsd = $appraiseLaw->duration;
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Thời hạn sử dụng', null, ['align' => 'left']);
        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($thsd, null, ['align' => 'left']);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Nguồn gốc sử dụng', null, ['align' => 'left']);
        $cell = $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none']);

        if (isset($appraise->appraiseLaw[0])) {
            $appraiseLaw = $appraise->appraiseLaw[0];
            $textRun = $cell->addTextRun(['align' => 'left']);
            $textRun->addText($appraiseLaw->origin_of_use, null, ['align' => 'left']);
        }
        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $zoning = "";
        $stt = 0;
        $existLandTypePurpose = [];
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Quy hoạch', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
                ->addText($propertyData['desciptionZoning'], null, ['align' => 'left']);
    }
    protected function assetCharacteristicsAppraiseLocation($table, $appraise)
    {
        $table->addRow(400, $this->cantSplit);
        $table->addCell(600, ['valign' => 'center', 'vMerge' => 'restart'])->addText('2', null, $this->cellHCentered);
        $table->addCell(2000, ['valign' => 'center', 'vMerge' => 'restart'])->addText('Vị trí', null, ['align' => 'left']);
        $coordinates = explode(",", $appraise->coordinates);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ X', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($coordinates[0], null, ['align' => 'left']);

        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Tọa độ Y', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText($coordinates[1], null, ['align' => 'left']);

        $table->addRow(400, $this->cantSplit);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $table->addCell(null, ['valign' => 'center', 'vMerge' => 'continue']);
        $positionType = "";
        foreach ($appraise->properties as $index => $property) {
            $positionType .= ($index) ? ', ' : '';
            $positionType .= (isset($property->description) ? $property->description : '');
        }
        $table->addCell($this->rowThirdWidth, ['borderRightSize' => 'none'])->addText('- Mô tả vị trí', null, ['align' => 'left']);

        $table->addCell($this->rowFourthWidth, ['borderLeftSize' => 'none'])
            ->addText(htmlspecialchars($positionType), null, ['align' => 'left']);
    }
    //V
    protected function getPrincipleOfValuationDescription($certificatePrinciple, $section)
    {
        $section->addListItem('Tổ thẩm định giá vận dụng các nguyên tắc sử dụng tốt nhất và hiệu quả nhất, nguyên tắc dự kiến lợi ích trong tương lai, nguyên tắc tăng và giảm thu nhập, nguyên tắc cung cầu và cạnh tranh, nguyên tắc thay thế, nguyên tắc thay đổi, nguyên tắc phù hợp, nguyên tắc cân bằng.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Phân tích sử dụng tốt nhất và hiệu quả nhất của tài sản thẩm định: Căn cứ vào đặc điểm pháp lý, đặc điểm kinh tế kỹ thuật của tài sản. Khu vực được quy hoạch để ở, xung quanh các lô đất tương tự là đất trống, đất đã có công trình xây dựng nhà ở. Hiện trạng thửa đất cũng được sử dụng để xây dựng công trình xây dựng phù hợp với quy hoạch và được sử dụng để ở. Tổ thẩm định nhận thấy tài sản thẩm định đã được sử dụng tốt nhất và có hiệu quả nhất.', 0, null, 'bullets', $this->indentFistLine);
    }
    //VII
    protected function step7(Section $section, $certificate)
    {
        $section->addTitle('CÁC GIẢ THIẾT VÀ GIẢ THIẾT ĐẶC BIỆT:', 1);
        $section->addListItem('Giả thiết: Không có.', 0, null, 'bullets');
        $section->addListItem('Giả thiết đắc biệt: Không có.', 0, null, 'bullets');
    }
    //VIII
    protected function step8(Section $section, $certificate)
    {
        $section->addTitle('CÁCH TIẾP CẬN VÀ PHƯƠNG PHÁP THẨM ĐỊNH GIÁ:', 1);
        $section->addTitle('Thông tin tổng quan về thị trường', 2);
        $section->addListItem('Tại thời điểm thẩm định giá, tình hình thị trường bất động sản tại khu vực tài sản thẩm định giá tọa lạc hoạt động tương đối ổn định. Giao dịch mua bán chủ yếu thực hiện trực tiếp giữa người mua và người bán qua kênh thông tin quảng cáo, rao vặt trên báo giấy và internet. Thông tin về tài sản được niêm yết trên các trang quảng cáo, rao vặt tương đối chính xác.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Giá giao dịch được hình thành trên cơ sở thỏa thuận trực tiếp giữa người mua và người bán. Do đó, giá giao dịch thành công của từng bất động sản phần nào chịu tác động bởi tính chủ quan của người tham gia giao dịch.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Giá chào bán bất động sản phổ biến cao hơn giá giao dịch thành công từ 5% - 10%. Thời gian chào bán và giao dịch mua bán bất động sản trên thị trường dao động phổ biến từ 03 đến 10 tháng.', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Thông tin về thị trường giao dịch của nhóm (loại) TSTĐG', 2);
        $section->addListItem('Tại thời điểm thẩm định giá, tình hình giao dịch tại khu vực chủ yếu là đất nền, nhà phố với quy mô diện tích đa dạng, khu vực dân cư tập trung đông, điều kiện giao thông thuận lợi nên việc mua bán bất động sản diễn ra tương đối sôi nổi.', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Thực trạng và triển vọng cung cầu của nhóm (loại) tài sản thẩm định giá', 2);
        $section->addListItem('Thực trạng cung - cầu:', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Thực trạng cung: Chủ yếu từ người dân tại khu vực có nhà cần bán', 1, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Thực trạng cầu: Nguồn cầu chủ yếu từ người dân tại khu vực và các vùng lân cận', 1, null, 'bullets', $this->indentFistLine);

        $section->addListItem('Triển vọng cung - cầu:', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Triển vọng cung: Điều kiện giao thông thuận lợi, khu dân cư hiện hữu, gần trường học, chợ nên giá bất động sản có chiều hướng tăng.', 1, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Triển vọng cầu: Chủ yếu từ người dân khu vực và các vùng lân cận', 1, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Phân tích về các tài sản thay thế hoặc cạnh tranh', 2);
        $section->addListItem('Tài sản thẩm định giá là bất động sản có thể thay thế bởi các bất động sản khác trên cùng tuyến đường tại khu vực và các tuyến đường khác trong khu vực có cùng các điều kiện tự nhiên, kinh tế, hạ tầng và các tiện ích chung trong khu vực.', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('Thông tin về các yếu tố kinh tế, xã hội và các yếu tố khác có ảnh hưởng đến giá trị tài sản thẩm định giá', 2);

        $table = $section->addTable($this->styleTable);
        $table->addRow(400, array_merge($this->cantSplit, $this->rowHeader));
        $table->addCell(2000, $this->cellVCentered)->addText('Các yếu tố', null, $this->keepNext);
        $table->addCell(8000, $this->cellVCentered)->addText('Nội dung thông tin', null, $this->keepNext);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(2000, $this->cellVCentered)->addText('Kinh tế', null, $this->keepNext);
        $table->addCell(8000, $this->cellVCentered)->addText('- Tài sản nằm trong khu vực dân cư sinh sống đông đúc, gần trường học, chợ.', null, $this->keepNext);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(2000, $this->cellVCentered)->addText('Xã hội', null, $this->keepNext);

        $cell = $table->addCell(8000, $this->cellVCentered);
        $cell->addText('- Hạ tầng kỹ thuật: Hạ tầng kỹ thuật hiện hữu, hoàn thiện.');
        $cell->addText('- Hệ thống cấp nước: Hoàn chỉnh.');
        $cell->addText('- Hệ thống thoát nước: Hoàn chỉnh.');
        $cell->addText('- Hệ thống điện: Hoàn chỉnh.');
        $cell->addText('- Dịch vụ bưu chính viễn thông: Khu có dịch vụ viễn thông hoàn chỉnh.');
        $cell->addText('- Môi trường dân cư: Khu vực dân cư sinh sống đông đúc.');

        $table->addRow(400, $this->cantSplit);
        $table->addCell(2000, $this->cellVCentered)->addText('Các yếu tố khác', null, $this->keepNext);
        $table->addCell(8000, $this->cellVCentered)->addText('- Quy mô diện tích: Có ảnh hưởng đến tính thanh khoản của tài sản.', null, $this->keepNext);

        $section->addTitle('Căn cứ lựa chọn phương pháp', 2);
        $certificatePrinciple = "";
        foreach ($certificate->certificatePrinciple as $index => $item) {
            $certificatePrinciple .= ($index) ? ' và ' : '';
            $certificatePrinciple .= (isset($item->name)) ? $item->name : '';
        }
        $section->addListItem('Tài sản thẩm định giá có đầy đủ giấy tờ pháp lý, phù hợp quy hoạch và sử dụng đúng mục đích công năng đem lại giá trị lớn nhất cho tài sản. Tài sản thẩm định đáp ứng với các yêu cầu theo ' . $certificatePrinciple . '.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Điều kiện, tính chất thông tin thị trường: Dữ liệu thị trường về các tài sản giao dịch có đặc điểm tương đồng với TSTĐG tương đối phổ biến và đầy đủ nên việc sử dụng phương pháp so sánh để xác định giá trị tài sản thẩm định giá là phù hợp và đáng tin cậy.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Ngoài ra nguồn dữ liệu và thông tin có thể sử dụng để xác định giá trị tài sản thẩm định giá theo phương pháp khác là rất hạn chế. Vì vậy, căn cứ mục đích thẩm định giá của tài sản ' . $this->acronym . ' sử dụng phương pháp so sánh là phù hợp.', 0, null, 'bullets', $this->indentFistLine);
        $section->addTitle('Cách tiếp cận, phương pháp thẩm định giá áp dụng:', 2);
        if ($this->isApartment) {
            $this->step8sub3Apartment($section);
        } else {
            $this->step8sub3Appraise($section);
        }
        $section->addTitle('Xác định giá trị tài sản cần thẩm định giá:', 2);
        foreach ($this->realEstates as $stt =>  $realEstate) {
            if ($realEstate->assetType->acronym == 'CC') {
               $this->step8Sub4Apartment($section, $stt);
            } else {
                $this->step8Sub4Appraise($section, $stt);
            }
        }
    }
    protected function step8sub3Appraise (Section $section)
    {
        $section->addText('Căn cứ vào các phương pháp thẩm định giá theo Tiêu chuẩn TĐGVN, Tổ thẩm định nhận thấy:', null, $this->indentFistLine);
        $section->addListItem('Đối với phương pháp so sánh: Tài sản thẩm định giá gồm quyền sử dụng đất, công trình xây dựng trên đất. Tại thời điểm thẩm định, khu vực thẩm định không có các giao dịch trong đó các tài sản so sánh có quyền sử dụng đất, công trình xây dựng trên đất tương đồng với tài sản thẩm định. Trong trường hợp này tổ thẩm định nhận định không đủ điều kiện áp dụng được phương pháp so sánh để ước tính giá trị của tài sản thẩm định là quyền sử dụng đất và công trình xây dựng trên đất. Do đó, tổ thẩm định chỉ áp dụng phương pháp so sánh để ước tính giá trị quyền sử dụng đất.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Đối với phương pháp chiết trừ: Tổ thẩm định có thu thập được thông tin giao dịch của thửa đất có tài sản gắn liền với đất tương tự với quyền sử dụng đất cần định giá. Do đó, tổ thẩm định nhận thấy đủ điều kiện để áp dụng phương pháp chiết trừ để tiến hành ước tính giá trị quyền sử dụng đất của tài sản cần thẩm định giá bằng cách loại trừ phần giá trị tài sản gắn liền với đất ra khỏi tổng giá trị bất động sản.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Đối với phương pháp vốn hóa trực tiếp và dòng tiền chiết khấu: Do khách hàng không cung cấp được thông tin dòng thu nhập do bất động sản mang lại. Vì vậy, trong trường hợp này chưa đủ điều kiện áp dụng phương pháp vốn hóa trực tiếp để ước tính giá trị tài sản cần thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Đối với phương pháp thặng dư: Phương pháp này áp dụng đối với trường hợp thửa đất trống có tiềm năng phát triển hoặc đất có CTXD có thể cải tạo, sửa chữa để khai thác có hiệu quả nhất. Do thửa đất hiện tại đã có CTXD phục vụ vào mục đích để ở đang khai thác tốt nhất và hiệu quả nhất và trong tương lai cũng chưa có thông tin quy hoạch phát triển và phương án sửa dụng tối ưu hơn. Vì vậy, chưa đủ điều kiện áp dụng được phương pháp thặng dư trong trường hợp này.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Phương pháp chi phí thay thế: Tổ thẩm định xác định giá trị công trình xây dựng của tài sản thẩm định giá dựa trên cơ sở chênh lệch giữa chi phí thay thế để tạo ra một tài sản công trình xây dựng tương tự tài sản thẩm định giá có cùng chức năng, công dụng theo giá thị trường hiện hành và giá trị hao mòn của công trình xây dựng tài sản thẩm định giá. Do đó, Tổ thẩm định nhận thấy đủ điều kiện để áp dụng phương pháp chi phí thay thế để tiến hành ước tính giá trị tài sản cần thẩm định giá là công trình xây dựng trên đất.', 0, null, 'bullets', $this->indentFistLine);

        $section->addText('Từ các nội dung trên, Tổ thẩm định nhận thấy áp dụng được phương pháp so sánh làm phương pháp chính để ước tính giá trị tài sản thẩm định là quyền sử dụng đất và phương pháp chi phí thay thế làm phương pháp chính để ước tính giá trị tài sản thẩm định là công trình xây dựng trên đất.', ['bold' => true], $this->indentFistLine);
        $section->addText('“Phương pháp so sánh là phương pháp thẩm định giá, xác định giá trị của tài sản thẩm định giá dựa trên cơ sở phân tích mức giá của các tài sản so sánh để ước tính, xác định giá trị của tài sản thẩm định giá. Phương pháp so sánh thuộc cách tiếp cận từ thị trường”.', ['italic' => true], $this->indentFistLine);
        $section->addText('“Phương pháp chi phí thay thế là phương pháp thẩm định giá xác định giá trị của tài sản thẩm định giá dựa trên cơ sở chênh lệch giữa chi phí thay thế để tạo ra một tài sản tương tự tài sản thẩm định giá có cùng chức năng, công dụng theo giá thị trường hiện hành và giá trị hao mòn của tài sản thẩm định giá. Phương pháp chi phí thay thế thuộc cách tiếp cận từ chi phí”.', ['italic' => true], $this->indentFistLine);
    }

    protected function step9(Section $section, $certificate)
    {
        $section->addTitle('KẾT QUẢ THẨM ĐỊNH GIÁ:', 1);
        $section->addText('Trên cơ sở các tài liệu do ' . $certificate->petitioner_name . ' cung cấp, với phương pháp thẩm định giá như trên được áp dụng trong tính toán, '. $this->companyName .' thông báo kết quả thẩm định giá như sau:', null, $this->indentFistLine);
        if ($this->isApartment) {
            $this->step9Apartment($section);
        } else {
            $this->step9Appraise($section, $certificate);
        }
        $section->addText('Căn cứ Tiêu chuẩn thẩm định giá 05 Ban hành kèm theo Thông tư số 28/2015/TT-BTC ngày 06/3/2015 của Bộ Tài chính: Thời hạn sử dụng kết quả thẩm định giá trong chứng thư tính từ ngày phát hành là 06 tháng kể từ ngày phát hành chứng thư thẩm định giá.', null, $this->indentFistLine);
    }

    protected function printAppendix(Section $section, $certificate)
    {
        $section->addListItem('Kết quả thẩm định giá trên chỉ xác nhận giá trị thị trường cho tài sản thẩm định có đặc điểm pháp lý và đặc điểm kinh tế - kỹ thuật và hiện trạng được mô tả chi tiết tại thời điểm thẩm định được ghi trong báo cáo thẩm định giá này.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('' . $this->acronym . ' mô tả đặc điểm pháp lý và đặc điểm kinh tế - kỹ thuật của tài sản thẩm định giá dựa trên các tài liệu, chứng từ, hồ sơ pháp lý liên quan đến tài sản thẩm định giá do khách hàng cung cấp và ghi nhận hiện trạng tài sản theo sự hướng dẫn của khách hàng hoặc người hướng dẫn do khách hàng chỉ định. Trong trường hợp có sự sai khác về các đặc điểm đã mô tả có khả năng dẫn đến sự thay đổi của kết quả thẩm định giá. Khách hàng và các bên liên quan khi sử dụng kết quả thẩm định giá trên xem như đã hiểu rõ về vấn đề này và đưa ra quyết định phù hợp với mục đích thẩm định giá đã ghi trong báo cáo này', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Kết quả thẩm định giá trên được tính toán trong điều kiện thị trường bình thường tại thời điểm thẩm định giá. Những biến động bất thường của thị trường hay chính sách trong tương lai có ảnh hưởng đến giá trị của tài sản không được xem xét trong báo cáo này.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Kết quả thẩm định giá phải được sử dụng đúng đối tượng và mục đích đã ghi trong báo cáo này. ' . $this->acronym . ' không chịu trách nhiệm trong mọi trường hợp khách hàng hoặc bên thứ ba không đúng đối tượng sử dụng kết quả thẩm định giá sai mục đích.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Kết quả thẩm định giá trên chỉ có giá trị khi và chỉ khi các bên tham gia tuân thủ và hoàn thành các điều khoản trong hợp đồng cung cấp dịch vụ thẩm định giá. Trong trường hợp khách hàng không thực hiện đầy đủ các nghĩa vụ (bao gồm nghĩa vụ thanh toán) được ghi trong hợp đồng thì ' . $this->acronym . ' mặc nhiên coi là hợp đồng vô hiệu và toàn bộ Chứng thư / Báo cáo thẩm định giá đã thỏa thuận giao trước cho khách hàng (nếu có) sẽ không có giá trị pháp lý.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Chứng thư này phát hành chỉ để tư vấn giá trị tài sản cho khách hàng làm cơ sở tham khảo để xem xét cân nhắc và tự quyết định theo mục đích đã yêu cầu. ' . $this->acronym . ' không đi sâu tìm hiểu kỹ nguồn gốc chủ quyền. Chủ quyền sở hữu tài sản là do các cơ quan có thẩm quyền cấp hoặc được xác định theo quy định của pháp luật hiện hành.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Về pháp lý tài sản thẩm định giá: Khách hàng cung cấp các tài liệu, chứng từ, hồ sơ pháp lý liên quan đến tài sản thẩm định giá như trên bằng bản sao (bản photo). Khách hàng chịu trách nhiệm về tính chính xác của hồ sơ đã cung cấp. ' . $this->acronym . ' không kiểm tra sự phù hợp giữa bản chính và bản chụp hồ sơ pháp lý khách hàng cung cấp. Kết quả thẩm định giá được nêu trong báo cáo này dựa trên giả định các tài liệu, chứng từ, hồ sơ pháp lý khách hàng cung cấp là trung thực và đúng với hiện trạng pháp lý của tài sản tại thời điểm thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Khách hàng yêu cầu thẩm định giá hoặc người hướng dẫn được khách hàng chỉ định đã hướng dẫn thẩm định viên/ chuyên viên ' . $this->acronym . ' thực hiện khảo sát / thẩm định hiện trạng tài sản phải chịu hoàn toàn trách nhiệm về thông tin liên quan đến đặc điểm kinh tế - kỹ thuật, tính năng và tính pháp lý của tài sản thẩm định giá đã cung cấp cho ' . $this->acronym . ' tại thời điểm và địa điểm thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Hiện trạng của tài sản thẩm định giá được ghi nhận tại thời điểm khảo sát hiện trạng tài sản. ' . $this->acronym . ' không chịu trách nhiệm nếu có phát sinh các hư hỏng, phá bỏ, thay đổi kết cấu hiện trạng của nó hay thay đổi chủ sở hữu trong quá trình sử dụng sau thời điểm khảo sát hiện trạng tài sản thẩm định giá.', 0, null, 'bullets', $this->indentFistLine);
        $section->addListItem('Thẩm định viên và những người tham gia trực tiếp không có quan hệ kinh tế hoặc quyền lợi kinh tế như góp vốn cổ phần, cho vay hoặc vay vốn từ khách hàng, không là cổ đông chi phối của khách hàng hoặc ký kết hợp đồng gia công dịch vụ, đại lý tiêu thụ hàng hóa và không có xảy ra bất kỳ xung đột lợi ích nào', 0, null, 'bullets', $this->indentFistLine);

        $section->addTitle('CÁC TÀI LIỆU KÈM THEO:', 1);
        $section->addListItem('Hồ sơ pháp lý tài sản thẩm định.', 0, null, 'bullets', $this->indentFistLine);
        $section->addText('Báo cáo kết quả thẩm định giá được phát hành 03 bản chính Tiếng Việt, kèm theo Chứng thư thẩm định giá số ' . $this->certificateCode . ' ngày ' . $this->certificateShortDateText . ' tại ' . $this->companyName, ['italic' => true], array_merge($this->indentFistLine, $this->keepNext));
    }
    protected function signature(Section $section, $certificate)
    {
        $section->addTextBreak(null, null, $this->keepNext);
        $table3 = $section->addTable($this->tableBasicStyle);
        $table3->addRow(Converter::inchToTwip(.1), $this->cantSplit);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("Chuyên Viên Thẩm Định", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell31 = $table3->addCell(Converter::inchToTwip(4));
        $cell31->addText("Thẩm Định Viên Về Giá", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell32 = $table3->addCell(Converter::inchToTwip(4));
        if(isset($certificate->appraiserConfirm->name)) {
            $cell32->addText("KT. Tổng Giám Đốc", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $cell32->addText( $certificate->appraiserConfirm->appraisePosition->description, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        } else {
            $cell32->addText("Tổng Giám Đốc", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        }
        $table3->addRow(Converter::inchToTwip(1.5), $this->cantSplit);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);
        $table3->addCell(Converter::inchToTwip(4))->addText("",null,  $this->keepNext);

        $table3->addRow(Converter::inchToTwip(.1));
        $cell33 = $table3->addCell(Converter::inchToTwip(4));
        $bien170 = (isset($certificate->appraiserPerform) && isset($certificate->appraiserPerform->name)) ? $certificate->appraiserPerform->name : '';
        $cell33->addText($bien170, ['bold' => true], ['align' => 'center', 'keepNext' => true]);

        $cell33 = $table3->addCell(Converter::inchToTwip(4));
        $bien171 = (isset($certificate->appraiser) && isset($certificate->appraiser->name)) ? $certificate->appraiser->name : '';
        $cell33->addText($bien171, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $appraiserNumber =   isset($certificate->appraiser) ? $certificate->appraiser->appraiser_number : '';
        $cell33->addText("Số thẻ TĐV về giá: " . $appraiserNumber, ['bold' => true], ['align' => 'center']);
        $cell34 = $table3->addCell(Converter::inchToTwip(4));

        $appraiserManager = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->name : $certificate->appraiserManager->name;
        $appraiserManagerNumber = (isset($certificate->appraiserConfirm->name)) ? $certificate->appraiserConfirm->appraiser_number : $certificate->appraiserManager->appraiser_number;
        $bien172 = $appraiserManager;
        $cell34->addText($bien172, ['bold' => true], ['align' => 'center', 'keepNext' => true]);
        $cell34->addText("Số thẻ TĐV về giá: " . $appraiserManagerNumber, ['bold' => true], ['align' => 'center']);
    }
}
