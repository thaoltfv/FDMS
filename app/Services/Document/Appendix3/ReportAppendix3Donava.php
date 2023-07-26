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
        $table->addCell(2000)->addImage($this->logoUrl, array(
            'height'        => 33,
            'wrappingStyle' => 'behind',
            'wrapDistanceRight' => 300,
        )); 
        $table->addCell()->addText('PHỤ LỤC ẢNH TÀI SẢN THẨM ĐỊNH GIÁ', ['bold' => true, 'size' => 14], $noSpace);
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
        $table->addRow();
        $table->addCell(1800);
        $table->addCell()->addText('(Kèm theo báo cáo Thẩm định giá số ' .  $certificateNum . $this->documentNumberSuffix . ', ngày ' . $certificateDate . ')', ['italic' => true, 'size' => 12], $noSpace);
    }
}
