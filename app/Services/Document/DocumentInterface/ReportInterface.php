<?php
namespace App\Services\Document\DocumentInterface;

use PhpOffice\PhpWord\Element\Footer;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;

interface ReportInterface
{
    public function printTitle(Section $section, $data);
    public function printContent(Section $section, $data);
    public function printHeader(Section $section);
    public function printFooter(Section $section, $data, $indentLeft = 0, $indentRight = 0);
    public function getFooterString($data);
    public function setFormat(PhpWord $phpWord);
    public function saveReport(PhpWord $phpWord, string $ext, bool $download = true);
    public function getReportName();
    public function getReportPath();
    public function generateDocx($company, $data, $ext, $documentConfig);
}
