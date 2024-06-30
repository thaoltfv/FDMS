<?php

namespace App\Services\Document\Appendix3;

use App\Services\Document\DocumentInterface\Report;
use Illuminate\Support\Carbon;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\JcTable;

class ReportAppendix3 extends Report
{
    protected $isPrintNational = false;
    protected $isOnlyAsset = false;
    protected $realEstates = [];
    protected $isTangibleAsset = false;
    protected $isApartment = false;
    protected $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
    protected $styleTableImageLeft = array(
        'width' => 233,
        'height' => 156,
        'space' => [
            'line' => 1000,
        ],
        'keepNext' => true,
        'align' => 'left',
    );
    protected $styleTableImageRight = array(
        'width' => 233,
        'height' => 156,
        'space' => [
            'line' => 1000,
        ],
        'keepNext' => true,
        'align' => 'right',
    );

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

        // return mb_strtoupper($this->envDocument)  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID;
        return mb_strtoupper($this->acronym)  . '/' . $createdName . '/' . $yearCVD . '/' . $reportID;
    }
    public function getReportName()
    {
        return '5_PL3';
    }
    protected function signature(Section $section, $certificate)
    {
        ////không có chữ ký
    }
    public function printTitle(Section $section, $data)
    {
        $this->setProperties($data);
        $section->addImage($this->logoUrl, $this->styleImageLogo);
        $section->addText('PHỤ LỤC ẢNH TÀI SẢN THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);
        $certificateNum = !empty($data->certificate_num) ? $data->certificate_num : '      ';
        $certificateDate = !empty($data->certificate_date) ? date("d/m/Y", strtotime($data->certificate_date)) : '      ';
        // $section->addText('(Kèm theo báo cáo Thẩm định giá số ' .  $certificateNum . $this->documentNumberSuffix . ', ngày ' . $certificateDate . ')', ['italic' => true, 'size' => 12], ['align' => 'center']);
    }
    protected function setProperties($data)
    {
        $this->isOnlyAsset = (!is_countable($data->realEstate) || count($data->realEstate) == 1);
        $this->realEstates = $data->realEstate;
        $this->isTangibleAsset = in_array('DCN', $data->document_type);
        $this->isApartment = in_array('CC', $data->document_type);
    }
    //content
    public function printContent(Section $section, $certificate)
    {
        $this->getCustomerRequest($section, $certificate);
        $this->getAsset($section);
    }
    protected function getCustomerRequest($section, $certificate)
    {
        $section->addTextBreak(1);
        $textRun = $section->addTextRun();
        $textRun->addText('Khách hàng yêu cầu TĐG: ', ['bold' => true, 'underLine' => true]);
        $textRun->addText($certificate->petitioner_name ?? '', ['bold' => true]);
    }
    protected function getRealEstate($realEstate)
    {
        $assetName = $realEstate->appraise_asset;
        if ($this->isApartment) {
            $apartment = $realEstate->apartment;
            $address = $apartment->full_address ? $apartment->full_address : '';
            $pics = $apartment->pic;
        } else {
            $appraise = $realEstate->appraises;
            $address = $appraise->full_address ? $appraise->full_address : '';
            $pics = $appraise->pic;
        }

        $result = [
            'asset_name' => $assetName,
            'address' => $address,
            'pic' => $pics
        ];
        return $result;
    }
    protected function getAsset($section)
    {
        if (is_countable($this->realEstates)) {
            foreach ($this->realEstates as $key => $realEstate) {
                $data = $this->getRealEstate($realEstate);
                $this->printData($section, $data['asset_name'], $data['address'], $data['pic'], $key);
            }
        } else {
            $data = $this->getRealEstate($this->realEstates);
            $this->printData($section, $data['asset_name'], $data['address'], $data['pic'], 0);
        }
    }
    protected function printData($section, $assetName, $address, $pics, $key)
    {
        if (($key + 1) > 1) {
            $section->addPageBreak();
        }
        $result = null;
        foreach ($pics as $value) {
            if (isset($value->picType)) {
                $result[$value->picType->description][] = $value;
            }
        }
        if ($this->isOnlyAsset) {
            $section->addTitle('Tài sản thẩm định:', 2);
        } else {
            $section->addTitle('Tài sản thẩm định ' . ($key + 1) . ':', 2);
        }
        $this->printAssetInfo($section, $assetName, $address);

        $picMap = $result['HÌNH BẢN ĐỒ'][0] ?? null;

        $this->printMapImage($section, $picMap);

        $picRoad = $result['ĐƯỜNG TIẾP GIÁP TÀI SẢN THẨM ĐỊNH GIÁ'] ?? null;
        $this->printRoadImage($section, $picRoad);
        $picOverall = $result['TỔNG THỂ TÀI SẢN THẨM ĐỊNH GIÁ'] ?? null;
        $this->printOverallImage($section, $picOverall);
        $picOverall = $result['HIỆN TRẠNG TÀI SẢN THẨM ĐỊNH GIÁ'] ?? null;
        $this->printCurrentStatusImage($section, $picOverall);
    }

    protected function printAssetInfo($section, $name, $address)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tên tài sản: ', ['bold' => true]);
        $textRun->addText($name ?  htmlspecialchars($name) : '');
        if (!empty($address)) {
            $textRun = $section->addTextRun();
            $textRun->addText('     - Địa chỉ: ', ['bold' => true]);
            $textRun->addText($address ?  htmlspecialchars($address) : '');
        }
    }
    protected function printMapImage($section, $pic)
    {
        if (!empty($pic)) {
            $section->addImage($pic->link, $this->styleMapImage);
            $section->addText('Sơ đồ vị trí.', null, ['align' => 'center']);
        }
    }
    protected function printCurrentStatusImage($section, $pic)
    {
        if (!empty($pic)) {
            $table = $section->addTable();
            for ($i = 0; $i < count($pic); $i += 2) {
                $table->addRow(800);
                $cell = $table->addCell(5000);
                if (isset($pic[$i]->link)) {
                    $cell->addImage($pic[$i]->link, $this->styleTableImageLeft);
                }
                $cell = $table->addCell(5000);
                if (isset($pic[$i + 1]->link)) {
                    $cell->addImage($pic[$i + 1]->link, $this->styleTableImageRight);
                }
            }
            $table->addRow();
            $cell = $table->addCell(10000, $this->cellColSpan);
            $cell->addText('Hiện trạng chi tiết tài sản thẩm định.', null, ['align' => 'center']);
        }
    }
    protected function printRoadImage($section, $pic)
    {
        if (!empty($pic)) {
            $table = $section->addTable();
            for ($i = 0; $i < count($pic); $i += 2) {
                $table->addRow(800);
                $cell = $table->addCell(5000);
                if (isset($pic[$i]->link)) {
                    $cell->addImage($pic[$i]->link, $this->styleTableImageLeft);
                }
                $cell = $table->addCell(5000);
                if (isset($pic[$i + 1]->link)) {
                    $cell->addImage($pic[$i + 1]->link, $this->styleTableImageRight);
                }
            }
            $table->addRow();
            $cell = $table->addCell(10000, $this->cellColSpan);
            $cell->addText('Đường tiếp giáp TSTĐG.', null, ['align' => 'center']);
        }
    }
    protected function printOverallImage($section, $pic)
    {
        if (!empty($pic)) {
            $table = $section->addTable();
            for ($i = 0; $i < count($pic); $i += 2) {
                $table->addRow(800);
                $cell = $table->addCell(5000);
                if (isset($pic[$i]->link)) {
                    $cell->addImage($pic[$i]->link, $this->styleTableImageLeft);
                }
                $cell = $table->addCell(5000);
                if (isset($pic[$i + 1]->link)) {
                    $cell->addImage($pic[$i + 1]->link, $this->styleTableImageRight);
                }
            }
            $table->addRow();
            $cell = $table->addCell(10000, $this->cellColSpan);
            $cell->addText('Hiện trạng tổng thể TSTĐG.', null, ['align' => 'center']);
        }
    }
}
