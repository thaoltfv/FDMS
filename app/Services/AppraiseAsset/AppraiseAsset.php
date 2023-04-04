<?php

namespace App\Services\AppraiseAsset;

use App\Contracts\AppraiseAssetRepository;
use App\Contracts\DictionaryRepository;
use App\Http\ResponseTrait;


class AppraiseAsset
{
    use ResponseTrait;

    private DictionaryRepository $dictionaryRepository;
    private AppraiseAssetRepository $appraiseAssetRepository;


    /**
     * FirebaseClient constructor.
     */
    public function __construct(AppraiseAssetRepository $appraiseAssetRepository,
                                DictionaryRepository    $dictionaryRepository)
    {
        $this->appraiseAssetRepository = $appraiseAssetRepository;
        $this->dictionaryRepository = $dictionaryRepository;
    }


    /**
     * @param $request
     * @return array
     */
    public function AppraiseAsset($request): array
    {
        $result = [];
        if ($request['front_side'] == 1) {
            $unrecognized = ($this->appraiseAssetRepository->estimateUnrecognizedFrontSiteAsset($request));
        } else {
            $unrecognized = $this->appraiseAssetRepository->estimateUnrecognizedUnfrontSiteAsset($request);
        }
        if (isset($unrecognized)) {
            $result['assets'] = $unrecognized['assets'] ?? null;
            $result['status'] = $unrecognized['status'] ?? null;
            $result['error_message'] = $unrecognized['error_message'] ?? null;
            $result['steps'] = $unrecognized['steps'] ?? null;
        }
        return $result;
    }

}
