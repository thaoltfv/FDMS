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

class ExportCertificateAssets
{
    use ResponseTrait;
    public function exportAsset($data){
        // dd($data[0]->frontside);
        $fontName = 'Times New Roman';
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'certification_assets/' . $now->format('Y') . '/' . $now->format('m') . '/';
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
                    'Vị trí' => ($data->properties[0]->properties == 0) ? 'Hẻm' :'Mặt tiền',
                    'Tên TSTĐ' => $data->appraise_asset,
                    'Tổng DT đất' => $data->properties[0]->appraise_land_sum_area,
                    'Tổng DT xây dựng' => $data->total_construction_area,
                    'Tổng giá trị (VNĐ)' => floatval($data->total_price) ,
                    'Người tạo' => isset($data->createdBy->name) ? $data->createdBy->name : '',
                    'Ngày tạo' => \Carbon\Carbon::parse($data->created_at)->format('Y-m-d')  ,
                    'Trạng thái' =>  $data->status_text,
                ];}
            );
        // $writer = IOFactory::load(storage_path('app/public/'. $path. $fileName));
        $reader= new Xlsx();
        $spreadSheet= new Spreadsheet();
        $spreadSheet= $reader->load(storage_path('app/public/'. $path. $fileName));
        $spreadSheet->setActiveSheetIndex(0);
        $activeSheet = $spreadSheet->getActiveSheet();
        $headers = ValueDefault::CERTIFICATION_ASSET_COLUMN_LIST;
        $alpha = ValueDefault::ALPHA;
        $stt = 0;
        $title = 'Thống Kê Tài Sản Thẩm Định';
        $date = 'Từ ngày '. $fromDate .' đến ngày '. $toDate;
        $activeSheet->insertNewRowBefore(1,3);
        $activeSheet->mergeCells('A2:J2');
        $activeSheet->setCellValue('A2', $title);
        $activeSheet->getStyle('A2:J2')->getFont()->setBold(true)->setName($fontName)->setSize(16);
        $activeSheet->getStyle('A2:j2')->getAlignment()->setHorizontal('center');

        $activeSheet->mergeCells('A3:J3');
        $activeSheet->setCellValue('A3', $date);
        $activeSheet->getStyle('A3:J3')->getFont()->setItalic(true)->setName($fontName);
        $activeSheet->getStyle('A3:j3')->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('A4:j4')->getAlignment()->setHorizontal('center')->setVertical('center');

        $activeSheet->getColumnDimension('A')->setWidth(10.30);
        $activeSheet->getColumnDimension('B')->setWidth(12.30);
        $activeSheet->getColumnDimension('C')->setWidth(7.30);
        $activeSheet->getColumnDimension('D')->setWidth(37.30);
        $activeSheet->getColumnDimension('E')->setWidth(11.30);
        $activeSheet->getColumnDimension('F')->setWidth(11.30);
        $activeSheet->getColumnDimension('G')->setWidth(12.80);
        $activeSheet->getColumnDimension('H')->setWidth(15.30);
        $activeSheet->getColumnDimension('I')->setWidth(13.30);
        $activeSheet->getColumnDimension('J')->setWidth(13.30);

        $countData = count($data) + 4;
        foreach($headers as $item){
            $cellAddress = "$alpha[$stt]4:$alpha[$stt]$countData";
            if(Str::contains($item, 'Tổng')){
                if(Str::contains($item, 'Tổng giá trị')){
                    $activeSheet->getStyle( $cellAddress)
                        ->getNumberFormat()->setFormatCode('###,###');
                }else{
                    $activeSheet->getStyle( $cellAddress)
                    ->getNumberFormat()->setFormatCode('###,##0.#0');
                }
            }
            if(Str::contains($item, 'Ngày')){
                $activeSheet->getStyle($cellAddress)
                    ->getNumberFormat()->setFormatCode(StyleNumberFormat::FORMAT_DATE_DDMMYYYY);
            }
            if(Str::contains($item, 'Tên')){
                $activeSheet->getStyle( $cellAddress)->getAlignment()->setWrapText(true);
            }
            // $activeSheet->getColumnDimension($alpha[$stt])->setAutoSize(true);
            $stt++;
        }

        $objWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');
        $objWriter->save(storage_path('app/public/'. $path. '/'. $fileName ));

        //Cleanup
        $spreadSheet->disconnectWorksheets();
        unset($spreadSheet);

        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/'. $fileName );
        $data['file_name'] = $fileName;
        return $data;
    }
}
