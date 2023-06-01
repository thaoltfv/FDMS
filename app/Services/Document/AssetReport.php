<?php

namespace App\Services\Document;

use App\Enum\ValueDefault;
use App\Http\ResponseTrait;
use App\Models\Certificate;
use App\Models\DocumentDictionary;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Shared\Converter;
use App\Services\CommonService;
use File;
use Illuminate\Support\Facades\Storage;

class AssetReport
{
    use ResponseTrait;

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function generateDocx($company, $objects, $format): array
    {
        $certificateId = request()->get('certificate_id');
        if (!empty($certificateId)) {
            $certificate = Certificate::query()->with(['appraiser:id,name', 'appraiserPerform:id,name'])->where('id', $certificateId)->first(['id', 'appraiser_id', 'appraiser_perform_id']);
        }
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);
        $styleTable = [
            'borderSize' => 1,
            'align' => JcTable::CENTER
        ];

        $m2 = 'm</w:t></w:r><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t xml:space="preserve">2</w:t></w:r><w:r><w:rPr></w:rPr><w:t xml:space="preserve">';

        $phpWord->setDefaultParagraphStyle([
			'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(2),
			'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(2),
			'indentation' => array('left' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5), 'right' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(3.5))
		]);
        $tableBasicStyle = array(
            'borderSize' => 'none',
            'cellMargin'  => Converter::inchToTwip(0),
            'width' => 100 * 50,
            'unit' => 'pct',
        );

        $cantSplit = ['cantSplit' => true];

        $section = $phpWord->addSection([
            'footerHeight'=>300,
            'marginTop' => Converter::cmToTwip(2),
            'marginBottom' => Converter::cmToTwip(2),
            'marginRight' => Converter::cmToTwip(2),
            'marginLeft' => Converter::cmToTwip(3)
        ]);
        $print_watermask = DocumentDictionary::where('slug', 'print_watermask')->first() ?: '';
        # watermark
        if($print_watermask and $print_watermask->value === 'yes') {
            $imgName = env('STORAGE_IMAGES','images') .'/'.'company_logo.png';
            $header = $section->addHeader();
            $header->addWatermark(storage_path('app/public/'.$imgName),
                                    array(
                                        'width' => 200,
                                        'marginTop' => 200,
                                        'marginLeft' => 120,
                                        'posHorizontal' => 'absolute',
                                        'posVertical' => 'absolute',
                                    )
                                );
        }

        $currentAsset = 1;
        $objects = $objects->reverse();
        foreach ($objects as $object) {
            if ($currentAsset > 1) {
                $section->addPageBreak();
            }else{
                $footer = $section->addFooter();
                $textRun = $footer->addTextRun();
                $textRun->addText('Người tạo: ',['size'=>8]);
                $textRun->addText($object->createdBy->name??'', ['bold' => true,'size'=>8], ['align' => JcTable::CENTER]);
            }
            $currentAsset += 1;
            $count = 0;
            $coordinates = explode(',', $object->coordinates);
            $table = $section->addTable($tableBasicStyle);
            $table->addRow(400);
            $compare_property_doc = $object->properties[0]->compare_property_doc;

            $table->addCell(5000)->addText(CommonService::formatCompanyName($company), ['bold' => true], ['align' => JcTable::CENTER]);
            if ($object->migrate_status == ValueDefault::MIGRATION_STATUS_DEFAULT) {
                $table->addCell(5000)->addText('Phiếu số: TSC_' . $object->id, ['italic' => true], ['align' => JcTable::END]);
            } else {
                $table->addCell(5000)->addText('Phiếu số: TSS_' . $object->id, ['italic' => true], ['align' => JcTable::END]);

            }
            $section->addText('PHIẾU THU THẬP THÔNG TIN VỀ TÀI SẢN SO SÁNH ', ['bold' => true, 'size' => 13], ['align' => JcTable::CENTER]);
            $section->addText("(Áp dụng đối với BĐS – Quyền sử dụng đất + Công trình xây dựng)", ['italic' => true], ['align' => JcTable::CENTER]);

            $textRun = $section->addTextRun();
            $textRun->addText('- Loại tài sản: ');
            $textRun->addText(isset($object->assetType->description) ? $this->mb_ucfirst(mb_strtolower($object->assetType->description)) : '', ['bold' => true]);

            $textRun = $section->addTextRun();
            $textRun->addText('- Địa chỉ (1): ');
            $textRun->addText($object->full_address, ['bold' => true]);

            $table = $section->addTable('docTable');
            $table->addRow(250);
            $cell = $table->addCell(4000);
            $textRun = $cell->addTextRun();
            $textRun->addText('- Thời điểm: ');
            $textRun->addText(date("d/m/Y", strtotime($object->public_date)), ['bold' => true]);
            $cell = $table->addCell(5000);
            $textRun = $cell->addTextRun();
            $textRun->addText('Loại giao dịch: ');
            $textRun->addText(isset($object->transactionType->description) ? $this->mb_ucfirst(mb_strtolower($object->transactionType->description)) : '', ['bold' => true]);

            $textRun = $section->addTextRun();
            $textRun->addText('- Giá bất động sản: ');
            $textRun->addText(number_format($object->total_amount, 0, ',', '.'), ['bold' => true]);
            $textRun->addText('đ/BĐS');

            $textRun = $section->addTextRun();
            $textRun->addText("- Nguồn thông tin: ");
            $textRun->addText(isset($object->source->description) ? $this->mb_ucfirst(mb_strtolower($object->source->description)) : '', ['bold' => true]);

            $textRun = $section->addTextRun();
            $textRun->addText("- Ghi chú: ");
            $textRun->addText(isset($object->note) ? $this->mb_ucfirst(mb_strtolower(htmlspecialchars($object->note))) : 'Không có.', ['bold' => true]);


            $table = $section->addTable('docTable');
            $table->addRow(250);
            $cell = $table->addCell(4000);
            $textRun = $cell->addTextRun();
            $textRun->addText("- Người liên hệ: ");
            $textRun->addText($object->contact_person, ['bold' => true]);
            $cell = $table->addCell(5000);
            $textRun = $cell->addTextRun();
            $textRun->addText("Số điện thoại liên hệ: ");
            $textRun->addText($object->contact_phone, ['bold' => true]);

            if (isset($object->assetType->description) && ($object->assetType->description == 'CHUNG CƯ')) {

                $section->addText('1. Các thông tin về chung cư ', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tên chung cư/dự án: ');
                $textRun->addText($object->project->name, ['bold' => true]);

                $table = $section->addTable('docTable');
                $table->addRow(250);
                $cell = $table->addCell(4000);
                $textRun = $cell->addTextRun();
                $textRun->addText('- Tọa độ X: ');
                $textRun->addText($coordinates[0] ?? '', ['bold' => true]);
                $cell = $table->addCell(5000);
                $textRun = $cell->addTextRun();
                $textRun->addText("Tọa độ Y: ");
                $textRun->addText($coordinates[1] ?? '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Block (khu): ');
                $textRun->addText(isset($object->block) ? $object->block->name : '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tầng: ');
                $textRun->addText(isset($object->floor) ? $object->floor->name : '', ['bold' => true]);
                $textRun = $section->addTextRun();
                $textRun->addText('- Mã căn hộ: ');
                $textRun->addText(isset($object->apartmentSpecification) ? $object->apartmentSpecification->apartment_name : '', ['bold' => true]);


                $section->addText('2. Các thông tin về Block ', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Năm bàn giao: ');
                $textRun->addText(isset($object->apartmentSpecification) ? $object->apartmentSpecification->handover_year : '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tổng số tầng: ');
                $textRun->addText($object->block->total_floors, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Số tầng hầm: ');
                $textRun->addText($object->block->nb_basement, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Số tầng thương mại: ');
                $textRun->addText($object->block->total_floors - $object->block->nb_living_floor, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Số tầng ở: ');
                $textRun->addText($object->block->nb_living_floor, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Số lượng thang máy: ');
                $textRun->addText($object->block->nb_elevator, ['bold' => true]);

                // $textRun = $section->addTextRun();
                // $textRun->addText('- Tiện ích cơ bản: ');
                // $textRun->addText(implode(', ', $object->apartment_specification->utilities), ['bold' => true]);

                $section->addText('3. Thông tin chi tiết căn hộ ', ['bold' => true]);
                foreach ($object->roomDetails as $roomDetail) {

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Diện tích ('.$m2.'): ');
                    $textRun->addText(number_format($roomDetail->area, 2, ',', '.'), ['bold' => true]);
                    $textRun->addText($m2);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Số phòng ngủ: ');
                    $textRun->addText($roomDetail->bedroom_num, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Số phòng WC: ');
                    $textRun->addText($roomDetail->wc_num, ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Hướng chính: ');
                    $textRun->addText((isset($roomDetail->direction->description) ? $this->mb_ucfirst(mb_strtolower($roomDetail->direction->description)) : ''), ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Tổng quan chất lượng nội thất: ');
                    $textRun->addText((isset($roomDetail->furnitureQuality->description) ? $this->mb_ucfirst(mb_strtolower($roomDetail->furnitureQuality->description)) : ''), ['bold' => true]);

                    $textRun = $section->addTextRun();
                    $textRun->addText('- Mô tả: ');
                    $textRun->addText((isset($roomDetail->description) ? $roomDetail->description : ''), ['bold' => true]);
                }
            } else {
                $section->addText('1. Các thông tin về thửa đất ', ['bold' => true]);

                $table = $section->addTable('docTable');
                $table->addRow(250);
                $cell = $table->addCell(4000);
                $cellRun = $cell->addTextRun();
                $cellRun->addText('- Tờ bản đồ số: ');
                $cellRun->addText($compare_property_doc, ['bold' => true]);
                $cell = $table->addCell(5000);
                $cellRun = $cell->addTextRun();
                $cellRun->addText('Thửa đất số: ');
                $cellRun->addText($compare_property_doc, ['bold' => true]);


                $table = $section->addTable('coordinatesTable');
                $table->addRow(250);
                $cell = $table->addCell(4000);
                $cellRun = $cell->addTextRun();
                $cellRun->addText('- Tọa độ X: ');
                $cellRun->addText($coordinates[0] ?? '', ['bold' => true]);
                $cell = $table->addCell(5000);
                $cellRun = $cell->addTextRun();
                $cellRun->addText("Tọa độ Y: ");
                $cellRun->addText($coordinates[1] ?? '', ['bold' => true]);

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
                            $landType = (isset($property->landType->description) ? $this->mb_ucfirst(mb_strtolower($property->landType->description)) : '');
                            $legal = (isset($property->legal->description) ? $this->mb_ucfirst(mb_strtolower($property->legal->description)) : '');
                            $landShape = (isset($property->landShape->description) ? $this->mb_ucfirst(mb_strtolower($property->landShape->description)) : '');
                            $size = $property->front_side_width . 'm' . 'x' . $property->insight_width . 'm';
                            $comparePropertyTurningTime = $property->comparePropertyTurningTime;
                            $position[] = (isset($detail->positionType->description) && isset($detail->landTypePurposeData->description)) ? ($this->mb_ucfirst(mb_strtolower($detail->landTypePurposeData->description)) . ' - ' . $this->mb_ucfirst(mb_strtolower($detail->positionType->description))) : '';
                        }
                    }
                } elseif ($unfrontSides) {
                    $frontSide = 'Trong hẻm';
                    foreach ($unfrontSides as $property) {
                        foreach ($property->propertyDetail as $detail) {
                            $landTypePurpose[] = (isset($detail->landTypePurposeData->description) ? $this->mb_ucfirst(mb_strtolower($detail->landTypePurposeData->description)) : '') . ' (' . $detail->total_area . $m2 . ')';
                            $landType = (isset($property->landType->description) ? $this->mb_ucfirst(mb_strtolower($property->landType->description)) : '');
                            $legal = (isset($property->legal->description) ? $this->mb_ucfirst(mb_strtolower($property->legal->description)) : '');
                            $landShape = (isset($property->landShape->description) ? $this->mb_ucfirst(mb_strtolower($property->landShape->description)) : '');
                            $size = $property->front_side_width . 'm' . 'x' . $property->insight_width . 'm';
                            $comparePropertyTurningTime = $property->comparePropertyTurningTime;
                            $position[] = (isset($detail->positionType->description) && isset($detail->landTypePurposeData->description)) ? ($this->mb_ucfirst(mb_strtolower($detail->landTypePurposeData->description)) . ' - ' . $this->mb_ucfirst(mb_strtolower($detail->positionType->description))) : '';

                        }
                    }
                }
                $table = $section->addTable('areaTable');
                $table->addRow(250);
                $cell = $table->addCell(4000);
                $cellRun = $cell->addTextRun();
                $cellRun->addText('- Diện tích: ');
                $cellRun->addText(number_format($object->total_area, 2, ',' , '.'), ['bold' => true]);
                $cellRun->addText($m2);
                $cell = $table->addCell(5000);
                $cellRun = $cell->addTextRun();
                $cellRun->addText('Loại đất: ');
                $cellRun->addText($landType, ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Mục đích sử dụng: ');
                $textRun->addText(implode(', ', $landTypePurpose), ['bold' => true]);


                $table = $section->addTable('coordinatesTable');
                $table->addRow(250);
                $cell = $table->addCell(4000);
                $textRun = $cell->addTextRun();
                $textRun->addText('- Giấy chứng nhận QSDĐ: ');
                $textRun->addText($legal, ['bold' => true]);
                $cell = $table->addCell(5000);
                $textRun = $cell->addTextRun();
                $textRun->addText('Hình dáng: ');
                $textRun->addText($landShape, ['bold' => true]);

                $table = $section->addTable('coordinatesTable');
                $table->addRow(250);
                $cell = $table->addCell(4000);
                $textRun = $cell->addTextRun();
                $textRun->addText('- Kích thước: ');
                $textRun->addText($size, ['bold' => true]);
                $cell = $table->addCell(5000);
                $textRun = $cell->addTextRun();
                $textRun->addText('Địa hình: ');;
                $textRun->addText($topographic ? ($this->mb_ucfirst(mb_strtolower($topographic))) : '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Tên đường (phố): ');
                $textRun->addText($object->street->name ?? '', ['bold' => true]);

                $textRun = $section->addTextRun();
                $textRun->addText('- Đoạn đường (từ đâu đến đâu): ');
                $textRun->addText($object->distance->name ?? '', ['bold' => true]);

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
                        $table->addCell(3000)->addText( $turning->turning, null, ['align' => JcTable::CENTER]);
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
                    // $textRun = $section->addTextRun();
                    $table = $section->addTable('docTable');
                    $table->addRow(250);
                    $cell = $table->addCell(4000);
                    $textRun = $cell->addTextRun();
                    $textRun->addText('- Loại nhà: ');
                    $textRun->addText((isset($tangibleAsset->buildingType->description) ? $this->mb_ucfirst(mb_strtolower($tangibleAsset->buildingType->description)) : ''), ['bold' => true]);
                    $cell = $table->addCell(2500);
                    $textRun = $cell->addTextRun();
                    $textRun->addText('Cấp nhà: ');
                    $textRun->addText((isset($tangibleAsset->buildingCategory->description) ? htmlspecialchars($tangibleAsset->buildingCategory->description) : ''), ['bold' => true]);
                    $cell = $table->addCell(2500);
                    $textRun = $cell->addTextRun();
                    $textRun->addText("Số tầng: ");
                    $textRun->addText($tangibleAsset->floor, ['bold' => true]);

                    if ($tangibleAsset->other_building){
                        $table = $section->addTable('docTable');
                        $table->addRow(250);
                        $cell = $table->addCell(4000);
                        $textRun = $cell->addTextRun();
                        $textRun->addText('- Tên công trình: ');
                        $textRun->addText($tangibleAsset->other_building, ['bold' => true]);
                        $cell = $table->addCell(5000);
                        $textRun = $cell->addTextRun();
                        $textRun->addText(" Mô tả: ");
                        $textRun->addText($tangibleAsset->description, ['bold' => true]);
                    }

                    $table = $section->addTable('coordinatesTable');
                    $table->addRow(250);
                    $cell = $table->addCell(4000);
                    $textRun = $cell->addTextRun();
                    $textRun->addText('- Năm xây dựng: ');
                    $textRun->addText($tangibleAsset->start_using_year, ['bold' => true]);
                    $cell = $table->addCell(5000);
                    $textRun = $cell->addTextRun();
                    $textRun->addText("Chất lượng còn lại: ");
                    $textRun->addText($tangibleAsset->remaining_quality, ['bold' => true]);
                    $textRun->addText('%');

                    $table = $section->addTable('coordinatesTable');
                    $table->addRow(250);
                    $cell = $table->addCell(4000);
                    $textRun = $cell->addTextRun();
                    $textRun->addText('- Diện tích sàn xây dựng: ');
                    $textRun->addText(number_format($tangibleAsset->total_construction_base, 2, ',', '.'), ['bold' => true]);
                    $textRun->addText(" ".$m2);
                    $cell = $table->addCell(5000);
                    $textRun = $cell->addTextRun();
                    $gpxd = $tangibleAsset->gpxd ? 'Có' : 'Không';
                    $textRun->addText('Giấy phép xây dựng: ');
                    $textRun->addText($gpxd, ['bold' => true]);
                    $count += count($tangibleAsset->pic);
                }
                if ($object->otherAssets && $object->otherAssets->count() > 0) {
                    $section->addText('2.2. Tài sản khác: ', ['bold' => true]);
                    foreach ($object->otherAssets as $otherAssets) {

                        $table = $section->addTable('coordinatesTable');
                        $table->addRow(250);
                        $cell = $table->addCell(4000);
                        $textRun->addText('- Loại tài sản khác: ');
                        $textRun->addText(isset($otherAssets->other_asset) ? $this->mb_ucfirst(mb_strtolower($otherAssets->other_asset)) : '', ['bold' => true]);
                        $cell = $table->addCell(5000);
                        $textRun = $cell->addTextRun();
                        $textRun->addText("Giá trị: ");
                        $textRun->addText(number_format($otherAssets->total_amount, 0, ',', '.'), ['bold' => true]);
                        $textRun->addText(' đồng');
                        $count += count($otherAssets->pic);


                    }
                } else {
                    $textRun = $section->addTextRun();
                    $textRun->addText('2.2. Tài sản khác: ', ['bold' => true]);
                    $textRun->addText('Không có');
                }
            }
            $count += count($object->pic);
            if (isset($object->assetType->description) && ($object->assetType->description == 'CHUNG CƯ')) {
                $textRun = $section->addTextRun();
                $textRun->addText('4. Hình ảnh hiện trạng chung cư: ', ['bold' => true]);
            } else {
                $textRun = $section->addTextRun();
                $textRun->addText('3. Hình ảnh hiện trạng đất, nhà: ', ['bold' => true]);
            }
            if ($count > 0) {
                $textRun->addText('Có (chi tiết xem trên hệ thống phần mềm lưu trữ kho giá)');
            }
            else {
                $textRun->addText('Không có');
            }
            $section->addTextBreak(1);
            $cellWidth = 5000;
            $table3 = $section->addTable($tableBasicStyle);
            $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
            $table3->addCell($cellWidth)->addText("", null, ['keepNext' => true]);
            $cell2 = $table3->addCell($cellWidth);
            $cell2->addText('Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y'), ['italic' => true], ['align' => 'center', 'keepNext' => true]);
            $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
            $cell3 = $table3->addCell($cellWidth);
            $cell3->addText("Chuyên viên thẩm định", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            $cell4 = $table3->addCell($cellWidth);
            $cell4->addText("Thẩm định viên", ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            if (!empty($certificate)) {
                $table3->addRow(Converter::inchToTwip(1.5), $cantSplit);
                $table3->addCell($cellWidth)->addText("", null, ['keepNext' => true]);
                $table3->addCell($cellWidth)->addText("", null, ['keepNext' => true]);;
                $table3->addRow(Converter::inchToTwip(.1), $cantSplit);
                $cell5 = $table3->addCell($cellWidth);
                $cell5->addText($certificate->appraiserPerform->name ?? '', ['bold' => true], ['align' => 'center', 'keepNext' => true]);
                $cell6 = $table3->addCell($cellWidth);
                $cell6->addText($certificate->appraiser->name ?? '', ['bold' => true], ['align' => 'center', 'keepNext' => true]);
            }
        }

        try {

            $comName = mb_strtoupper($company->acronym ? $company->acronym : env('DOCUMENT_MODULE'));
            if(!empty($certificateId)) {
                $reportID = 'HSTD_'. $certificateId;
                $reportUserName = CommonService::getUserReport();
		        $reportName = '6_TSSS' . '_' . $reportUserName . '_' . $reportID . '_' . $comName;
                $downloadDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('dmY');
                $downloadTime = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('Hi');
                $fileName = $reportName . '_' . $downloadTime . '_' . $downloadDate;
            } else {
                $fileName = $comName . '_TSSS_'. $object->id .'_'. Carbon::now()->format('Y_m_d');
            }

            $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
            $path =  env('STORAGE_DOCUMENTS') . '/'. 'comparison_assets/' . $now->format('Y') . '/' . $now->format('m') . '/';
            if(!File::exists(storage_path('app/public/'. $path))){
                File::makeDirectory(storage_path('app/public/'. $path), 0755, true);
            }
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save(storage_path('app/public/'. $path. $fileName .'.docx'));

        } catch (\Exception $e) {
            throw $e;
        }
        $data = [];
        if ($format =='pdf'){
            shell_exec('export HOME=/tmp/ ; libreoffice --headless --convert-to '. $format . ' ' . storage_path('app/public/'. $path. $fileName .'.docx') . ' --outdir ' . storage_path('app/public/'. $path));
        }
        $data['url'] = Storage::disk('public')->url($path . $fileName .'.' . $format);
        $data['file_name'] = $fileName;
        return $data;
    }

    function mb_ucfirst($string, $encoding = "utf8")
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
}
