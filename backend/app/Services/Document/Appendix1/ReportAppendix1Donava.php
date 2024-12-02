<?php

namespace App\Services\Document\Appendix1;

use App\Services\Document\Appendix1\ReportAppendix1;

class ReportAppendix1Donava extends ReportAppendix1
{

    protected function printAssetInfo($section, $name, $address)
    {
        $textRun = $section->addTextRun();
        $textRun->addText('     - Tên tài sản: ', ['size' => 13, 'bold' => true]);
        $textRun->addText($name ? htmlspecialchars($name) : '', ['size' => 13, 'bold' => false]);
    }

}
