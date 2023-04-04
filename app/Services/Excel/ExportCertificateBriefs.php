<?php
namespace App\Services\Excel;

use App\Enum\ValueDefault;
use App\Http\ResponseTrait;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportCertificateBriefs
{
    use ResponseTrait;
    public function exportAsset($data){
        // $data = CommonService::exportCertificateAssets();
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'certification_briefs/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if(!File::exists(storage_path('app/public/'. $path))){
            File::makeDirectory(storage_path('app/public/'. $path), 0755, true);
        }
        $downloadDate = $now->format('dmY');
        $downloadTime = $now->format('Hi');
        $fileName = 'HSTĐ' . '_' . $downloadTime . '_' . $downloadDate .'.xlsx';
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
            ->export(storage_path('app/public/'. $path. '/'. $fileName ) ,function($data){
                return [
                    'Mã HSTĐ' => 'HSTD_'. $data->id,
                    'Số hợp đồng' => $data->document_num,
                    'Số chứng thư' => $data->certificate_num,
                    'Khách hàng' => $data->petitioner_name,
                    'Tổng giá trị (VNĐ)' => isset($data->assetPrice[0]->value) ? $data->assetPrice[0]->value : 0,
                    'Nhân viên kinh doanh' => isset($data->appraiserSale->name) ? $data->appraiserSale->name : '',
                    'Chuyên viên thẩm định' => $data->appraiserPerform->name??'',
                    'Thẩm định viên' => $data->appraiser->name??'',
                    'Người tạo' => isset($data->createdBy->name) ? $data->createdBy->name : '',
                    'Ngày tạo' => \Carbon\Carbon::parse($data->created_at)->format('Y-m-d')  ,
                    'Trạng thái' =>  $data->status_text,
                ];}
            );
        $styleBorderArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $reader= new Xlsx();
        $spreadSheet= new Spreadsheet();
        $spreadSheet= $reader->load(storage_path('app/public/'. $path. $fileName));
        $spreadSheet->setActiveSheetIndex(0);
        $activeSheet = $spreadSheet->getActiveSheet();
        $headers = ValueDefault::CERTIFICATION_BRIEF_COLUMN_LIST;
        $alpha = ValueDefault::ALPHA;
        $stt = 0;
        $fontName = 'Times New Roman';
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $title = 'Thống Kê Hồ Sơ Thẩm Định';
        $date = 'Từ ngày '. $fromDate .' đến ngày '. $toDate;
        $appraiseTeam = 'Tổ thẩm định';
        $activeSheet->insertNewRowBefore(1,3);
        $activeSheet->mergeCells('A2:K2');
        $activeSheet->setCellValue('A2', $title);
        $activeSheet->getStyle('A2:K2')->getFont()->setBold(true)->setName($fontName)->setSize(16);
        $activeSheet->getStyle('A2:K2')->getAlignment()->setHorizontal('center');
        $activeSheet->mergeCells('A3:K3');
        $activeSheet->setCellValue('A3', $date);
        $activeSheet->getStyle('A3:K3')->getFont()->setItalic(true)->setName($fontName);
        $activeSheet->getStyle('A3:K3')->getAlignment()->setHorizontal('center');
        $activeSheet->insertNewRowBefore(4,1);
        // $value = $activeSheet->getCell("A4")->getValue();
        $activeSheet->mergeCells('F4:I4');
        $activeSheet->setCellValue('F4', $appraiseTeam);
        $activeSheet->getStyle('F4:I4')->getFont()->setBold(true)->setItalic(false);
        $activeSheet->getStyle('F4:I4')->applyFromArray($styleBorderArray);

        $activeSheet->getColumnDimension('A')->setWidth(10.30);
        $activeSheet->getColumnDimension('B')->setWidth(11.80);
        $activeSheet->getColumnDimension('C')->setWidth(11.80);
        $activeSheet->getColumnDimension('D')->setWidth(29.30);
        $activeSheet->getColumnDimension('E')->setWidth(12.80);
        $activeSheet->getColumnDimension('F')->setWidth(13.30);
        $activeSheet->getColumnDimension('G')->setWidth(13.30);
        $activeSheet->getColumnDimension('H')->setWidth(13.30);
        $activeSheet->getColumnDimension('I')->setWidth(13.30);
        $activeSheet->getColumnDimension('J')->setWidth(10.30);
        $activeSheet->getColumnDimension('K')->setWidth(13.80);

        $countData = count($data)+5;
        foreach($headers as $item){
            $cellAddress = "$alpha[$stt]6:$alpha[$stt]$countData";
            $cellAddressMerge = "$alpha[$stt]4:$alpha[$stt]5";

            switch($item){
                case 'Nhân viên kinh doanh':
                case 'Chuyên viên thẩm định':
                case 'Thẩm định viên':
                case 'Người tạo':
                    $activeSheet->getStyle("$alpha[$stt]5")->getFont()->setBold(true)->setItalic(false);
                    $activeSheet->getStyle("$alpha[$stt]5")->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
                    break;
                default:
                    $value = $activeSheet->getCell("$alpha[$stt]5")->getValue();
                    $activeSheet->mergeCells($cellAddressMerge);
                    $activeSheet->setCellValue("$alpha[$stt]4",$value);
                    $activeSheet->getStyle($cellAddressMerge)->getFont()->setBold(true)->setItalic(false);
                    $activeSheet->getStyle($cellAddressMerge)->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
                    $activeSheet->getStyle($cellAddressMerge)->applyFromArray($styleBorderArray);

            }
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
                    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            }
            if(Str::contains($item, 'Khách hàng')){
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

    public function exportBrieft($datas){
        // $spread = new PhpSpreadsheet();
        $data = array($datas);
        // dd(json_encode(json_decode($datas,true)) );
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)
            ;

        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/'. 'comparison_briefs/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if(!Storage::has($path)){
            Storage::makeDirectory($path);
        }
        $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
        $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
        $fileName = 'HSTĐ' . '_' . $downloadTime . '_' . $downloadDate .'.xlsx';

        $objWriter->save(storage_path('app/public/'. $path. '/'. $fileName ));
        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/'. $fileName);
        $data['file_name'] = $fileName;
        return $data;

    }
}
