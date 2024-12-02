<?php

namespace App\Services\Document\Appendix2;

class ReportAppendix2Donava extends ReportAppendix2
{

    protected function printAssetInfo($section, $name, $address)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tên tài sản: ', ['size' => 13, 'bold' => true]);
        $textRun->addText($name ? htmlspecialchars($name) : '', ['size' => 13, 'bold' => false]);
    }

}
