<?php

namespace App\Services\Document;

use App\Enum\ValueDefault;
use App\Http\ResponseTrait;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\JcTable;

class AssetReportPdf
{
    use ResponseTrait;

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function generatePdf($objects, $user): array
    {

        define("PRINT_DOCX_FORMAT_DEFAULT", 'docx');
        define("PRINT_PDF_FORMAT", 'pdf');
        $phpWord = new PhpWord();
        $format = PRINT_PDF_FORMAT;
        $phpWord->setDefaultFontName('DejaVu Serif');
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');
        $phpWord->setDefaultFontSize(9);

        $styleTable = [
            'borderSize' => 1,
            'align' => JcTable::CENTER
        ];

        $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';

        $section = $phpWord->addSection();
        $currentAsset = 1;
        foreach ($objects as $object) {
            if ($currentAsset > 1) {
                $section->addPageBreak();
            }
            $currentAsset += 1;
            $count = 0;
            $coordinates = explode(',', $object->coordinates);
            $textRun = $section->addTextRun();
            $textRun->addText('CÔNG TY CP THẨM ĐỊNH GIÁ ĐỒNG NAI', ['bold' => true], ['align' => JcTable::START]);
            $textRun->addText(str_repeat('&nbsp;', 30));
            if ($object->migrate_status == ValueDefault::MIGRATION_STATUS_DEFAULT) {
                $textRun->addText("Phiếu số: TSC_" . $object->id, ['italic' => true], ['align' => JcTable::END]);
            } else {
                $textRun->addText("Phiếu số: TSS_" . $object->id, ['italic' => true], ['align' => JcTable::END]);
            }

            $section->addText('PHIẾU THU THẬP THÔNG TIN VỀ TÀI SẢN SO SÁNH ', ['bold' => true, 'size' => 13], ['align' => JcTable::CENTER]);
            $section->addText("(Áp dụng đối với BĐS – Quyền sử dụng đất + Công trình xây dựng)", ['italic' => true], ['align' => JcTable::CENTER]);

            $textRun = $section->addTextRun();
            $textRun->addText('- Loại tài sản: ');
            $textRun->addText(isset($object->assetType->description) ? $this->mb_ucfirst(mb_strtolower($object->assetType->description)) : '', ['bold' => true]);

            $textRun = $section->addTextRun();
            $textRun->addText('- Địa chỉ (1): ');
            $textRun->addText($object->full_address, ['bold' => true]);

            $textRun = $section->addTextRun();
            $textRun->addText('- Thời điểm chuyển nhượng: ');
            $textRun->addText(date("d/m/Y", strtotime($object->public_date)), ['bold' => true]);

            $textRun->addText(str_repeat('&nbsp;', 50 - strlen($object->public_date) * 2));
            $textRun->addText('- Loại giao dịch: ');
            $textRun->addText(isset($object->transactionType->description) ? $this->mb_ucfirst(mb_strtolower($object->transactionType->description)) : '', ['bold' => true]);

            $textRun = $section->addTextRun();
            $textRun->addText('- Giá bất động sản chuyển nhượng: ');
            $textRun->addText(number_format($object->total_amount, 0, ',', '.'), ['bold' => true]);
            $textRun->addText('đ/BĐS');

            /*
            $textRun = $section->addTextRun();
            $textRun->addText('- Giá điều chỉnh: ');
            $textRun->addText(number_format($object->adjust_amount, 0, ',', '.'), ['bold' => true]);
            $textRun->addText('đ/BĐS');

            $textRun = $section->addTextRun();
            $textRun->addText('- Giá chuyển nhượng sau điều chỉnh: ');
            $textRun->addText(number_format($object->total_estimate_amount, 0, ',', '.'), ['bold' => true]);
            $textRun->addText('đ/BĐS');
            */

            $textRun = $section->addTextRun();
            $textRun->addText('- Nguồn thông tin: ');
            $textRun->addText(isset($object->source->description) ? $this->mb_ucfirst(mb_strtolower($object->source->description)) : '', ['bold' => true]);

            $textRun = $section->addTextRun();
            $textRun->addText("- Người liên hệ: ");
            $textRun->addText(htmlspecialchars($object->contact_person), ['bold' => true]);
            $textRun->addText(str_repeat('&nbsp;', 20));
            $textRun->addText("Số điện thoại liên hệ: ");
            $textRun->addText(htmlspecialchars($object->contact_phone), ['bold' => true]);

            if (isset($object->assetType->description) && ($object->assetType->description == 'CHUNG CƯ')) {

                $section->addText('1. Các thông tin về chung cư ', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tên chung cư/dự án: ');
                $textRun->addText(htmlspecialchars($object->apartment->name), ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tọa độ X: ');
                $textRun->addText($coordinates[0] ?? '', ['bold' => true]);
                $textRun->addText(str_repeat('&nbsp;', 10));
                $textRun->addText("Tọa độ Y: ");
                $textRun->addText($coordinates[1] ?? '', ['bold' => true]);

                foreach ($object->blockSpecification as $specification) {
                    $textRun = $section->addTextRun();
                    $textRun->addText('- Block (khu): ');
                    $textRun->addText(isset($specification->blockLists) ? htmlspecialchars($specification->blockLists->name) : '', ['bold' => true]);
                }

                foreach ($object->roomDetails as $roomDetail) {
                    $textRun = $section->addTextRun();
                    $textRun->addText('- Mã căn hộ: ');
                    $textRun->addText(htmlspecialchars($roomDetail->room_num), ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Tầng: ');
                    $textRun->addText(htmlspecialchars($roomDetail->floor), ['bold' => true]);
                }

                $section->addText('2. Các thông tin về Block ', ['bold' => true]);
                foreach ($object->blockSpecification as $specification) {

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Năm xây dựng: ');
                    $textRun->addText($specification->built_year, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Tổng số tầng: ');
                    $textRun->addText($specification->total_floor, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Số tầng hầm: ');
                    $textRun->addText($specification->basement_floor, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Số tầng thương mại: ');
                    $textRun->addText($specification->commercial_floor, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Số tầng ở: ');
                    $textRun->addText($specification->living_floor, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Số lượng thang máy: ');
                    $textRun->addText($specification->lift_number, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Tiện ích khác: ');
                    $textRun->addText($specification->other_utilities, ['bold' => true]);
                    $basicUtility = [];
                    foreach ($specification->basicUtilities as $basicUtilities) {
                        $basicUtility[] = $basicUtilities->description;
                    }
                    $textRun = $section->addTextRun();
                    $textRun->addText('- Tiện ích cơ bản: ');
                    $textRun->addText(implode(', ', $basicUtility), ['bold' => true]);

                    $section->addText('3. Thông tin chi tiết căn hộ  ', ['bold' => true]);
                    foreach ($object->roomDetails as $roomDetail) {
                        $textRun = $section->addTextRun();
                        $textRun->addText('- Căn góc: ');
                        $twoSidesRoom = $roomDetail->two_sides_room ? 'Có' : 'Không';
                        $textRun->addText($twoSidesRoom, ['bold' => true]);

                        $textRun = $section->addTextRun();
                        $textRun->addText('- Diện tích (' . $m2 . '): ');
                        $textRun->addText($roomDetail->area, ['bold' => true]);
                        $textRun->addText($m2);

                        $textRun = $section->addTextRun();
                        $textRun->addText('- Số phòng ngủ: ');
                        $textRun->addText($roomDetail->bedroom_num, ['bold' => true]);

                        $textRun = $section->addTextRun();
                        $textRun->addText('- Số phòng WC: ');
                        $textRun->addText($roomDetail->wc_num, ['bold' => true]);

                        $textRun = $section->addTextRun();
                        $textRun->addText('- Hướng chính: ');
                        $textRun->addText((isset($roomDetail->direction->description) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($roomDetail->direction->description))) : ''), ['bold' => true]);

                        $textRun = $section->addTextRun();
                        $textRun->addText('- Tổng quan chất lượng nội thất: ');
                        $textRun->addText((isset($roomDetail->furnitureQuality->description) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($roomDetail->furnitureQuality->description))) : ''), ['bold' => true]);

                        $table = $section->addTable($styleTable);
                        $table->addRow(400);
                        $table->addCell(3000)->addText('Tên nội thất', ['bold' => true], ['align' => JcTable::CENTER]);
                        $table->addCell(3000)->addText('Số lượng', ['bold' => true], ['align' => JcTable::CENTER]);
                        $table->addCell(3000)->addText('Mô tả', ['bold' => true], ['align' => JcTable::CENTER]);
                        foreach ($roomDetail->roomFurnitureDetails as $roomFurnitureDetail) {
                            $table->addRow(400);
                            $table->addCell(3000)->addText((isset($roomFurnitureDetail->name) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($roomFurnitureDetail->name))) : ''), null, ['align' => JcTable::START]);
                            $table->addCell(3000)->addText($roomFurnitureDetail->number ?? '', null, ['align' => JcTable::CENTER]);
                            $table->addCell(3000)->addText((isset($roomFurnitureDetail->description) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($roomFurnitureDetail->description))) : ''), null, ['align' => JcTable::CENTER]);
                        }
                    }
                }
            } else {
                $section->addText('1. Các thông tin về thửa đất ', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tờ bản đồ số: ');
                $textRun->addText(htmlspecialchars($object->doc_no), ['bold' => true]);
                $textRun->addText(str_repeat('&nbsp;', 30));
                $textRun->addText('Thửa đất số: ');
                $textRun->addText(htmlspecialchars($object->land_no), ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tọa độ X: ');
                $textRun->addText($coordinates[0] ?? '', ['bold' => true]);
                $textRun->addText(str_repeat('&nbsp;', 10));
                $textRun->addText("Tọa độ Y: ");
                $textRun->addText($coordinates[1] ?? '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Diện tích: ');
                $textRun->addText($object->total_area, ['bold' => true]);
                $textRun->addText($m2);
                $landType = '';
                $landTypePurpose = [];
                $legal = '';
                $landShape = '';
                $size = '';
                $position = [];
                $comparePropertyTurningTime = [];
                $topographic = isset($object->topographicData->description) ? mb_strtolower($object->topographicData->description) : '';
                $frontSide = '';
                $frontSides = [];
                $unfrontSides = [];
                foreach ($object->properties as $property) {
                    $count += count($property->pic);
                    if ($property->front_side == 1) {
                        $frontSides[] = $property;
                    } else {
                        $unfrontSides[] = $property;
                    }
                }
                if ($frontSides) {
                    $frontSide = 'Mặt tiền';
                    foreach ($frontSides as $property) {
                        foreach ($property->propertyDetail as $detail) {
                            $landTypePurpose[] = (isset($detail->landTypePurposeData->description) ? $this->mb_ucfirst(mb_strtolower($detail->landTypePurposeData->description)) : '') . ' (' . $detail->total_area . $m2 . ')';
                            $position[] = (isset($detail->positionType->description) && isset($detail->landTypePurposeData->description)) ? ($this->mb_ucfirst(mb_strtolower($detail->landTypePurposeData->description)) . ' - ' . $this->mb_ucfirst(mb_strtolower($detail->positionType->description))) : '';
                            $landType = (isset($property->landType->description) ? $this->mb_ucfirst(mb_strtolower($property->landType->description)) : '');
                            $legal = (isset($property->legal->description) ? $this->mb_ucfirst(mb_strtolower($property->legal->description)) : '');
                            $landShape = (isset($property->landShape->description) ? $this->mb_ucfirst(mb_strtolower($property->landShape->description)) : '');
                            $size = $property->front_side_width . 'm' . 'x' . $property->insight_width . 'm';
                            $comparePropertyTurningTime = $property->comparePropertyTurningTime;
                        }
                    }
                } elseif ($unfrontSides) {
                    $frontSide = 'Trong hẻm';
                    foreach ($unfrontSides as $property) {
                        foreach ($property->propertyDetail as $detail) {
                            $landTypePurpose[] = (isset($detail->landTypePurposeData->description) ? $this->mb_ucfirst(mb_strtolower($detail->landTypePurposeData->description)) : '') . ' (' . $detail->total_area . $m2 . ')';
                            $position[] = (isset($detail->positionType->description) && isset($detail->landTypePurposeData->description)) ? ($this->mb_ucfirst(mb_strtolower($detail->landTypePurposeData->description)) . ' - ' . $this->mb_ucfirst(mb_strtolower($detail->positionType->description))) : '';
                            $landType = (isset($property->landType->description) ? $this->mb_ucfirst(mb_strtolower($property->landType->description)) : '');
                            $legal = (isset($property->legal->description) ? $this->mb_ucfirst(mb_strtolower($property->legal->description)) : '');
                            $landShape = (isset($property->landShape->description) ? $this->mb_ucfirst(mb_strtolower($property->landShape->description)) : '');
                            $size = $property->front_side_width . 'm' . 'x' . $property->insight_width . 'm';
                            $comparePropertyTurningTime = $property->comparePropertyTurningTime;
                        }
                    }
                }

                $textRun = $section->addTextRun();
                $textRun->addText('- Loại đất: ');
                $textRun->addText($landType, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Mục đích sử dụng: ');
                $textRun->addText(implode(', ', $landTypePurpose), ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Giấy chứng nhận QSDĐ: ');
                $textRun->addText(htmlspecialchars($legal), ['bold' => true]);

                $textRun->addText(str_repeat('&nbsp;', 50 - strlen($legal) * 2));
                $textRun->addText('Hình dáng: ');
                $textRun->addText($landShape, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Kích thước: ');
                $textRun->addText($size, ['bold' => true]);

                $textRun->addText(str_repeat('&nbsp;', 60 - strlen($size) * 2));
                $textRun->addText('Địa hình: ');
                $textRun->addText($topographic ? ($this->mb_ucfirst(mb_strtolower($topographic))) : '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tên đường (phố): ');
                $textRun->addText($object->street->name ? htmlspecialchars($object->street->name) : '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Đoạn đường (từ đâu đến đâu): ');
                $textRun->addText($object->distance->name ? htmlspecialchars($object->distance->name) : '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Vị trí đất theo QĐUBT: ');
                $textRun->addText(implode(', ', $position), ['bold' => true]);


                $textRun = $section->addTextRun();
                $textRun->addText('- Vị trí bất động sản: ');
                $textRun->addText($frontSide, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Số lần rẽ (quẹo) từ mặt tiền đường vào đến BĐS: ');
                $textRun->addText((count($comparePropertyTurningTime) ?? 0) . ' lần', ['bold' => true]);

                if (count($comparePropertyTurningTime) > 0) {
                    $table = $section->addTable($styleTable);
                    $table->addRow(400);
                    $table->addCell(3000)->addText('Chi tiết lần rẽ (quẹo)', ['bold' => true], ['align' => JcTable::CENTER]);
                    $table->addCell(3000)->addText('Hiện trạng hẻm', ['bold' => true], ['align' => JcTable::CENTER]);
                    $table->addCell(3000)->addText('Chiều rộng hẻm (m)', ['bold' => true], ['align' => JcTable::CENTER]);

                    foreach ($comparePropertyTurningTime as $turning) {
                        $table->addRow(400);
                        $table->addCell(3000)->addText($turning->turning, null, ['align' => JcTable::CENTER]);
                        $table->addCell(3000)->addText(isset($turning->material->description) ? $this->mb_ucfirst(mb_strtolower($turning->material->description)) : '', null, ['align' => JcTable::CENTER]);
                        $table->addCell(3000)->addText($turning->main_road_length . 'm', null, ['align' => JcTable::CENTER]);
                    }
                }

                $section->addText('2. Các thông tin về tài sản gắn liền với đất', ['bold' => true]);
                $section->addText('2.1. Công trình xây dựng: ', ['bold' => true]);
                $currenTangibleAssets = 1;
                foreach ($object->tangibleAssets as $key => $tangibleAsset) {
                    if ($currenTangibleAssets > 1) {
                        $section->addTextBreak(1);
                    }
                    $currenTangibleAssets++;
                    $textRun = $section->addTextRun();
                    $textRun->addText('- Loại nhà: ');
                    $textRun->addText((isset($tangibleAsset->buildingType->description) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($tangibleAsset->buildingType->description))) : ''), ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Cấp nhà: ');
                    $textRun->addText((isset($tangibleAsset->buildingCategory->description) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($tangibleAsset->buildingCategory->description))) : ''), ['bold' => true]);

                    if ($tangibleAsset->other_building) {
                        $textRun = $section->addTextRun();
                        $textRun->addText('- Tên công trình: ');
                        $textRun->addText(htmlspecialchars($tangibleAsset->other_building), ['bold' => true]);
                        $textRun->addText(str_repeat('&nbsp;', (33 - strlen($tangibleAsset->other_building) * 2) > 0 ? (33 - strlen($tangibleAsset->other_building) * 2) : 10));
                        $textRun->addText(" Mô tả: ");
                        $textRun->addText(htmlspecialchars($tangibleAsset->description), ['bold' => true]);
                    }

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Năm xây dựng: ');
                    $textRun->addText($tangibleAsset->start_using_year, ['bold' => true]);
                    $textRun->addText(str_repeat('&nbsp;', (38 - strlen($tangibleAsset->start_using_year) * 2) > 0 ? (38 - strlen($tangibleAsset->start_using_year) * 2) : 10));
                    $textRun->addText(" Chất lượng còn lại: ");
                    $textRun->addText($tangibleAsset->remaining_quality, ['bold' => true]);
                    $textRun->addText('%');

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Diện tích sàn xây dựng: ');
                    $textRun->addText(number_format($tangibleAsset->total_construction_base, 2, ',', '.'), ['bold' => true]);
                    $textRun->addText(" " . $m2 . " ");
                    $textRun->addText(str_repeat('&nbsp;', (19 - strlen($tangibleAsset->total_construction_base) * 2) > 0 ? (19 - strlen($tangibleAsset->total_construction_base) * 2) : 10));
                    $textRun->addText("Số tầng: ");

                    $textRun->addText(htmlspecialchars($tangibleAsset->floor), ['bold' => true]);

                    $gpxd = $tangibleAsset->gpxd ? 'Có' : 'Không';

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Giấy phép xây dựng: ');
                    $textRun->addText($gpxd, ['bold' => true]);
                    $count += count($tangibleAsset->pic);
                }
                $section->addText('2.2. Tài sản khác: ', ['bold' => true]);
                foreach ($object->otherAssets as $otherAssets) {
                    $textRun = $section->addTextRun();
                    $textRun->addText('- Loại tài sản khác: ');
                    $textRun->addText(isset($otherAssets->other_asset) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($otherAssets->other_asset))) : '', ['bold' => true]);
                    $textRun->addText(str_repeat('&nbsp;', (70 - strlen($otherAssets->other_asset) * 2) > 0 ? (70 - strlen($otherAssets->other_asset) * 2) : 10));
                    $textRun->addText("Giá trị: ");
                    $textRun->addText(number_format($otherAssets->total_amount, 0, ',', '.'), ['bold' => true]);
                    $textRun->addText(' đồng');
                    $count += count($otherAssets->pic);
                }
            }
            $count += count($object->pic);
            if (isset($object->assetType->description) && ($object->assetType->description == 'CHUNG CƯ')) {
                $textRun = $section->addTextRun();
                $textRun->addText('4. Hình ảnh hiện trạng chung cư: ', ['bold' => true]);
                $textRun->addText(str_repeat('&nbsp;', 20));
                $textRun->addText(($count > 0) ? 'Có ' : 'Không có ', ['bold' => true]);
                $section->addTextBreak(1);
            } else {
                $textRun = $section->addTextRun();
                $textRun->addText('3. Hình ảnh hiện trạng đất, nhà: ', ['bold' => true]);
                $textRun->addText(str_repeat('&nbsp;', 20));
                $textRun->addText(($count > 0) ? 'Có ' : 'Không có ', ['bold' => true]);
                $section->addTextBreak(1);
            }

            $textRun = $section->addTextRun();
            $textRun->addText(str_repeat('&nbsp;', 90));
            $textRun->addText('Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y'), null, ['align' => JcTable::CENTER]);
            $textRun = $section->addTextRun();
            $textRun->addText(str_repeat('&nbsp;', 15));
            $textRun->addText('Người thu thập thông tin', ['bold' => true], ['align' => JcTable::CENTER]);
            $textRun->addText(str_repeat('&nbsp;', 40));
            $textRun->addText('Thẩm định viên ', ['bold' => true], ['align' => JcTable::CENTER]);
            $section->addTextBreak(3);
            $textRun = $section->addTextRun();
            $textRun->addText(str_repeat('&nbsp;', 20));
            $textRun->addText($object->createdBy->name ?? '', ['bold' => true], ['align' => JcTable::CENTER]);
            $textRun->addText(str_repeat('&nbsp;', 70 - strlen($object->createdBy->name)));
            $textRun->addText($user->name ?? '', ['bold' => true], ['align' => JcTable::CENTER]);

            $footer = $section->addFooter();
            $textRun = $footer->addTextRun();
            $textRun->addText('Người tạo: ');
            $textRun->addText($object->createdBy->name ?? '', ['bold' => true], ['align' => JcTable::CENTER]);
        }
        $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        $path =  env('STORAGE_DOCUMENTS') . '/' . 'comparison_assets/' . $now->format('Y') . '/' . $now->format('m');
        if (!File::exists(storage_path('app/public/' . $path))) {
            File::makeDirectory(storage_path('app/public/' . $path), 0755, true);
        }
        $objWriter = IOFactory::createWriter($phpWord, 'PDF');
        try {
            $fileName = 'DONAVA_TSSS_' . Carbon::now()->format('Y_m_d') . '.' . $format;

            // $objWriter->save(public_path('uploads/doc/' . $fileName));
            $objWriter->save(storage_path('app/public/' . $path . '/' . $fileName));
        } catch (\Exception $e) {
            throw $e;
        }
        $data = [];
        $data['url'] = Storage::disk('public')->url($path . '/' . $fileName);

        $data['file_name'] = $fileName;
        return $data;
    }

    function mb_ucfirst($string, $encoding = "utf8"): string
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
}
