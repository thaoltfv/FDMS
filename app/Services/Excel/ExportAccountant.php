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
                        'Tên khách hàng' => $data->preCertificate->petitioner_name,
                        'Nhân viên kinh doanh' => $data->preCertificate->appraiserSale ? $data->preCertificate->appraiserSale->name : '',
                        'Chuyên viên thẩm định' => $data->preCertificate->appraiserPerform ? $data->preCertificate->appraiserPerform->name : '',
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
            ->setFontColor(Color::RGB(255, 0, 0))
            ->setBackgroundColor(Color::RGB(217, 217, 217))
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
            ->addAutoIncrementColumn('STT')
            ->export(
                storage_path('app/public/' . $path . '/' . $fileName),
                function ($data) {
                    return [
                        'Mã HS' => $data->id,
                        'Số chứng thư' => $data->certificate_num ?? '',
                        'Ngày phát hành chứng thư' => isset($data->certificate_date) ?  \Carbon\Carbon::parse($data->certificate_date)->format('d-m-Y') : '',
                        'Số hợp đồng' => $data->document_num ?? '',
                        'Ngày phát hành hợp đồng' =>  isset($data->document_date) ?  \Carbon\Carbon::parse($data->document_date)->format('d-m-Y') : '',
                        'Đơn vị yêu cầu' => $data->petitioner_name,
                        'Thời điểm TĐG' => isset($data->appraise_date) ?  'Tháng ' . date_format(date_create($data->appraise_date), "m/Y")  : '',
                        'Tổng giá dịch vụ TĐG' => isset($data->service_fee) ?  $data->service_fee  : '',
                        'Giá dịch vụ TĐG gồm VAT' => '',
                        'Giá dịch vụ TĐG chưa VAT' => '',
                        'Thuế VAT' => '',
                        'Đại diện pháp luật' => $data->appraiserManager ? $data->appraiserManager->name : '',
                        'Thẩm định viên' => $data->appraiser ? $data->appraiser->name : '',
                        'Người khai thác' => $data->appraiserSale ? $data->appraiserSale->name : '',
                        'Chi nhánh MSB' => '',
                        'NV thực hiện' => $data->appraiserPerform ? $data->appraiserPerform->name : '',
                        'Địa điểm thẩm định' => $data->survey_location ?? '',
                        'Phương tiện di chuyển' =>  '',
                        'Ghi chú' =>  '',
                        'Tạm ứng' => isset($data->payments) && count($data->payments) ? $this->calcAdvancePayment($data->payments) : '',
                        'CÔNG NỢ (Bao gồm VAT)' => isset($data->payments) && count($data->payments) ? $data->service_fee -  $this->calcAdvancePayment($data->payments) :  $data->service_fee,
                        'Tình trạng thanh toán' => '',
                        'Xuất hóa đơn' => '',
                        'Tháng tiền về' => '',
                        'Số hóa đơn' => '',
                        'Thuế suất' => '',
                        'Tình trạng hợp đồng' => '',
                        'BBTL' => '',
                        'Đã chi lương khoán' => '',
                        'Tháng quyết toán' => ''
                    ];
                }
            );


        $this->formatColumn($path, $fileName);

        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/' . $fileName);
        $data['file_name'] = $fileName;
        return $data;
    }
    private function calcAdvancePayment($listPayment)
    {
        $totalAdvance = 0;
        foreach ($listPayment as  $payment) {
            $totalAdvance += $payment->amount;
        }
        return $totalAdvance;
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
