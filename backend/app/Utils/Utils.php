<?php
namespace App\Utils;

class Utils{
    /**
     * @param $teacher_gender
     * @param $data
     * @return array
     */
    public static function generateResource($teacher_gender, $data): array
    {
        $array = array();
        $index = 0;
        foreach ($data as $lesson_detail) {
            $photo_bg = [
                "res_id" => '#res:00'.$index,
                'type' => 'photo',
                "x" => 0,
                "y" => 0,
                "width" => 852,
                "height" => 480,
                "uri" => env('RENDER_URL')."/static/sample/photo/bg.png",
            ];
            array_push($array, $photo_bg);
            $index++;
            $photo = [
                'res_id' => '#res:00'.$index,
                'type' => 'photo',
                'uri' => isset($lesson_detail->file_path) ? 's3://gumi/'.$lesson_detail->file_path : 's3://gumi/images/originals/empty_table.png',
                "x" => 247,
                "y"=> 65,
                "width"=> 531,
                "height"=> 265,
                "order"=> 0,
            ];
            array_push($array, $photo);
            $index++;
            $text = [
                'res_id' => '#res:00'.$index,
                'type' => 'text',
                "x" => 242,
                "y"=> 381,
                "width"=> 552,
                "height"=> 88,
                "order"=> 0,
                'value' => $lesson_detail->movie_name,
                'font-size' => 20,
            ];
            array_push($array, $text);
            $index++;
            $animation_id = $lesson_detail->animation_id - 1;
            if($teacher_gender == 2) {
                $user = 'man';
            } else {
                $user = 'woman';
            }
            $anim = [
                'res_id' => '#res:00'.$index,
                'type' => 'anim',
                "x" => 0,
                "y"=> 0,
                "width"=> 852,
                "height"=> 480,
                "order"=> 0,
                'value' => $user.$animation_id
            ];
            array_push($array, $anim);
            $index++;
        }
        return $array;
    }

    /**
     * @param $teacher_gender
     * @param $resources
     * @param $data
     * @return array
     */
    public static function generateSegments($teacher_gender, $resources, $data): array
    {
        if($teacher_gender == 2) {
            $user = 'man';
            $type = 0;
        } else {
            $user = 'woman';
            $type = 1;
        }
        $array_segments = array();
        $index = 0;
        $resource_compares = [];
        $count = sizeof($resources)/4;
        for ($i = 1; $i <= $count + 1; $i++) {
            $array = array_slice($resources, $index, 4);
            array_push($resource_compares, $array);
            $index = ($i)*4;
        }
        $index_seg = 0;
        foreach ($data as $detail) {
            $item = [
                'id' => '#seg:000'.$index_seg,
                'interval' => (int)$detail->interval,
                'voice' => ['text' => $detail->movie_name, 'type' => $type],
                'resources' => $resource_compares[$index_seg]
            ];
            array_push($array_segments, $item);
            $index_seg++;
        }
        return $array_segments;
    }

    /**
     * @param $teacher_gender
     * @param $data
     * @return array
     */
    public static function generateResourceReview($teacher_gender, $data): array
    {
        $array = array();
        $index = 0;
        foreach ($data as $lesson_detail) {
            $photo_bg = [
                "res_id"=> '#res:00'.$index,
                "type" => 'photo',
                "x" => 0,
                "y" => 0,
                "width" => 426,
                "height" => 240,
                "uri" => env('RENDER_URL')."/static/sample/photo/bg_small.png",
            ];
            array_push($array, $photo_bg);
            $index++;
            $photo = [
                'res_id' => '#res:00'.$index,
                'type' => 'photo',
                'uri' => isset($lesson_detail['file_path']) ? 's3://gumi/'.$lesson_detail['file_path'] : 's3://gumi/images/originals/empty_table.png',
                "x" => 123,
                "y"=> 32,
                "width"=> 265,
                "height"=> 133,
                "order"=> 0,
            ];
            array_push($array, $photo);
            $index++;
            $text = [
                'res_id' => '#res:00'.$index,
                'type' => 'text',
                "x" => 121,
                "y"=> 190,
                "width"=> 276,
                "height"=> 44,
                "order"=> 0,
                'value' => $lesson_detail['movie_name'],
                'font-size' => 10,
            ];
            array_push($array, $text);
            $index++;
            $animation_id = $lesson_detail['animation_id'] - 1;
            if($teacher_gender == 2) {
                $user = 'man';
            } else {
                $user = 'woman';
            }
            $anim = [
                'res_id' => '#res:00'.$index,
                'type' => 'anim',
                "x" => 0,
                "y"=> 0,
                "width"=> 426,
                "height"=> 240,
                "order"=> 0,
                'value' => $user.$animation_id
            ];
            array_push($array, $anim);
            $index++;
        }
        return $array;
    }

    /**
     * @param $teacher_gender
     * @param $resources
     * @param $data
     * @return array
     */
    public static function generateSegmentsReview($teacher_gender, $resources, $data): array
    {
        $array_segments = array();
        $index = 0;
        $resource_compares = [];
        if($teacher_gender == 2) {
            $type = 0;
        } else {
            $type = 1;
        }
        $count = sizeof($resources) / 4;
        for ($i = 1; $i <= $count + 1; $i++) {
            $array = array_slice($resources, $index, 4);
            array_push($resource_compares, $array);
            $index = ($i)*4;
        }
        $index_seg = 0;
        foreach ($data as $detail) {
            $item = [
                'id' => '#seg:000'.$index_seg,
                'interval' => (int)$detail['interval'],
                'voice' => ['text' => $detail['movie_name'], 'type' => $type],
                'resources' => $resource_compares[$index_seg]
            ];
            array_push($array_segments, $item);
            $index_seg++;
        }
        return $array_segments;
    }

    /**
     * @param $resources
     * @param $segments
     * @return array
     */
    public static function createArrayNew($resources, $segments): array
    {
        return [
            'callback_url' => env('CALLBACK_URL').'/callback',
            'id' => '#video:000',
            'size' => [
                'height' => 480,
                'width' => 852,
            ],
            'fps' => 30,
            'all_resources' => $resources,
            'segments' => $segments
        ];
    }

    /**
     * @param $resources
     * @param $segments
     * @return array
     */
    public static function createArrayReview($resources, $segments): array
    {
        return [
            'callback_url' => env('CALLBACK_URL').'/callback',
            'id' => '#video:000',
            'size' => [
                'height' => 240,
                'width' => 426,
            ],
            'fps' => 30,
            'all_resources' => $resources,
            'segments' => $segments
        ];
    }
}
