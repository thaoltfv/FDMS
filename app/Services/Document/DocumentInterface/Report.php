<?php
namespace App\Services\Document\DocumentInterface;

use App\Http\ResponseTrait;
use App\Services\CommonService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\JcTable;

class Report implements ReportInterface
{
    #region Style
    use ResponseTrait;
    //Format region
    protected $marginTopNational = 1080;
    protected $marginBottomNational = 600;
    protected $marginRightNational = 800;
    protected $marginLeftNational = 900;

    protected $marginTopContent = 600;
    protected $marginBottomContent = 600;
    protected $marginRightContent = 1000;
    protected $marginLeftContent = 1000;

    protected $tableBasicStyle = [
        'borderSize' => 'none',
        'cellMargin' => 0,
        'width' => 100 * 50,
        'unit' => 'pct',
    ];
    protected $styleTable = [
        'borderSize' => 1,
        'align' => JcTable::START,
        'width' => 100 * 50,
        'unit' => 'pct',
    ];
    protected $styleAlignStart = [
        'align' => JcTable::START
    ];
    protected $styleAlignRight = [
        'align' => 'right'
    ];
    protected $styleAlignLeft = [
        'align' => 'left'
    ];
    protected $styleAlignCenter = [
        'align' => JcTable::CENTER
    ];
    protected $styleImageLogo = [
        'height' => 33,
        'space' => [
            'line' => 1000,
        ],
    ];

    protected $styleImageHeader = [
        'width' => 488,
        'align' => 'left',
        'space' => [
            'line' => 1000,
            'rule' => 'single',
        ],
    ];
    protected $styleImageHeader1 = [
        'align' => 'center',
        'height' => 33,
        'space' => [
            'line' => 1000,
        ],
    ];
    protected $styleImageFooter = [
        'width' => 488,
        'align' => 'left',
        'space' => [
            'line' => 1000,
            'rule' => 'single',
        ],
    ];
    protected $styleMapImage = [
        'width' => 488,
        'align' => 'left',
        'space' => [
            'line' => 1000,
            'rule' => 'single',
        ],
    ];
    protected $styleNationalSection = [];
    protected $styleSection = [];
    protected $styleBold = ['bold' => true];
    protected $cellHCentered = array('align' => 'center');
    protected $cellVCentered = array('valign' => 'center');
    protected $cellVCenteredKeepNext = array('valign' => 'center', 'keepNext' => true);
    protected $cellHCenteredKeepNext = array('align' => 'center', 'keepNext' => true);
    protected $rowHeader = array('tblHeader' => true, 'cantSplit' => true);
    protected $keepNext = ['keepNext' => true];
    protected $cantSplit = ['cantSplit' => true];
    protected $indentFistLine = ['indentation' => ['firstLine' => 360]];
    protected $styleTableImage = [
        'align' => JcTable::CENTER,
        'width' => 100 * 50,
        'unit' => 'pct',
    ];
    //Document property
    protected $acronym = '';
    protected $companyName = '';
    protected $companyAcronym = '';
    protected $companyAddress = '';
    protected $companyPhone = '';
    protected $companyFax = '';
    protected $companyDownLine = '';
    protected $createdName ='';
    protected $assetName = '';
    protected $logoUrl = '';
    protected $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';
    protected $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
    protected $cellRowContinue = array('vMerge' => 'continue');
    protected $documentConfig = [];
    protected $certificateNumberSuffix = '';
    protected $certificateNumberPrefix = '';
    protected $documentWatermask= '';
    protected $documentNumberSuffix = '';
    protected $documentNumberPrefix = '';
    protected $contractCodeSuffix = '';
    protected $contractCodePrefix = '';
    protected $certificateCode = '';
    protected $reportCode = '';
    protected $contractCode = '';
    protected $certificateLongDateText = 'ngày        tháng        năm        ';
    protected $documentLongDateText = 'ngày        tháng        năm        ';
    protected $certificateShortDateText = '                  ';
    protected $documentShortDateText = '                  ';
    protected $envDocument = '';
    protected $fileName = '';
    protected $isPrintNational = true;
    #endregion

    public function generateDocx($company, $data, $ext, $documentConfig)
    {
        $phpWord = new PhpWord();
        $this->getCompanyInfo($company);
        $this->processData($data, $documentConfig);
        $this->setFormat($phpWord);
        $this->nationalName($phpWord, $data);
        $styleSection = $this->styleSection;
        if (!$this->isPrintNational) {
            $styleSection['headerHeight'] = 0;
        }
        $section = $phpWord->addSection($styleSection);
        $this->printTitle($section, $data);
        $this->printContent($section, $data);
        $this->signature($section, $data);
        $this->printHeader($section);
        $this->printFooter($section, $data);
        $this->getFileName ($data);
        return $this->saveReport($phpWord, $ext);
    }
    protected function processData ($data, $documentConfig)
    {
        $this->envDocument = config('services.document_service.document_module');
        $this->createdName = !empty($data->createdBy) ? CommonService::withoutAccents($data->createdBy->name) : '';
        $this->logoUrl = storage_path('app/public/' . env('STORAGE_IMAGES','images').'/'.'company_logo.png');
        if (!empty($documentConfig)) {
            $this->documentConfig = $documentConfig;
            $this->certificateNumberSuffix = $documentConfig->where('slug', 'certificatte_number_suffix')->first()->value ?: '';
            $this->certificateNumberPrefix = $documentConfig->where('slug', 'certificatte_number_prefix')->first()->value ?: '';
            $this->documentNumberSuffix = $documentConfig->where('slug', 'document_number_suffix')->first()->value ?: '';
            $this->documentNumberPrefix = $documentConfig->where('slug', 'document_number_prefix')->first()->value ?: '';
            // $this->contractCodeSuffix = $documentConfig->where('slug', 'contract_code_suffix')->first()->value ?: '';
            $this->contractCodeSuffix =  '';
            $this->contractCodePrefix = $documentConfig->where('slug', 'contract_code_prefix')->first()->value ?: '';
            $this->documentWatermask = $documentConfig->where('slug', 'print_watermask')->first()->value ?: '';

        }
        // Report code
        if(isset($data->certificate_num) && !empty(trim($data->certificate_num))) {
            $this->reportCode = $this->documentNumberPrefix . $data->certificate_num . $this->documentNumberSuffix;
        } else {
            $this->reportCode = $this->documentNumberPrefix . '            ' . $this->documentNumberSuffix;
        }
        // Certificate code
        if(isset($data->certificate_num) && !empty(trim($data->certificate_num))) {
            $this->certificateCode = $this->certificateNumberPrefix . $data->certificate_num . $this->certificateNumberSuffix;
        } else {
            $this->certificateCode = $this->certificateNumberPrefix . '            ' . $this->certificateNumberSuffix;
        }
        //Contract code
        if(isset($data->document_num) && !empty(trim($data->document_num))) {
            $this->contractCode = $this->contractCodePrefix . $data->document_num . $this->contractCodeSuffix;
        } else {
            $this->contractCode = $this->contractCodePrefix . '            ' . $this->contractCodeSuffix;
        }
        if(!empty($data->certificate_date)) {
            $certificateDate = date_create($data->certificate_date);
            $this->certificateShortDateText = $certificateDate->format("d/m/Y");
            $this->certificateLongDateText = "ngày " . $certificateDate->format('d') . " tháng " . $certificateDate->format('m') . " năm " . $certificateDate->format('Y');
        }
        if(!empty($data->document_date)) {
            $documentDate = date_create($data->document_date);
            $this->documentShortDateText = $documentDate->format("d/m/Y");
            $this->documentLongDateText =  "ngày " . $documentDate->format('d') . " tháng " . $documentDate->format('m') . " năm " . $documentDate->format('Y');
        }
    }

    protected function downloadFile($path, $fileName, $ext)
    {
        $result = [];
        $result['url'] = Storage::disk('public')->url($path .  $fileName . $ext);
        $result['file_name'] = $fileName;
        return $result;
    }
    protected function getFileName ($data)
    {
        $data = (object)$data;
        $reportID = '';
        if (is_countable($data->realEstate)) $reportID = 'HSTD_' . $data->id;
        else $reportID = 'TSTD_' . $data->id;
        $reportName = $this->getReportName() . '_' . CommonService::getUserReport() . '_' . $reportID . '_' . mb_strtoupper($this->companyAcronym);
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $this->fileName = $reportName . '_' . $downloadTime . '_' . $downloadDate;
    }
    private function getCompanyInfo($company)
    {
        $this->companyName = $company->name ?: '';
        $this->companyAcronym = $company->acronym ?: '';
        $this->companyAddress = $company->address ?: '';
        $this->companyFax = $company->fax_number ?: '';
        $this->companyPhone = $company->phone_number ?: '';
        $this->companyDownLine = $company->down_line ?: 0;
        $this->acronym = !empty($company->acronym) ? mb_strtoupper($company->acronym) : $company->name;
    }
    public function printHeader(Section $section)
    {
        $this->waterMark($section);
    }
    public function waterMark (Section $section)
    {
        if ($this->documentWatermask === 'yes') {
            $header = $section->addHeader();
            $header->addWatermark($this->logoUrl,[
                'width' => 200,
                'marginTop' => 200,
                'marginLeft' => 120,
                'posHorizontal' => 'absolute',
                'posVertical' => 'absolute',
            ]);
        }
    }
    protected function nationalName(PhpWord $phpWord, $data)
    {
        if ($this->isPrintNational) {
            $section = $phpWord->addSection($this->styleNationalSection);
            $table1 = $section->addTable($this->tableBasicStyle);
            $table1->addRow(1000);
            $cell11 = $table1->addCell(Converter::cmToTwip(1), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $imgName = env('STORAGE_IMAGES','images').'/'.'company_logo.png';
            $cell11->addImage(storage_path('app/public/'.$imgName), $this->styleImageLogo);
            $cell12 = $table1->addCell(Converter::inchToTwip(3), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell12->addText(CommonService::downLineCompanyName($this->companyName, $this->companyDownLine), ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            // $table1->addCell(Converter::inchToTwip(.1), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell13 = $table1->addCell(Converter::inchToTwip(4), ['valign' => 'top', 'borderBottomSize' => 20, 'underline' => 'dash']);
            $cell13->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM ", ['bold' => true, 'size' => '12'], $this->styleAlignCenter);
            $cell13->addText("Độc lập – Tự do – Hạnh phúc", ['bold' => true], $this->styleAlignCenter);
            $indentLeft = $this->marginLeftContent - $this->marginLeftNational;
            $indentRight = $this->marginRightContent - $this->marginRightNational;
            $this->printFooter($section, $data, $indentLeft, $indentRight);
        }
    }
    public function printContent(Section $section, $data)
    {

    }
    public function printFooter(Section $section, $data, $indentLeft = 0, $indentRight = 0)
    {
        $footer = $section->addFooter();
        $strFooter = $this->getFooterString($data);
        $table = $footer->addTable();
        $table->addRow();
        $table->addCell(4500)->addText($strFooter, array('size' => 8), array('align' => 'left', 'indentation' => array('left' => $indentLeft)));
        $table->addCell(6000)->addPreserveText('Trang {PAGE}/{NUMPAGES}', array('size' => 8), array('align' => 'right',  'indentation' => array('right' => $indentRight)));
    }
    public function setFormat(PhpWord $phpWord)
    {
        $this->styleNationalSection = [
            'footerHeight' => 300,
            'marginTop' => $this->marginTopNational,
            'marginBottom' => $this->marginBottomNational,
            'marginRight' => $this->marginRightNational,
            'marginLeft' => $this->marginLeftNational,
            'breakType' => 'continuous'
        ];
        $this->styleSection = [
            'footerHeight' => 300,
            'marginTop' => $this->marginTopContent,
            'marginBottom' => $this->marginBottomContent,
            'marginRight' => $this->marginRightContent,
            'marginLeft' => $this->marginLeftContent,
            'breakType' => 'continuous'
        ];
        $phpWord->addNumberingStyle(
            'headingNumbering',
            array(
                'type' => 'multilevel',
                'levels' => array(
                    array('pStyle' => 'Heading1', 'format' => 'upperRoman', 'text' => '%1.', 'left' => 360, 'hanging' => 360, 'tabPos' => 360),
                    array('pStyle' => 'Heading2', 'format' => 'decimal', 'text' => '%2.', 'left' => 360, 'hanging' => 360, 'tabPos' => 360),
                    array('pStyle' => 'Heading3', 'format' => 'decimal', 'text' => '%2.%3.', 'left' => 600, 'hanging' => 360, 'tabPos' => 600),
                )
            )
        );
        $phpWord->addNumberingStyle(
            'bullets',
            array(
                'type' => 'multilevel',
                'levels' => array(
                    array('format' => 'upperLetter', 'text' => '-', 'left' => 360, 'hanging' => 0, 'suffix' => 'space'),
                    array('format' => 'upperLetter', 'text' => '+', 'left' => 360, 'hanging' => 0, 'suffix' => 'space'),
                )
            )
        );

        $phpWord->addTitleStyle(
            1,
            array('size' => '13', 'bold' => true, 'allCaps' => true, 'spaceBefore' => 100),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 0, 'spaceBefore' => 300, 'spaceAfter' => 100)
        );
        $phpWord->addTitleStyle(
            2,
            array('size' => '13', 'bold' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 1, 'spaceBefore' => 200, 'spaceAfter' => 100)
        );
        $phpWord->addTitleStyle(
            3,
            array('size' => '13', 'bold' => true),
            array('keepNext' => true, 'numStyle' => 'headingNumbering', 'numLevel' => 2, 'spaceBefore' => 150, 'spaceAfter' => 100)
        );

        $phpWord->addParagraphStyle(
            'leftTab',
            array('tabs' => array(new \PhpOffice\PhpWord\Style\Tab('left', 5000)))
        );
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);
        $phpWord->setDefaultParagraphStyle([
            'spaceBefore' => 80,
            'spaceAfter' => 80,
            'indentation' => array('left' => 70, 'right' => 70),
            'space' => [
                'line' => 320,
                'rule' => \PhpOffice\PhpWord\SimpleType\LineSpacingRule::AT_LEAST
            ],
            'align' => 'both'
        ]);
        $phpWord->addTableStyle('Colspan Rowspan', $this->styleTable);
        $phpWord->addTableStyle('Colspan Rowspan Image', $this->styleTableImage);

    }
    public function printTitle(Section $section, $data)
    {

    }
    public function saveReport(PhpWord $phpWord, string $ext, bool $download = true)
    {
        $reportPath = $this->getReportPath();
        $fileName = $this->fileName;
        if(!File::exists(storage_path('app/public/'. $reportPath))){
            File::makeDirectory(storage_path('app/public/'. $reportPath), 0755, true);
        }
        try {
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/'. $reportPath. '/'. $fileName . $ext));
            if ($download)
                return $this->downloadFile($reportPath, $fileName, $ext);
            else
                return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getReportName()
    {
        return '';
    }
    public function getReportPath()
    {
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'certification_briefs/' . $now->format('Y') . '/' . $now->format('m') . '/';
        return $path;
    }
    public function getFooterString($data)
    {
        $strFooter = 'report';
        return $strFooter;
    }
    protected function signature(Section $section, $data)
    {

    }
}
