<?php

namespace App\Services\Document\Appendix3;
use PhpOffice\PhpWord\Element\Section;


class ReportAppendix3Donava extends ReportAppendix3
{
    protected function printAssetInfo($section, $name, $address)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tên tài sản: ', ['bold' => true]);
        $textRun->addText($name ? $name : '', ['bold' => false]);
    }
    public function printTitle(Section $section, $data)
    {
        $this->setProperties($data);
        // $textrun = $section->addTextRun();
        // $noSpace = array('spaceAfter' => 0);
        $table = $section->addTable();
        $table->addRow();
        $c0 = $table->addCell(2200);
        $c0->addTextBreak(1);
        $c0->addImage($this->logoUrl, array(
            'height'        => 33,
            'wrappingStyle' => 'behind',
            'alignment' => 'left',
        )); 
        $c1 = $table->addCell();
        $c1->addText('   PHỤ LỤC ẢNH TÀI SẢN THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], array('spaceAfter' => 0, 'spaceBefore' => 400));
        // $section->addImage($this->logoUrl, array(
        //     'height'        => 33,
        //     'wrappingStyle' => 'inline',
        //     'wrapDistanceRight' => 30,
        //     'marginTop' => 1,
        // ));
        // $section->addText('PHỤ LỤC ẢNH TÀI SẢN THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], ['align' => 'center']);
        $certificateNum = !empty($data->certificate_num) ? $data->certificate_num : '      ';
        $certificateDate = !empty($data->certificate_date) ? date("d/m/Y", strtotime($data->certificate_date)) : '      ';
        // $section->addText('(Kèm theo báo cáo Thẩm định giá số ' .  $certificateNum . $this->documentNumberSuffix . ', ngày ' . $certificateDate . ')', ['italic' => true, 'size' => 12], ['align' => 'center']);
        $c1->addText('(Kèm theo báo cáo Thẩm định giá số ' .  $certificateNum . $this->documentNumberSuffix . ', ngày ' . $certificateDate . ')', ['italic' => true, 'size' => 12], array('spaceAfter' => 0));
    }

    protected function printMapImage($section, $pic)
    {
        if (!empty($pic)) {
            $section->addImage($pic->link, array(
                'height' => 244,
                'width' => 488,
                'align' => 'left',
                'space' => [
                'line' => 1000,
                'rule' => 'single',
            ],));
            $section->addText('Sơ đồ vị trí.', null, ['align' => 'center','lineHeight' => 1]);
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
            $cell->addText('Đường tiếp giáp TSTĐG.', null, ['align' => 'center','lineHeight' => 1]);
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
            $cell->addText('Hiện trạng tổng thể TSTĐG.', null, ['align' => 'center','lineHeight' => 1]);
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
            $cell->addText('Hiện trạng chi tiết tài sản thẩm định.', null, ['align' => 'center','lineHeight' => 1]);
        }
    }

    protected function getCustomerRequest($section, $certificate)
    {
        // $section->addTextBreak(1);
        $textRun = $section->addTextRun();
        $textRun->addText('Khách hàng yêu cầu TĐG: ', ['bold' => true, 'underLine' => true]);
        $textRun->addText($certificate->petitioner_name ?? '', ['bold' => true]);
    }
}
