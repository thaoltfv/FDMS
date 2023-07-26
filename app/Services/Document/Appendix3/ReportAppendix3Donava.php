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
        $noSpace = array('spaceAfter' => 0);
        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2200)->addImage($this->logoUrl, array(
            'height'        => 33,
            'wrappingStyle' => 'behind',
            'wrapDistanceRight' => 300,
        )); 
        $c1 = $table->addCell();
        $c1->addText('PHỤ LỤC ẢNH TÀI SẢN THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14, 'align' => 'center'], $noSpace);
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
        $c1->addText('(Kèm theo báo cáo Thẩm định giá số ' .  $certificateNum . $this->documentNumberSuffix . ', ngày ' . $certificateDate . ')', ['italic' => true, 'size' => 12, 'align' => 'center'], $noSpace);
    }
}
