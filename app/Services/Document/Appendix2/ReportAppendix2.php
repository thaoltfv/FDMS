<?php

namespace App\Services\Document\Appendix2;

use App\Services\CommonService;
use App\Services\Document\DocumentInterface\Report;
use Illuminate\Support\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;

class ReportAppendix2 extends Report
{
    protected $isPrintNational = false;
    protected $isOnlyAsset = false;
    protected $realEstates = [];
    protected $isTangibleAsset = false;
    protected $total = [];
    #footer, reportname, path
    public function getFooterString($data)
    {
        $data = (object)$data;
        $createdName =  $this->createdName;
        if (isset($data->document_date) && !empty(trim($data->document_date))) {
            $yearCVD = Carbon::createFromFormat('Y-m-d',  $data->document_date)->format('Y');
        } else {
            $yearCVD = "        ";
        }
        if (is_countable($data->realEstate)) $reportID = 'HSTD_' . $data->id;
        else $reportID = 'TSTD_' . $data->id;

        return mb_strtoupper($this->envDocument)  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID;
    }
    public function getReportName()
    {
        return '4_PL2';
    }
    protected function signature(Section $section, $certificate)
    {
        ////không có chữ ký
    }
    public function printTitle(Section $section, $data)
    {
        $this->setProperties($data);
        $section->addImage($this->logoUrl, $this->styleImageLogo);
        $section->addText('PHỤ LỤC 2 KÈM THEO BÁO CÁO THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);
        $section->addText('NHÀ CỬA VẬT KIẾN TRÚC', ['italic' => true, 'size' => 14], ['align' => 'center']);
    }
    private function setProperties($data)
    {
        $this->isOnlyAsset = (!is_countable($data->realEstate) || count($data->realEstate) == 1);
        $this->realEstates = $data->realEstate;
        $this->isTangibleAsset = in_array('DCN', $data->document_type);
        $this->styleTable = [
            'borderSize' => 1,
            'align' => JcTable::START
        ];
    }

    protected function printAssetInfo($section, $name, $address)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tên tài sản: ', ['size' => 13, 'bold' => true]);
        $textRun->addText($name ? $name : '', ['size' => 13, 'bold' => false]);
        if (!empty($address)) {
            $textRun = $section->addTextRun();
            $textRun->addText('     - Địa chỉ: ', ['size' => 13, 'bold' => true]);
            $textRun->addText($address ? $address : '', ['size' => 13, 'bold' => false]);
        }
    }

    protected function printContentByRealEstate($section, $realEstate, $key)
    {
        $appraise = $realEstate->appraises;
        $tangibleAssets = $appraise->tangibleAssets;
        $this->total = [];
        if ($this->isOnlyAsset) {
            $textRun = $section->addTextRun();
            $textRun->addText('     ' . ($key + 1) . '. Tài sản thẩm định:', ['bold' => true, 'size' => 14]);
        } else {
            $textRun = $section->addTextRun('Heading2');
            $textRun->addText('Tài sản thẩm định ' . ($key + 1) . ': ', ['bold' => true, 'size' => 14]);
        }
        if ($appraise->assetType->description == "ĐẤT TRỐNG") {
            $textRun->addText('Tài sản thẩm định không có công trình xây dựng', ['size' => 13, 'bold' => false]);
            return;
        }
        $this->printAssetInfo($section, $realEstate->appraise_asset, $appraise->full_address);
        // $textRun->addText('Công trình xây dựng tọa lạc tại ' . ($appraise->ward ? $appraise->ward->name : ''), ['size' => 13, 'bold' => false]);
        // $textRun->addText(', ' . ($appraise->district ? $appraise->district->name : ''), ['size' => 13, 'bold' => false]);
        // $textRun->addText(', ' . ($appraise->province ? $appraise->province->name : ''), ['size' => 13, 'bold' => false]);

        $dgxdSlug = 'dg-uoc-tinh';
        $appraiseDgxd = $appraise->appraisal_dgxd;
        if (!empty($appraiseDgxd)) {
            $dgxdSlug = $appraiseDgxd->slug_value;
        }

        $this->printOriginalPriceDescription($section, $dgxdSlug);
        $this->printBuildingComapanyInfo($section, $tangibleAssets, $dgxdSlug);
        $this->printBuildingPriceChoosed($section, $dgxdSlug);
        $this->printRemainQualityDescription($section);
        $remainQualitySlug = 'trung-binh-cong';
        $appraisalCLCL = $appraise->appraisal_clcl;
        if (!empty($appraisalCLCL)) {
            $remainQualitySlug = $appraisalCLCL->slug_value;
        }
        if ($remainQualitySlug == 'trung-binh-cong' || $remainQualitySlug == 'tuoi-doi') {
            $this->printRemainQualityFunc1($section, $tangibleAssets);
        }
        if ($remainQualitySlug == 'trung-binh-cong' || $remainQualitySlug == 'chuyen-gia') {
            $this->printRemainQualityFunc2($section, $tangibleAssets);
        }
        if ($remainQualitySlug == 'trung-binh-cong') {
            $this->printRemainQualityFuncAvg($section, $tangibleAssets, $appraisalCLCL);
        }
    }
    public function printContent(Section $section, $data)
    {
        $realEstates = $this->realEstates;
        if (is_countable($realEstates)) {
            $count = count($realEstates);
            foreach ($realEstates as $key => $realEstate) {
                $this->printContentByRealEstate($section, $realEstate, $key);
                if (($key < ($count - 1)) && ($realEstates[$key + 1]->assetType->description != "ĐẤT TRỐNG")) {
                    $section->addPageBreak();
                }
            }
        } else {
            $this->printContentByRealEstate($section, $realEstates, 0);
        }
    }
    protected function printOriginalPriceDescription($section, $dgxdSlug)
    {
        $section->addText('❖ Về nguyên giá của nhà cửa, vật kiến trúc:', ['bold' => true, 'size' => 13], ['align' => 'left']);
        $textRun = $section->addTextRun();
        $textRun->addText('- Giá trị công trình xây dựng ' . $this->acronym . ' căn cứ vào đặc điểm kết cấu, kiến trúc, khẩu độ, chiều cao, công năng sử dụng, vật liệu sử dụng…. trên cơ sở những thông tin, tài liệu thu thập và phương pháp thẩm định giá được lựa chọn tại phần 3, mục VIII của Báo cáo này, mức giá ước tính như sau:');
    }
    protected function printBuildingComapanyInfo($section, $tangibleAssets, $dgxdSlug)
    {
        $section->addText('BẢNG TỔNG HỢP THÔNG TIN TSTĐG VÀ TSSS', null, ['align' => 'center']);
        $section->addText('Đvt: đ/' . $this->m2, ['italic' => true], ['align' => 'right', 'keepNext' => true]);
        $table = $section->addTable($this->styleTable);
        $this->printBuildingPriceDetail($table, $tangibleAssets, $dgxdSlug);
    }

    protected function printBuildingPriceDetail($table, $tangibleAssets, $dgxdSlug)
    {
        $constructionCompany = $tangibleAssets[0]->constructionCompany;
        $com1 = count($constructionCompany) && $constructionCompany[0] ? $constructionCompany[0]->name : '';
        $com2 = count($constructionCompany) && $constructionCompany[1] ? $constructionCompany[1]->name : '';
        $com3 = count($constructionCompany) && $constructionCompany[2] ? $constructionCompany[2]->name : '';
        $table->addRow(400, $this->rowHeader);
        $table->addCell(2500, $this->cellRowSpan)->addText('Tên tài sản', ['bold' => true], $this->cellHCentered);
        $table->addCell(500, $this->cellRowSpan)->addText('Đvt', ['bold' => true], $this->cellHCentered);
        $table->addCell(1500, $this->cellRowSpan)->addText($com1, ['bold' => true], $this->cellHCentered);
        $table->addCell(1500, $this->cellRowSpan)->addText($com2, ['bold' => true], $this->cellHCentered);
        $table->addCell(1500, $this->cellRowSpan)->addText($com3, ['bold' => true], $this->cellHCentered);
        $table->addCell(1500, $this->cellRowSpan)->addText('Đơn giá trung bình', ['bold' => true], $this->cellHCentered);
        $table->addCell(1500, $this->cellRowSpan)->addText('Đơn giá quyết định', ['bold' => true], $this->cellHCentered);
        foreach ($tangibleAssets as $tangibleAsset) {
            $name = $tangibleAsset->tangible_name;
            $startUsingYear = $tangibleAsset->start_using_year;
            $unitPrice1 = isset($tangibleAsset->constructionCompany[0]) ? $tangibleAsset->constructionCompany[0]->unit_price_m2 : 0;
            $unitPrice2 = isset($tangibleAsset->constructionCompany[1]) ? $tangibleAsset->constructionCompany[1]->unit_price_m2 : 0;
            $unitPrice3 = isset($tangibleAsset->constructionCompany[2]) ? $tangibleAsset->constructionCompany[2]->unit_price_m2 : 0;
            $table->addRow();

            $table->addCell(2500, $this->cellRowSpan)->addText(CommonService::mbUcfirst($name), ['bold' => true], $this->cellHCentered);
            $table->addCell(500, $this->cellRowSpan)->addText($this->m2, ['bold' => true], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice1, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice2, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
            $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice3, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
            $dgqd = isset($tangibleAsset->total_desicion_average) ? $tangibleAsset->total_desicion_average : 0;
            $unitPrice = round(($unitPrice1 + $unitPrice2 + $unitPrice3) / 3, -4, PHP_ROUND_HALF_DOWN);
            if ($unitPrice) {
                $table->addCell(1500, $this->cellRowSpan)->addText(number_format($unitPrice, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
            } else {
                $table->addCell(1500, $this->cellRowSpan)->addText("Không biết", ['bold' => false], $this->cellHCentered);
            }
            $table->addCell(1500, $this->cellRowSpan)->addText(number_format($dgqd, 0, ',', '.'), ['bold' => false], $this->cellHCentered);
            $this->total[$tangibleAsset->id]['name'] = $name;
            $this->total[$tangibleAsset->id]['start_using_year'] = $startUsingYear;
        }
    }
    protected function printBuildingPriceChoosed($section, $dgxdSlug)
    {
        $description = '';
        if ($dgxdSlug == 'dg-uoc-tinh') {
            $description = $this->companyName . ' đề xuất đơn giá xây dựng mới cho TSTĐ theo đơn giá trung bình của các công ty xây dựng đang cung cấp trên thị trường.';
        } else {
            $description = $this->companyName . ' đề xuất đơn giá xây dựng mới cho TSTĐ theo đơn giá quyết định của UBND.';
        }
        $textRun = $section->addTextRun();
        $textRun->addText('Kết luận: ', $this->styleBold, null);
        $textRun->addText($description, null, null);
    }

    protected function printRemainQualityDescription($section)
    {
        $section->addText('❖ Chất lượng còn lại nhà cửa, vật kiến trúc: ', ['bold' => true, 'size' => 13], ['align' => 'left']);
        $textRun = $section->addTextRun();
        $textRun->addText('- Căn cứ theo biên bản kiểm kê và kết quả khảo sát hiện trạng. ' . $this->acronym . ' đánh giá chất lượng còn lại của công trình xây dựng như sau:');
    }

    protected function printRemainQualityFunc1($section, $tangibleAssets)
    {
        $section->addText('✔ Phương pháp 1: Phương pháp tuổi đời (PP1):', ['bold' => true, 'size' => 13, 'italic' => true], ['align' => 'left']);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(3000, $this->cellRowSpan)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('Năm sử dụng', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2250, $this->cellRowSpan)->addText('Thời gian đã sử dụng', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(2250, $this->cellRowSpan)->addText('Niên hạn theo qui định', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('CLCL (%)', ['bold' => true], $this->cellHCenteredKeepNext);
        $level = '';
        $stt = 1;
        $countTangible = count($tangibleAssets);
        foreach ($tangibleAssets as $tangibleAsset) {
            $startUsingYear = $tangibleAsset->start_using_year ?? '';
            $usingYear = '';
            if ($startUsingYear != '') {
                $usingYear = Carbon::now()->year - (int)$startUsingYear;
            }
            $usefulYear = isset($tangibleAsset->duration) ? CommonService::mbUcfirst($tangibleAsset->duration) : '';
            $clcl = $tangibleAsset->remaining_quality ?? 0;
            $table->addRow();
            $table->addCell(3000, $this->cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(1500, $this->cellRowSpan)->addText($startUsingYear, ['bold' => false], ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(2250, $this->cellRowSpan)->addText($usingYear, ['bold' => false], ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(2250, $this->cellRowSpan)->addText($usefulYear, ['bold' => false], ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(1500, $this->cellRowSpan)->addText($clcl, ['bold' => false], ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $this->total[$tangibleAsset->id]['clcl1'] = $clcl;
            $stt++;
        }
    }
    protected function printRemainQualityFunc2($section, $tangibleAssets)
    {
        $section->addText('✔ Phương pháp 2: Phương pháp chuyên gia (PP2): ', ['bold' => true, 'size' => 13, 'italic' => true], ['align' => 'left', 'keepNext' => true]);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(1500, $this->cellRowSpan)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(7500, ['gridSpan' => 10, 'valign' => 'center'])->addText('Phần kết cấu chính', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('CLCL (%)', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addRow();
        $table->addCell(1500, $this->cellRowContinue)->addText(null, null, ['keepNext' => true]);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Móng, cột', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Tường', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Nền, sàn', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Kết cấu mái', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, ['gridSpan' => 2, 'valign' => 'center'])->addText('Mái', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowContinue)->addText(null, null, ['keepNext' => true]);
        $table->addRow(400, $this->cantSplit);
        $table->addCell(1500, $this->cellRowContinue)->addText(null, null, ['keepNext' => true]);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('p', null,  $this->cellHCenteredKeepNext);
        $table->addCell(750, $this->cellVCentered)->addText('h', null,  $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellVCentered)->addText('H= Σ ph / Σ p', null,  $this->cellHCenteredKeepNext);

        $stt = 1;
        $countTangible = count($tangibleAssets);
        foreach ($tangibleAssets as $tangibleAsset) {
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
            $table->addRow(400, $this->cantSplit);
            $table->addCell(1500, $this->cellRowSpan)->addText(CommonService::mbUcfirst($tangibleAsset->tangible_name), null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p1 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h1 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p2 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h2 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p3 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h3 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($d4 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h4 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($p5 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(750, $this->cellVCentered)->addText($h5 . '%', null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $clcl2 = ($p1 + $p2 + $p3 + $d4 + $p5) != 0 ? round(($p1 * $h1 + $p2 * $h2 + $p3 * $h3 + $d4 * $h4 + $p5 * $h5) / ($p1 + $p2 + $p3 + $d4 + $p5), 0) : 0;
            $table->addCell(1500, $this->cellRowSpan)->addText($clcl2, null, ($stt = $countTangible) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $this->total[$tangibleAsset->id]['clcl2'] = $clcl2;
            $stt++;
        }
    }
    protected function printRemainQualityFuncAvg($section, $tangibleAssets, $appraisalCLCL)
    {
        $section->addText('✔ Chất lượng còn lại lựa chọn:', ['bold' => true, 'size' => 13], ['align' => 'left', 'keepNext' => true]);
        $table = $section->addTable($this->styleTable);
        $table->addRow(400, $this->rowHeader);
        $table->addCell(3000, $this->cellRowSpan)->addText('Tên tài sản', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('Năm sử dụng', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('Theo PP1', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('Theo PP2', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('Theo bình quân', ['bold' => true], $this->cellHCenteredKeepNext);
        $table->addCell(1500, $this->cellRowSpan)->addText('CLCL lựa chọn', ['bold' => true], $this->cellHCenteredKeepNext);
        $count = count($this->total);
        foreach ($tangibleAssets as $tangibleAsset) {
            $clclChoosed = CommonService::getClclChoosed($tangibleAsset, $appraisalCLCL);
            $clcl1 = $this->total[$tangibleAsset->id]['clcl1'];
            $clcl2 = $this->total[$tangibleAsset->id]['clcl2'];
            $cltb = CommonService::roundPrice(($clcl1 + $clcl2) / 2, 0);
            $table->addRow();
            $table->addCell(3000, $this->cellRowSpan)->addText(CommonService::mbUcfirst($this->total[$tangibleAsset->id]['name']), null, ($stt = $count) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(1500, $this->cellRowSpan)->addText($this->total[$tangibleAsset->id]['start_using_year'], null, ($stt = $count) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(1500, $this->cellRowSpan)->addText($clcl1, null, ($stt = $count) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(1500, $this->cellRowSpan)->addText($clcl2, null, ($stt = $count) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(1500, $this->cellRowSpan)->addText($cltb, null, ($stt = $count) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
            $table->addCell(1500, $this->cellRowSpan)->addText($clclChoosed, null, ($stt = $count) ? $this->cellHCentered : $this->cellHCenteredKeepNext);
        }
    }
}
