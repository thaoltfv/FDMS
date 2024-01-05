<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Address\AddressController;
use App\Http\Controllers\Address\AddressLogController;
use App\Http\Controllers\Address\DistanceController;
use App\Http\Controllers\Address\DistrictController;
use App\Http\Controllers\Address\ProvinceController;
use App\Http\Controllers\Address\StreetController;
use App\Http\Controllers\Address\WardController;
use App\Http\Controllers\Apartment\ApartmentAssetController;
use App\Http\Controllers\Apartment\ApartmentController;
use App\Http\Controllers\Apartment\BlockListController;
use App\Http\Controllers\Apartment\ProjectController;
use App\Http\Controllers\AppraiseDictionary\AppraisalConstructionCompanyController;
use App\Http\Controllers\AppraiseDictionary\AppraiseCompanyController;
use App\Http\Controllers\AppraiseDictionary\AppraiseController;
use App\Http\Controllers\AppraiseDictionary\AppraiseDictionaryController;
use App\Http\Controllers\AppraiseDictionary\AppraiseLawDocumentController;
use App\Http\Controllers\AppraiseDictionary\AppraiseOtherInformationController;
use App\Http\Controllers\AppraiseDictionary\AppraiserController;
use App\Http\Controllers\AppraiseDictionary\AppraiseVersionController;
use App\Http\Controllers\AppraiseDictionary\CertificateAssetController;
use App\Http\Controllers\AppraiseDictionary\CertificateAssetVersionController;
use App\Http\Controllers\AppraiseDictionary\CertificateController;
use App\Http\Controllers\Asset\AssetVersionController;
use App\Http\Controllers\Asset\CompareAssetGeneralController;
use App\Http\Controllers\Asset\EstimatePriceLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Branch\BranchController;
use App\Http\Controllers\BuildingPrice\BuildingPriceController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Dictionary\DictionaryController;
use App\Http\Controllers\Intergration\CertificateAssetsController;
use App\Http\Controllers\Intergration\CertificateBriefController;
use App\Http\Controllers\Intergration\RealEstateController;
use App\Http\Controllers\PersonalProperty\MachineCertificateAssetController;
use App\Http\Controllers\MigrateData\ImportDataController;
use App\Http\Controllers\MigrateData\MigrateDataController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersonalProperty\OtherCertificateAssetController;
use App\Http\Controllers\PersonalProperty\PersonalPropertiesController;
use App\Http\Controllers\PersonalProperty\TechnologicalLineCertificateAssetController;
use App\Http\Controllers\Report\CertificateBriefReportController;
use App\Http\Controllers\Report\ComparisonAssetController;
use App\Http\Controllers\UnitPrice\UnitPriceController;
use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\UtilController;
use App\Http\Controllers\PersonalProperty\VerhicleCertificateAssetController;
use App\Http\Controllers\Report\CertificateAssetReportController;
use App\Http\Controllers\Workflow\WorkflowController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AppraiseDictionary\PreCertificateController;
use App\Http\Controllers\PreCertificateConfig\PreCertificateConfigController;
// use App\Http\Controllers\PreCertificateConfig\PreCertificateBriefController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

###################
# GUEST
###################

Route::group(['middleware' => 'guest'], function () {
    Route::post('auth/login', [LoginController::class, 'login']);

    Route::post('auth/refresh', [LoginController::class, 'refreshToken']);

    Route::get('ping', [UtilController::class, 'serverTime']);

    Route::get('run/fix', [UtilController::class, 'runFix']);

    Route::post('notification/send', [NotificationController::class, 'sendNotification']);
});

###################
# JUST AUTH
###################

Route::group(['middleware' => 'auth'], function () {

    Route::post('auth/logout', [LoginController::class, 'logout']);

    Route::post('users/change-password', [UserController::class, 'changeUserPassword']);

    Route::post('users/reset-password/{id}', [UserController::class, 'resetUserPasswordNew']);
    
    Route::post('users/deactive-user/{id}', [UserController::class, 'deactiveUser']);

    Route::post('users/active-user/{id}', [UserController::class, 'activeUser']);

    Route::post('users/isnt-legal-user/{id}', [UserController::class, 'isntLegalUser']);

    Route::post('users/is-legal-user/{id}', [UserController::class, 'isLegalUser']);

    Route::apiResource('user', UserController::class);

    Route::get('/users', [UserController::class, 'findAll']);

    Route::post('/store-users', [UserController::class, 'storeUserInExcel']);

    Route::apiResource('branch', BranchController::class);

    Route::get('/branches', [BranchController::class, 'findAll']);

    Route::get('/profile/{id}', [UserController::class, 'profile']);

    Route::get('/notifications/{id}', [UserController::class, 'notifications']);

    Route::post('/notification/all', [NotificationController::class, 'markAllAsRead']);

    Route::post('/notification', [NotificationController::class, 'markAsRead']);

    Route::apiResource('role', RoleController::class);

    Route::get('/roles', [RoleController::class, 'findAll']);

    Route::apiResource('permission', PermissionController::class);

    Route::get('/permissions/screen', [PermissionController::class, 'getScreen']);

    Route::apiResource('province', ProvinceController::class);

    Route::get('/provinces', [ProvinceController::class, 'findAll']);

    Route::apiResource('district', DistrictController::class);

    Route::get('/districts', [DistrictController::class, 'findAll']);

    Route::apiResource('ward', WardController::class);

    Route::get('/wards', [WardController::class, 'findAll']);

    Route::apiResource('street', StreetController::class);

    Route::get('/streets', [StreetController::class, 'findAll']);

    Route::apiResource('distance', DistanceController::class);

    Route::get('/distances', [DistanceController::class, 'findAll']);

    Route::apiResource('dictionary', DictionaryController::class);

    Route::get('/dictionaries', [DictionaryController::class, 'findAll']);

    Route::get('/dictionaries/all/{type}', [DictionaryController::class, 'findAllByType']);

    Route::apiResource('asset-general', CompareAssetGeneralController::class);

    Route::get('/asset-generals', [CompareAssetGeneralController::class, 'findAll']);

    Route::get('/asset-generals/search', [CompareAssetGeneralController::class, 'findAllInElastic']);

    Route::post('/asset-generals/image', [CompareAssetGeneralController::class, 'uploadImage']);

    Route::put('/asset-generals/update-status/{id}', [CompareAssetGeneralController::class, 'updateStatus']);

    Route::get('/asset-generals/print/{id}', [CompareAssetGeneralController::class, 'print']);

    Route::post('/asset-generals/estimate-price', [CompareAssetGeneralController::class, 'estimatePrice']);

    Route::get('/asset-generals/create-index', [CompareAssetGeneralController::class, 'createIndex']);

    Route::get('/asset-generals/version/{id}', [CompareAssetGeneralController::class, 'findVersionById']);

    Route::get('/asset-generals/asset-export', [CompareAssetGeneralController::class, 'assetExport']);

    Route::apiResource('apartment', ApartmentController::class);

    Route::get('/apartments', [ApartmentController::class, 'findAll']);

    Route::post('/store-apartments', [ApartmentController::class, 'storeApartmentInExcel']);

    Route::apiResource('block-list', BlockListController::class);

    Route::get('/block-lists', [BlockListController::class, 'findAll']);

    Route::apiResource('building-price', BuildingPriceController::class);

    Route::get('/building-prices', [BuildingPriceController::class, 'findAll']);

    Route::get('/building-prices/average-building-price', [BuildingPriceController::class, 'getAverageBuildPriceNew']);

    Route::apiResource('unit-price', UnitPriceController::class);

    Route::get('get/unit-price/{id}', [UnitPriceController::class, 'findById']);

    Route::get('/unit-prices', [UnitPriceController::class, 'findAll']);

    Route::get('/migrate/list', [MigrateDataController::class, 'list']);

    Route::get('/migrate/async-data', [MigrateDataController::class, 'asyncDonavaToDb']);

    Route::get('/migrate/async-elastic', [MigrateDataController::class, 'asyncDonavaToElastic']);

    Route::get('/migrate/async-s3', [MigrateDataController::class, 'asyncImageToS3']);

    Route::get('/migrate/async-migrate-status', [MigrateDataController::class, 'updateMigrateStatus']);

    Route::get('/migrate/createIndex', [MigrateDataController::class, 'createIndex']);

    Route::get('/import/street', [ImportDataController::class, 'asyncImportStreet']);

    Route::get('/import/distance', [ImportDataController::class, 'asyncImportDistance']);

    Route::get('/import/unit-price', [ImportDataController::class, 'asyncImportUnitPrice']);

    Route::post('/address/find', [AddressController::class, 'findAddress']);

    Route::post('/address/find/street', [AddressController::class, 'findStreet']);

    Route::apiResource('estimate-log', EstimatePriceLogController::class);

    Route::get('/get-estimate-log/{id}', [EstimatePriceLogController::class, 'getLog']);

    Route::get('/estimate-logs', [EstimatePriceLogController::class, 'findAll']);

    Route::post('/estimate-logs', [EstimatePriceLogController::class, 'create']);

    Route::get('/estimate-logs/create-index', [EstimatePriceLogController::class, 'createIndex']);

    Route::apiResource('customer', CustomerController::class);

    Route::get('/customers', [CustomerController::class, 'findAll']);

    Route::post('/customers/status', [CustomerController::class, 'updateCustomersStatus']);

    Route::apiResource('appraise/dictionary', AppraiseDictionaryController::class);

    Route::get('appraise/dictionaries', [AppraiseDictionaryController::class, 'findAll']);

    Route::apiResource('appraiser', AppraiserController::class);

    Route::get('appraisers', [AppraiserController::class, 'findAll']);

    Route::apiResource('appraiser-company', AppraiseCompanyController::class);

    Route::get('appraiser-companies', [AppraiseCompanyController::class, 'findAll']);

    Route::apiResource('appraisal-construction', AppraisalConstructionCompanyController::class);

    Route::get('appraisal-constructions', [AppraisalConstructionCompanyController::class, 'findAll']);

    Route::apiResource('appraise-other', AppraiseOtherInformationController::class);

    Route::get('appraise-others', [AppraiseOtherInformationController::class, 'findAll']);

    Route::apiResource('appraise-law', AppraiseLawDocumentController::class);

    Route::get('appraise-laws', [AppraiseLawDocumentController::class, 'findAll']);

    Route::apiResource('address-log', AddressLogController::class);

    Route::get('address-logs', [AddressLogController::class, 'findAll']);

    Route::apiResource('appraise', AppraiseController::class);

    Route::get('appraises', [AppraiseController::class, 'findAll']);

    Route::post('appraises/comparison-factor', [AppraiseController::class, 'updateComparisonFactor']);

    Route::post('appraises/bao-cao/edit/{id}', [AppraiseController::class, 'updateBaoCao']);

    Route::post('/appraises/tangible-comparison', [AppraiseController::class, 'updateTangibleComparisonFactor']);

    Route::get('appraises/ids/{ids}', [AppraiseController::class, 'findByIds']);

    Route::post('appraises/asset', [AppraiseController::class, 'appraiseAsset']);

    Route::get('/appraises/print/phu-luc-1/{id}', [AppraiseController::class, 'printPL1']);

    Route::get('/appraises/print/phu-luc-2/{id}', [AppraiseController::class, 'printPL2']);

    Route::get('/appraises/print/phu-luc-hinh-anh/{id}', [AppraiseController::class, 'printPhuLucHinhAnh']);

    Route::get('/appraises/print/chung-thu/{id}', [AppraiseController::class, 'printChungThu']);

    Route::get('/appraises/print/bao-cao/{id}', [AppraiseController::class, 'printBaoCao']);

    Route::post('/appraises/image', [AppraiseController::class, 'uploadImage']);

    Route::apiResource('asset/version', AssetVersionController::class);

    Route::apiResource('appraise/version', AppraiseVersionController::class);

    Route::get('/appraises/create-index', [AppraiseVersionController::class, 'createIndex']);

    Route::get('/appraises/comparison-factor/{id}', [AppraiseController::class, 'getComparisonFactor']);

    Route::apiResource('/certificate', CertificateController::class);


    Route::post('appraise/status/{id}', [AppraiseController::class, 'updateStatus']);

    Route::post('certificate/status/{id}', [CertificateController::class, 'updateStatus']);

    Route::post('certificate/other-document/upload/{id}', [CertificateController::class, 'otherDocumentUpload']);

    //test

    Route::post('certificate/test-document/upload', [CertificateController::class, 'testDocumentUpload']);

    //test

    Route::post('certificate/other-document/remove/{id}', [CertificateController::class, 'otherDocumentRemove']);

    Route::get('certificate/other-document/download/{id}', [CertificateController::class, 'otherDocumentDownload']);

    Route::get('/certificates', [CertificateController::class, 'findAll']);

    Route::apiResource('/pre-certificate', PreCertificateController::class);
    
    Route::post('pre-certificates/status/{id}', [PreCertificateController::class, 'updateStatus']);
    Route::get('pre-certificates/pre-certificate-paging', [PreCertificateController::class, 'findPaging']);

    // Route::post('pre-certificate/pre-certification-brief/{id?}', [PreCertificateBriefController::class, 'postGeneralInfomation']);
    // Route::get('pre-certificates/brief-export', [PreCertificateController::class, 'exportCertificateBriefs']);
    Route::get('pre-certificates/pre-certificate-workflow', [PreCertificateController::class, 'getPreCertificateWorkFlow']);
    Route::post('pre-certificates/pre-certification-brief/{id?}', [PreCertificateController::class, 'postGeneralInfomation']);
    Route::get('pre-certificates/pre-certification-infomation/{id}', [PreCertificateController::class, 'getPreCertificate']);

    Route::post('pre-certificates/pre-certificate-update-status/{id}', [PreCertificateController::class, 'updateStatus']);
    Route::post('pre-certificates/pre-certificate-update-offical/{id}', [PreCertificateController::class, 'updateToOffical']);
    Route::post('pre-certificates/other-document/upload/{id}/{typeDocument}', [PreCertificateController::class, 'otherDocumentUpload']);

    Route::post('pre-certificates/other-document/remove/{id}', [PreCertificateController::class, 'otherDocumentRemove']);

    Route::get('pre-certificates/other-document/download/{id}', [PreCertificateController::class, 'otherDocumentDownload']);

    Route::get('/pre-certificates', [PreCertificateController::class, 'findAll']);

    Route::apiResource('pre-certificate-config', PreCertificateConfigController::class);

    Route::get('/pre-certificate-configs', [PreCertificateConfigController::class, 'findAll']);

    Route::get('certificate-assets', [CertificateAssetController::class, 'findAll']);

    Route::apiResource('certificate-asset', CertificateAssetController::class);

    Route::prefix('certificate-asset')->group(function () {

        Route::post('comparison-factor', [CertificateAssetController::class, 'updateComparisonFactor']);

        Route::post('tangible-comparison', [CertificateAssetController::class, 'updateTangibleComparisonFactor']);

        Route::get('ids/{ids}', [CertificateAssetController::class, 'findByIds']);

        Route::post('asset', [CertificateAssetController::class, 'appraiseAsset']);

        Route::get('print/phu-luc-1/{id}', [CertificateAssetController::class, 'printPL1']);

        Route::get('print/phu-luc-2/{id}', [CertificateAssetController::class, 'printPL2']);

        Route::get('print/phu-luc-hinh-anh/{id}', [CertificateAssetController::class, 'printPhuLucHinhAnh']);

        Route::get('print/chung-thu/{id}', [CertificateAssetController::class, 'printChungThu']);

        Route::get('print/bao-cao/{id}', [CertificateAssetController::class, 'printBaoCao']);

        Route::get('print/test1/bao-cao/{id}', [CertificateAssetController::class, 'printBaoCaoTest1']);
        Route::get('print/test2/bao-cao/{id}', [CertificateAssetController::class, 'printBaoCaoTest2']);

        Route::post('image', [CertificateAssetController::class, 'uploadImage']);

        Route::get('comparison-factor/{id}', [CertificateAssetController::class, 'getComparisonFactor']);

        Route::apiResource('version', CertificateAssetVersionController::class);

        Route::get('create-index', [CertificateAssetVersionController::class, 'createIndex']);

        Route::get('/asset-generals', [CompareAssetGeneralController::class, 'findAll']);

        Route::get('/asset-generals/search', [CompareAssetGeneralController::class, 'findAllInElastic']);

        Route::post('/asset-generals/image', [CompareAssetGeneralController::class, 'uploadImage']);

        Route::put('/asset-generals/update-status/{id}', [CompareAssetGeneralController::class, 'updateStatus']);

        Route::get('/asset-generals/print/{id}', [CompareAssetGeneralController::class, 'print']);

        Route::post('/asset-generals/estimate-price', [CompareAssetGeneralController::class, 'estimatePrice']);

        Route::get('/asset-generals/create-index', [CompareAssetGeneralController::class, 'createIndex']);

        Route::get('/asset-generals/version/{id}', [CompareAssetGeneralController::class, 'findVersionById']);

    });

    Route::get('workflow/getworkflow', [WorkflowController::class, 'getWorkflow']);
    Route::get('workflow/certificate-workflow', [WorkflowController::class, 'getCertificateWorkFlow']);

    // Appraise Intergration
    Route::get('certification_asset/step/{id?}', [CertificateAssetsController::class, 'getAppraiseStep']);

    //Step 1
    Route::get('certification_asset/general-infomation/{id}', [CertificateAssetsController::class, 'getGeneralInfomation']);
    Route::post('certification_asset/step1-general-infomation/{id?}', [CertificateAssetsController::class, 'postGeneralInfomation']);
    //Step 2
    Route::get('certification_asset/land-infomation/{id}', [CertificateAssetsController::class, 'getLandInfomation']);
    Route::post('certification_asset/step2-land-infomation/{id}', [CertificateAssetsController::class, 'postLandDetailInfomation']);
    //Step 3
    Route::get('certification_asset/construction-infomation/{id}', [CertificateAssetsController::class, 'getConstruction']);
    Route::post('certification_asset/step3-construction-infomation/{id}', [CertificateAssetsController::class, 'postConstructionInfomation']);
    //Step 4
    Route::get('certification_asset/law-infomation/{id}', [CertificateAssetsController::class, 'getLaw']);
    Route::post('certification_asset/step4-law-infomation/{id}', [CertificateAssetsController::class, 'postLawInfomation']);
    //Step 5
    Route::get('certification_asset/appraisal-infomation/{id}', [CertificateAssetsController::class, 'getAppraisalFacility']);
    Route::post('certification_asset/step5-appraisal-infomation/{id}', [CertificateAssetsController::class, 'postAppraisalFacility']);
    //step 6
    Route::get('certification_asset/assets-infomation/{id}', [CertificateAssetsController::class, 'getAssets']);
    Route::get('certification_asset/assets-automatic/{id}', [CertificateAssetsController::class, 'getAssetsAutomatic']);
    Route::post('certification_asset/assets-version-by-id', [CertificateAssetsController::class, 'getAssetVersionById']);
    Route::post('certification_asset/step6-assets-infomation/{id}', [CertificateAssetsController::class, 'postAssets']);
    Route::get('certification_asset/assets-search', [CertificateAssetsController::class, 'findAllInElastic']);

    //Step 7
    Route::get('certification_asset/appraise-calculate-infomation/{id}', [CertificateAssetsController::class, 'getAppraiseFinallData']);
    Route::get('certification_asset/appraise-infomation/{id}', [CertificateAssetsController::class, 'getAppraiseData']);
    Route::get('certification_asset/appraise-all-step/{id}', [CertificateAssetsController::class, 'getAppraiseDataStepOneToSix']);
    Route::post('certification_asset/step7-other-asset/{id}', [CertificateAssetsController::class, 'postOtherAssets']);
    Route::post('certification_asset/step7-comparison-factor/{id}', [CertificateAssetsController::class, 'updateComparisonFactor_V2']);
    Route::post('certification_asset/step7-comparison-factor-ver1/{id}', [CertificateAssetsController::class, 'updateComparisonFactor_V2_ver1']);
    Route::get('certification_asset/appraise-paging', [CertificateAssetsController::class, 'findPaging']);
    Route::post('certification_asset/step7-construction-company/{id}', [CertificateAssetsController::class, 'updateConstructionCompany']);
    Route::post('certification_asset/step7-round-appraise-total/{id}', [CertificateAssetsController::class, 'updateRoundAppraiseTotal']);
    Route::post('certification_asset/step7-tangible-comparison/{id}', [CertificateAssetsController::class, 'updateConstructionComparison']);
    Route::get('certification_asset/appraise-export', [RealEstateController::class, 'exportCertificateAssets']);
    Route::post('certification_asset/update-distance/{id?}', [CertificateAssetsController::class, 'updateDistance']);
    Route::post('certification_asset/update-mucdichchinh/{id?}', [CertificateAssetsController::class, 'updateMucdichchinh']);
    Route::post('certification_asset/update-note-hientrang/{id?}', [CertificateAssetsController::class, 'updateNoteHienTrang']);

    // Route::post('appraise-intergration/assets-version-by-id', [CertificateAssetsController::class, 'getAssetVersionById']);

    // Certificate Intergration
    Route::get('certification_brief/certificate-paging', [CertificateBriefController::class, 'findPaging']);
    //Step 1
    Route::post('certification_brief/step1-infomation-certification/{id?}', [CertificateBriefController::class, 'postGeneralInfomation']);
    Route::get('certification_brief/certificate-general-infomation/{id}', [CertificateBriefController::class, 'getGeneralInfomation']);
    Route::post('certification_brief/certificate-update-status/{id}', [CertificateBriefController::class, 'updateStatus']);
    Route::get('certification_brief/certificate-appraise-list', [CertificateBriefController::class, 'findAppraisePaging']);
    Route::post('certification_brief/certificate-update-appraise/{id}', [CertificateBriefController::class, 'updateCertificateV3']);
    Route::get('certification_brief/certificate-infomation/{id}', [CertificateBriefController::class, 'getCertificate']);
    Route::post('certification_brief/certificate-update-general/{id}', [CertificateBriefController::class, 'updateCertificateGeneral']);
    Route::get('certification_brief/processing-time', [CertificateBriefController::class, 'getProcessingTime']);
    Route::post('certification_brief/certificate-update-appraisers/{id}', [CertificateBriefController::class, 'updateAppraisersTeam']);
    Route::get('certification_brief/comparison-appraise', [CertificateBriefController::class, 'getComparisonAppraise']);
    Route::get('certification_brief/brief-export', [CertificateBriefController::class, 'exportCertificateBriefs']);
    Route::get('certification_brief/brief-customize-export', [CertificateBriefController::class, 'exportCustomizeCertificateBriefs']);

    Route::post('certificate/sale-document/upload/{id}', [CertificateController::class, 'saleDocumentUpload']);
    Route::get('/DistrictAll', [DistrictController::class, 'findAllByProvince']);

    Route::post('/company-logo', [DictionaryController::class, 'uploadCompanyLogoImage']);
    Route::post('/get-token', [DictionaryController::class, 'getToken']);
    Route::post('/get-info-by-coord', [DictionaryController::class, 'getInfoByCoord']);
    Route::post('/get-info-by-land', [DictionaryController::class, 'getInfoByLand']);
    Route::post('/local-image', [DictionaryController::class, 'uploadLocalImage']);

    Route::get('report/comparison-asset/total-by-province', [ComparisonAssetController::class, 'getAssetByProvince']);
    Route::get('report/comparison-asset/total-by-month-of-year', [ComparisonAssetController::class, 'getAssetByMonthOfYear']);
    Route::get('report/certificate-brieft/doughnut-chart', [CertificateBriefReportController::class, 'countBrieftStatus']);
    Route::get('report/certificate-brieft/doughnut-chart-expired', [CertificateBriefReportController::class, 'countBrieftStatusExpired']);
    Route::get('report/certificate-brieft/bar-chart-status-by-month', [CertificateBriefReportController::class, 'countBrieftStatusByMonth']);
    Route::get('report/certificate-brieft/status-by-appraiser', [CertificateBriefReportController::class, 'countBrieftStatusByAppraiser']);
    Route::get('report/certificate-brieft/doughnut-chart-backlog', [CertificateBriefReportController::class, 'countBrieftBacklog']);
    Route::get('report/certificate-brieft/doughnut-chart-in-processing', [CertificateBriefReportController::class, 'countBriefInProcessing']);
    Route::get('report/certificate-brieft/bar-chart-finish-byQuarters', [CertificateBriefReportController::class, 'countBriefFinishByQuarters']);
    Route::get('report/certificate-brieft/bar-chart-cancel-byQuarters', [CertificateBriefReportController::class, 'countBriefCancelByQuarters']);
    Route::get('report/certificate-brieft/doughnut-chart-branch-revenue', [CertificateBriefReportController::class, 'totalBriefBranchRevenue']);
    Route::get('report/certificate-brieft/doughnut-chart-branch-dept', [CertificateBriefReportController::class, 'totalBriefBranchDebt']);

    Route::post('other-certificate/step1/{id?}', [OtherCertificateAssetController::class, 'postStep1']);
    Route::get('other-certificate/step1/{id}', [OtherCertificateAssetController::class, 'getStep1']);
    Route::post('other-certificate/step2/{id}', [OtherCertificateAssetController::class, 'postStep2']);
    Route::get('other-certificate/step2/{id}', [OtherCertificateAssetController::class, 'getStep2']);
    Route::post('other-certificate/step3/{id}', [OtherCertificateAssetController::class, 'postStep3']);
    Route::get('other-certificate/step3/{id}', [OtherCertificateAssetController::class, 'getStep3']);
    Route::post('other-certificate/step4/{id}', [OtherCertificateAssetController::class, 'postStep4']);
    Route::get('other-certificate/step4/{id}', [OtherCertificateAssetController::class, 'getStep4']);
    Route::get('other-certificate/all-step/{id}', [OtherCertificateAssetController::class, 'getAll']);
    Route::get('personal-property/paging', [PersonalPropertiesController::class, 'findPaging']);
    Route::get('personal-property/getData', [PersonalPropertiesController::class, 'getDataByIdAssetType']);
    Route::post('machine-certificate/step1/{id?}', [MachineCertificateAssetController::class, 'postStep1']);
    Route::post('machine-certificate/step2/{id}', [MachineCertificateAssetController::class, 'postStep2']);
    Route::post('machine-certificate/step3/{id}', [MachineCertificateAssetController::class, 'postStep3']);
    Route::post('machine-certificate/step4/{id}', [MachineCertificateAssetController::class, 'postStep4']);
    Route::get('machine-certificate/all-step/{id}', [MachineCertificateAssetController::class, 'getAll']);
    //export động sản
    Route::get('personal-property/adjust-export', [PersonalPropertiesController::class, 'exportPersonalProperty']);

    Route::post('verhicle-certificate/step1/{id?}', [VerhicleCertificateAssetController::class, 'postStep1']);
    Route::post('verhicle-certificate/step2/{id}', [VerhicleCertificateAssetController::class, 'postStep2']);
    Route::post('verhicle-certificate/step3/{id}', [VerhicleCertificateAssetController::class, 'postStep3']);
    Route::post('verhicle-certificate/step4/{id}', [VerhicleCertificateAssetController::class, 'postStep4']);
    Route::get('verhicle-certificate/all-step/{id}', [VerhicleCertificateAssetController::class, 'getAll']);

    Route::post('technology-certificate/step1/{id?}', [TechnologicalLineCertificateAssetController::class, 'postStep1']);
    Route::post('technology-certificate/step2/{id}', [TechnologicalLineCertificateAssetController::class, 'postStep2']);
    Route::post('technology-certificate/step3/{id}', [TechnologicalLineCertificateAssetController::class, 'postStep3']);
    Route::post('technology-certificate/step4/{id}', [TechnologicalLineCertificateAssetController::class, 'postStep4']);
    Route::get('technology-certificate/all-step/{id}', [TechnologicalLineCertificateAssetController::class, 'getAll']);

    Route::get('province-all', [ProvinceController::class,'getAllProvince']);
    Route::get('project', [ProjectController::class,'index']);
    Route::get('projects', [ProjectController::class,'getAll']);
    // Route::get('apartment/', [ProjectController::class,'getAll']);

    Route::get('project/{id}', [ProjectController::class,'getProjectById']);
    Route::post('project', [ProjectController::class,'createProject']);
    Route::post('project/{id}', [ProjectController::class,'updateProject']);
    Route::post('block/{project_id}', [ProjectController::class,'updateOrCreateBlock']);
    Route::post('floor/{block_id}', [ProjectController::class,'updateOrCreateFloor']);
    Route::post('apartment/{floor_id}', [ProjectController::class,'updateOrCreateApartment']);
    // Route::post('project/update-status/{id}', [ProjectController::class,'updateStatus']);
    Route::get('project/active/{id}', [ProjectController::class,'getProjectActiveById']);
    Route::get('projects/active', [ProjectController::class,'getAllActive']);
    Route::get('apartment-by-floor/{foor_id}', [ProjectController::class,'getApartmentByFloorId']);

    Route::post('project/update-status/id={id}&status={status}', [ProjectController::class,'updateStatusProject']);
    Route::post('block/update-status/id={id}&status={status}', [ProjectController::class,'updateStatusBlock']);
    Route::post('floor/update-status/id={id}&status={status}', [ProjectController::class,'updateStatusFloor']);
    Route::post('apartment/update-status/id={id}&status={status}', [ProjectController::class,'updateStatusApartment']);

    // Route::get('get-all-certificate', [ActivityController::class, 'getAllCertificate']);
    Route::get('activity/get-certificate/{id}', [ActivityController::class, 'getCertificateWithId']);

    // Route::get('get-all-appraise', [ActivityController::class, 'getAllAppraise']);
    Route::get('activity/get-compare/{id}', [ActivityController::class, 'getCompareWithId']);

    // lấy lịch sử tsss
    Route::get('activity/get-appraise/{id}', [ActivityController::class, 'getAppraiseWithId']);

    // SUM Total TSSS
    Route::get('report/get-count-compare-asset', [ComparisonAssetController::class, 'countCompareAssetGeneral']);
    // SUM Total TSTD
    Route::get('report/get-count-appraise-asset', [CertificateAssetReportController::class, 'countAppraiseAsset']);
    Route::get('report/certificate-brieft/bar-chart-finish-byMonth', [CertificateBriefReportController::class, 'countBriefFinishByMonth']);
    Route::get('report/certificate-brieft/bar-chart-cancel-byMonth', [CertificateBriefReportController::class, 'countBriefCancelByMonth']);

    //Apartment Asset
    Route::post('apartment-asset/step1/{id?}', [ApartmentAssetController::class,'postApartmentAsset']);
    Route::post('apartment-asset/step2/{id}', [ApartmentAssetController::class,'postApartmentAssetLaw']);
    Route::post('apartment-asset/step3/{id}', [ApartmentAssetController::class,'postApartmentAssetAppraisal']);
    Route::post('apartment-asset/step4/{id}', [ApartmentAssetController::class,'postAparmentAssetHasAsset']);
    Route::post('apartment-asset/apartment-version-by-id', [ApartmentAssetController::class,'getApartmentVersionById']);
    Route::post('apartment-asset/step5-other-asset/{id}', [ApartmentAssetController::class,'postAparmentOtherAsset']);
    Route::post('apartment-asset/step5-comparison-factor/{id}', [ApartmentAssetController::class,'updateComparisonFactor']);
    Route::post('apartment-asset/step5-update-round-total/{id}', [ApartmentAssetController::class,'updateRoundTotal']);

    // Route::post('apartment-asset/step5/{id}', [ApartmentAssetController::class,'postAparmentAssetHasAsset']);

    Route::get('apartment-asset/all-step/{id}', [ApartmentAssetController::class,'show']);
    // Route::get('apartment-asset', [ApartmentAssetController::class,'index']);
    Route::get('apartment-asset/automatic-asset/{id}', [ApartmentAssetController::class,'getAutomaticAsset']);

    // Route::get('apartment-asset/{id}', [ApartmentAssetController::class,'show']);

    // get activity log machine - verhicle - other
    Route::get('activity/get-machine-certificate-asset/{id}', [ActivityController::class, 'getMachineCertificateAssetWithId']);
    Route::get('activity/get-verhicle-certificate-asset/{id}', [ActivityController::class, 'getVerhicleCertificateAssetWithId']);
    Route::get('activity/get-other-certificate-asset/{id}', [ActivityController::class, 'getOtherCertificateAssetWithId']);

    // Route::get('real-estate', [ApartmentAssetController::class,'indexRealEstates']);
    Route::get('real-estate', [RealEstateController::class,'index']);

    Route::post('certification_asset/estimate_asset_price/{id}', [CertificateAssetsController::class,'updateEstimateAssetPrice']);

    Route::get('certification_asset/check/{id}', [CertificateAssetsController::class,'checkAppraise']);

    Route::post('certification_brief/certificate-update-appraise-version/{id}', [CertificateBriefController::class, 'updateCertificateVersion']);
    Route::get('asset-generals/appraise-detail/{id}', [CertificateAssetsController::class, 'getAppraiseDetail']);
    Route::get('asset-generals/apartment-detail/{id}', [CertificateAssetsController::class, 'getApartmentDetail']);
    Route::get('certification_brief/asset-version', [CertificateBriefController::class, 'getVersionAppraises']);
    Route::get('certification_brief/get-status/{id}', [CertificateBriefController::class, 'getCertificateStatus']);

    Route::post('real_estate/status/{id}', [RealEstateController::class, 'updateStatus']);
    Route::post('real_estate/additional-data/{id}', [RealEstateController::class, 'updateAditionalData']);
    Route::get('real_estate/printPL1/{id}', [RealEstateController::class, 'printPL1']);
    Route::get('real_estate/printPL2/{id}', [RealEstateController::class, 'printPL2']);
    Route::get('real_estate/printPL3/{id}', [RealEstateController::class, 'printPL3']);
    Route::get('real_estate/printTSS/{id}', [RealEstateController::class, 'printTSS']);
    Route::get('real_estate/exportShinhan/{id}', [RealEstateController::class, 'exportShinhanFormat']);

    Route::put('config/update-certificate-sub-status', [CertificateBriefController::class, 'updateSubStatusFromConfig']);
    Route::post('certification_brief/upload-document/{id}/{description}', [CertificateBriefController::class, 'uploadDocument']);
    Route::get('certification_brief/download-document/{id}', [CertificateBriefController::class, 'downloadDocument']);
    Route::get('activity/get-apartment-history/{id}', [ActivityController::class, 'getApartmentAssetWithId']);
    Route::post('real_estate/estimate_asset_price/{id}', [CertificateAssetsController::class,'updateEstimateAssetPrice']);
    Route::post('apartment-asset/estimate_asset_price/{id}', [ApartmentAssetController::class,'updateEstimateAssetPrice']);
    Route::delete('certification_brief/delete-document/{id}', [CertificateBriefController::class,'deleteDocument']);

    Route::get('projects/get-project-by-district', [ProjectController::class,'getProjectByDistrictId']);
});
