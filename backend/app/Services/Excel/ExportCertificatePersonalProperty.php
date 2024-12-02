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

class ExportCertificatePersonalProperty
{
    use ResponseTrait;
    public function exportAsset($data){
        // dd($data[0]->frontside);
        $fontName = 'Times New Roman';
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'certification_personal_property/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if(!File::exists(storage_path('app/public/'. $path))){
            File::makeDirectory(storage_path('app/public/'. $path), 0755, true);
        }
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $fileName = 'TSTĐ' . '_' . $downloadTime . '_' . $downloadDate .'.xlsx';
        $border = (new BorderBuilder())
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->build();

        $header_style = (new StyleBuilder())->setFontName($fontName)->setFontBold()->setBorder($border)->build();

        $rows_style = (new StyleBuilder())
        ->setFontName($fontName)
        ->setFontSize(11)
        ->setBorder($border)
        ->build();
        // dd($data);
        (new FastExcel($data))
            ->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->export(storage_path('app/public/'. $path. '/'. $fileName ) ,function($data){
                return [
                    'Mã TSTĐ' => 'TSTD_'. $data->id,
                    'Loại TSTĐ' =>CommonService::mbCaseTitle($data->assetType->description) ,
                    'Tên TSTĐ' => $data->name,
                    'Đvt' => $data->unit,
                    'Số lượng' => $data->quantity,
                    'Đơn giá (VNĐ)' => floatval($data->unit_price),
                    'Thành tiền (VNĐ)' => floatval($data->total_price) ,
                    'Ngày tạo' => \Carbon\Carbon::parse($data->created_at)->format('d/m/Y')  ,
                    'Người tạo' => isset($data->createdBy->name) ? $data->createdBy->name : '',
                ];}
            );

        $this->formatColumn($path, $fileName);

        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/'. $fileName );
        $data['file_name'] = $fileName;
        return $data;
    }

    private function formatColumn($path, $fileName)
    {
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $reader= new Xlsx();
        $spreadSheet= new Spreadsheet();
        $spreadSheet= $reader->load(storage_path('app/public/'. $path. $fileName));
        $spreadSheet->setActiveSheetIndex(0);
        $activeSheet = $spreadSheet->getActiveSheet();

        $lastCol = 'A';
        foreach ($activeSheet->getColumnIterator() as $column) {
            $lastCol = $column->getColumnIndex();
            $cellAddress = $lastCol . '1';
            $cellValue = strval($activeSheet->getCell($cellAddress)->getValue());
            if ($cellValue === "Tên TSTĐ") {
                $activeSheet->getColumnDimension($lastCol)->setWidth(40);
            } else if ($cellValue === "Đơn giá (VNĐ)") {
                $activeSheet->getStyle($lastCol)->getNumberFormat()->setFormatCode('###,###');
                $activeSheet->getColumnDimension($lastCol)->setAutoSize(true);
            } else if ($cellValue === "Thành tiền (VNĐ)") {
                $activeSheet->getStyle($lastCol)->getNumberFormat()->setFormatCode('###,###');
                $activeSheet->getColumnDimension($lastCol)->setAutoSize(true);
            } else {
                $activeSheet->getColumnDimension($lastCol)->setAutoSize(true);
            }
        }

        $title = 'Thống Kê Tài Sản Thẩm Định';
        $date = 'Từ ngày '. $fromDate .' đến ngày '. $toDate;
        $activeSheet->insertNewRowBefore(1,3);
        $activeSheet->mergeCells('A2:' . $lastCol .'2');
        $activeSheet->setCellValue('A2', $title);
        $activeSheet->getStyle('A2:' . $lastCol .'2')->getFont()->setBold(true)->setSize(16);
        $activeSheet->getStyle('A2:' . $lastCol .'2')->getAlignment()->setHorizontal('center');

        $activeSheet->mergeCells('A3:' . $lastCol .'3');
        $activeSheet->setCellValue('A3', $date);
        $activeSheet->getStyle('A3:' . $lastCol .'3')->getFont()->setItalic(true);
        $activeSheet->getStyle('A3:' . $lastCol .'3')->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('A4:' . $lastCol .'4')->getAlignment()->setHorizontal('center')->setVertical('center');

        $objWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');
        $objWriter->save(storage_path('app/public/'. $path. '/'. $fileName ));

        //Cleanup
        $spreadSheet->disconnectWorksheets();
        unset($spreadSheet);
    }
}
