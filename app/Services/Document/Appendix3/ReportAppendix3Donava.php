<?php

namespace App\Services\Document\Appendix3;


class ReportAppendix3Donava extends ReportAppendix3
{
    protected function printAssetInfo($section, $name, $address)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tên tài sản: ', ['bold' => true]);
        $textRun->addText($name ? $name : '', ['bold' => false]);
    }
}
