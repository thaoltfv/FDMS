<?php

namespace App\Services\Excel;

use App\Enum\ValueDefault;
use App\Http\ResponseTrait;
use App\Services\CommonService;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as StyleNumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class ExportAccountant
{
    use ResponseTrait;
    public function exportPreAccountant($data)
    {
        // $data = CommonService::exportCertificateAssets();
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/' . 'pre_certification/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if (!File::exists(storage_path('app/public/' . $path))) {
            File::makeDirectory(storage_path('app/public/' . $path), 0755, true);
        }
        $downloadDate = $now->format('dmY');
        $downloadTime = $now->format('Hi');
        $fileName = 'TT_HSSB' . '_' . $downloadTime . '_' . $downloadDate . '.xlsx';
        $border = (new BorderBuilder())
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->build();

        $header_style = (new StyleBuilder())
            ->setFontName('Times New Roman')
            ->setFontBold()
            ->setBorder($border)
            ->build();

        $rows_style = (new StyleBuilder())
            ->setFontName('Times New Roman')
            ->setFontSize(11)
            ->setBorder($border)
            ->build();

        // dd( new JsonResponse($data) );
        (new FastExcel($data))
            ->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->export(
                storage_path('app/public/' . $path . '/' . $fileName),
                function ($data) {


                    return [
                        'Mã HS' => $data->pre_certificate_id,
                        'Giá trị thanh toán' => $data->amount,
                        'Nội dung' => $data->for_payment_of,
                        'Ngày thanh toán' => \Carbon\Carbon::parse($data->pay_date)->format('d-m-Y'),
                        'Chuyển chính thức' => $data->certificate_id ? 'HSTD_' . $data->certificate_id : '',
                    ];
                }
            );


        $this->formatColumn($path, $fileName);

        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/' . $fileName);
        $data['file_name'] = $fileName;
        return $data;
    }
    public function exportCertificateAccountant($data)
    {
        // $data = CommonService::exportCertificateAssets();
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/' . 'pre_certification/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if (!File::exists(storage_path('app/public/' . $path))) {
            File::makeDirectory(storage_path('app/public/' . $path), 0755, true);
        }
        $downloadDate = $now->format('dmY');
        $downloadTime = $now->format('Hi');
        $fileName = 'TT_HSTĐ' . '_' . $downloadTime . '_' . $downloadDate . '.xlsx';
        $border = (new BorderBuilder())
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->build();

        $header_style = (new StyleBuilder())
            ->setFontName('Times New Roman')
            ->setFontBold()
            ->setBorder($border)
            ->build();

        $rows_style = (new StyleBuilder())
            ->setFontName('Times New Roman')
            ->setFontSize(11)
            ->setBorder($border)
            ->build();

        // dd( new JsonResponse($data) );
        (new FastExcel($data))
            ->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->export(
                storage_path('app/public/' . $path . '/' . $fileName),
                function ($data) {


                    return [
                        'Mã HS' => $data->certificate_id,
                        'Tên khách hàng' => $data->certificate->petitioner_name,
                        'Nhân viên kinh doanh' => $data->certificate->appraiserSale ? $data->certificate->appraiserSale->name : '',
                        'Chuyên viên thẩm định' => $data->certificate->appraiserPerform ? $data->certificate->appraiserPerform->name : '',
                        'Thẩm định viên' => $data->certificate->appraiser ? $data->certificate->appraiser->name : '',
                        'Đại diện pháp luật' => $data->certificate->appraiserManager ? $data->certificate->appraiserManager->name : '',
                        'Giá trị thanh toán' => $data->amount,
                        'Nội dung' => $data->for_payment_of,
                        'Ngày thanh toán' => \Carbon\Carbon::parse($data->pay_date)->format('d-m-Y'),
                    ];
                }
            );


        $this->formatColumn($path, $fileName);

        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/' . $fileName);
        $data['file_name'] = $fileName;
        return $data;
    }
    private function formatColumn($path, $fileName)
    {
        $reader = new Xlsx();
        $spreadSheet = new Spreadsheet();
        $spreadSheet = $reader->load(storage_path('app/public/' . $path . $fileName));
        $spreadSheet->setActiveSheetIndex(0);
        $activeSheet = $spreadSheet->getActiveSheet();

        $lastCol = 'A';
        foreach ($activeSheet->getColumnIterator() as $column) {
            $lastCol = $column->getColumnIndex();
            $cellAddress = $lastCol . '1';
            $cellValue = strval($activeSheet->getCell($cellAddress)->getValue());
            if ($cellValue === "Đã thanh toán" || $cellValue === "Giá trị thanh toán" || $cellValue === "Còn nợ") {
                $activeSheet->getStyle($lastCol)->getNumberFormat()->setFormatCode('#,##0');
                $activeSheet->getColumnDimension($lastCol)->setAutoSize(true);
            } else {
                $activeSheet->getColumnDimension($lastCol)->setAutoSize(true);
            }
        }
        $objWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');
        $objWriter->save(storage_path('app/public/' . $path . '/' . $fileName));

        //Cleanup
        $spreadSheet->disconnectWorksheets();
        unset($spreadSheet);
    }
}
