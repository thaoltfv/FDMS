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

class ExportPreCertificate
{
    use ResponseTrait;
    public function exportPre($data)
    {
        // $data = CommonService::exportCertificateAssets();
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/' . 'certification_briefs/' . $now->format('Y') . '/' . $now->format('m') . '/';
        if (!File::exists(storage_path('app/public/' . $path))) {
            File::makeDirectory(storage_path('app/public/' . $path), 0755, true);
        }
        $downloadDate = $now->format('dmY');
        $downloadTime = $now->format('Hi');
        $fileName = 'Export Data' . '_' . $downloadTime . '_' . $downloadDate . '.xlsx';
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
                    $totalDebt = 0;

                    foreach ($data->payments as $item) {
                        $totalDebt += $item->amount;
                    }
                    $totalRemain = $data->total_service_fee - $totalDebt;
                    return [
                        'Mã YCSB' => 'YCSB_' . $data->id,
                        'Giai đoạn' =>  $data->status_text,
                        'Khách hàng' => $data->petitioner_name,
                        'MST(CMND)' => $data->petitioner_identity_card,
                        'Địa chỉ' => $data->petitioner_address,
                        'Mục đích thẩm định' => $data->appraisePurpose->name ?? '',
                        'Loại sơ bộ' => $data->preType->name ?? '',
                        'Tên tài sản sơ bộ' => $data->pre_asset_name,
                        'Tổng giá trị sơ bộ' => $data->total_preliminary_value,
                        'Đối tác' => $data->customer->name ?? '',
                        'Địa chỉ' => $data->customer->address ?? '',
                        'Liên hệ' =>  $data->customer->phone ?? '',
                        'Tổng phí dịch vụ' => $data->total_service_fee,
                        'Chiết khấu' => $data->commission_fee,
                        'Đã thanh toán' => $totalDebt,
                        'Còn nợ' => $totalRemain,
                        'NV Kinh doanh' => $data->appraiserSale->name ?? '',
                        'CV nghiệp vụ' =>  $data->appraiserPerform->name ?? '',
                        'QL Nghiệp vụ' => $data->appraiserBusinessManager->name ?? '',
                        'Người tạo' => isset($data->createdBy->name) ? $data->createdBy->name : '',
                        'Ngày tạo' => \Carbon\Carbon::parse($data->created_at)->format('Y-m-d'),
                        'Mã HSTĐ' => 'HSTD_' . $data->certificate_id,
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
            if ($cellValue === "Tên tài sản sơ bộ") {
                $activeSheet->getColumnDimension($lastCol)->setWidth(40);
            } else if ($cellValue === "Tổng phí dịch vụ") {
                $activeSheet->getStyle($lastCol)->getNumberFormat()->setFormatCode('###,###');
                $activeSheet->getColumnDimension($lastCol)->setAutoSize(true);
            } else if ($cellValue === "Địa chỉ") {
                $activeSheet->getColumnDimension($lastCol)->setWidth(40);
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
