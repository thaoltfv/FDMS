<?php

namespace App\Providers;

use App\Contracts\AddressLogRepository;
use App\Contracts\ApartmentAssetRepository;
use App\Contracts\ApartmentRepository;
use App\Contracts\AppraiseAssetRepository;
use App\Contracts\AppraiseDictionaryRepository;
use App\Contracts\AppraiseLawDocumentRepository;
use App\Contracts\AppraiseOtherInformationRepository;
use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\AppraisalConstructionCompanyRepository;
use App\Contracts\AppraiseRepository;
use App\Contracts\AppraiserRepository;
use App\Contracts\AppraiseVersionRepository;
use App\Contracts\BlockListRepository;
use App\Contracts\BranchRepository;
use App\Contracts\BuildingPriceRepository;
use App\Contracts\CertificateRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\CompareAssetVersionRepository;
use App\Contracts\CompareGeneralPicRepository;
use App\Contracts\CustomerRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\DistanceRepository;
use App\Contracts\DistrictRepository;
use App\Contracts\DonavaOldEstatesRepository;
use App\Contracts\DonavaOldUserRepository;
use App\Contracts\EstimatePriceLogRepository;
use App\Contracts\EstimatePriceRepository;
use App\Contracts\LoginHistoryRepository;
use App\Contracts\MigrateStatusRepository;
use App\Contracts\PermissionRepository;
use App\Contracts\ProvinceRepository;
use App\Contracts\RoleRepository;
use App\Contracts\StreetRepository;
use App\Contracts\UnitPriceRepository;
use App\Contracts\UserRepository;
use App\Contracts\WardRepository;
use App\Contracts\CertificateAssetRepository;
use App\Contracts\MachineCertificateAssetRepository;
use App\Contracts\PersonalPropertiesRepository;
use App\Contracts\OtherCertificateAssetRepository;
use App\Contracts\ProjectRepository;
use App\Contracts\RealEstateRepository;
use App\Contracts\TechnologyCertificateAssetRepository;
use App\Contracts\VerhicleCertificateAssetRepository;
use App\Contracts\ViewCertificateBrieftRepository;
use App\Http\Controllers\OtherCertificateAssetController;
use App\Models\AddressLog;
use App\Models\Apartment;
use App\Models\ApartmentAsset;
use App\Models\AppraisalConstructionCompany;
use App\Models\Appraise;
use App\Models\AppraiseDictionary;
use App\Models\AppraiseLawDocument;
use App\Models\AppraiseOtherInformation;
use App\Models\Appraiser;
use App\Models\AppraiserCompany;
use App\Models\AppraiseVersion;
use App\Models\BlockList;
use App\Models\Branch;
use App\Models\BuildingPrice;
use App\Models\Certificate;
use App\Models\CompareAssetGeneral;
use App\Models\CompareAssetVersion;
use App\Models\CompareGeneralPic;
use App\Models\Customer;
use App\Models\Dictionary;
use App\Models\Distance;
use App\Models\District;
use App\Models\DonavaOldEstates;
use App\Models\DonavaOldUser;
use App\Models\EstimatePriceLog;
use App\Models\LoginHistory;
use App\Models\MigrateStatus;
use App\Models\Permission;
use App\Models\Province;
use App\Models\Role;
use App\Models\Street;
use App\Models\UnitPrice;
use App\Models\User;
use App\Models\Ward;
use App\Models\CertificateAsset;
use App\Models\MachineCertificateAsset;
use App\Models\PersonalProperty;
use App\Models\OtherCertificateAsset;
use App\Models\Project;
use App\Models\RealEstate;
use App\Models\TechnologicalLineCertificateAsset;
use App\Models\VerhicleCertificateAsset;
use App\Models\ViewCertificateBrief;
use App\Repositories\EloquentAddressLogRepository;
use App\Repositories\EloquentApartmentAssetRepository;
use App\Repositories\EloquentApartmentRepository;
use App\Repositories\EloquentAppraiseAssetRepository;
use App\Repositories\EloquentAppraiseDictionaryRepository;
use App\Repositories\EloquentAppraiseLawDocumentRepository;
use App\Repositories\EloquentAppraiseOtherInformationRepository;
use App\Repositories\EloquentAppraiserCompanyRepository;
use App\Repositories\EloquentAppraisalConstructionCompanyRepository;
use App\Repositories\EloquentAppraiseRepository;
use App\Repositories\EloquentAppraiserRepository;
use App\Repositories\EloquentAppraiseVersionRepository;
use App\Repositories\EloquentAssetVersionRepository;
use App\Repositories\EloquentBlockListRepository;
use App\Repositories\EloquentBranchRepository;
use App\Repositories\EloquentBuildingPriceRepository;
use App\Repositories\EloquentCertificateRepository;
use App\Repositories\EloquentCompareAssetGeneralRepository;
use App\Repositories\EloquentCompareGeneralPicRepository;
use App\Repositories\EloquentCustomerRepository;
use App\Repositories\EloquentDictionaryRepository;
use App\Repositories\EloquentDistanceRepository;
use App\Repositories\EloquentDistrictRepository;
use App\Repositories\EloquentDonavaOldEstatesRepository;
use App\Repositories\EloquentDonavaOldUserRepository;
use App\Repositories\EloquentEstimatePriceLogRepository;
use App\Repositories\EloquentEstimatePriceRepository;
use App\Repositories\EloquentLoginHistoryRepository;
use App\Repositories\EloquentMigrateStatusRepository;
use App\Repositories\EloquentPermissionRepository;
use App\Repositories\EloquentProvinceRepository;
use App\Repositories\EloquentRepository;
use App\Repositories\EloquentRoleRepository;
use App\Repositories\EloquentStreetRepository;
use App\Repositories\EloquentUnitPriceRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentWardRepository;
use App\Repositories\EloquentCertificateAssetRepository;
use App\Repositories\EloquentMachineCertificateAssetRepository;
use App\Repositories\EloquentPersonalPropertiesRepository;
use App\Repositories\EloquentOtherCertificateAssetRepository;
use App\Repositories\EloquentProjectRepository;
use App\Repositories\EloquentRealEstateRepository;
use App\Repositories\EloquentTechnologyCertificateAssetRepository;
use App\Repositories\EloquentVerhicleCertificateAssetRepository;
use App\Repositories\EloquentViewCertificateBriefRepository;
use App\Services\Document\Certificate\ReportCertificate;
use App\Services\Document\Certificate\ReportCertificateInterface;
use App\Services\Document\DocumentInterface\Report;
use App\Services\Document\DocumentInterface\ReportInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(LoginHistoryRepository::class, function () {
            return new EloquentLoginHistoryRepository(new LoginHistory());
        });

        $this->app->singleton(UserRepository::class, function () {
            return new EloquentUserRepository(new User());
        });

        $this->app->singleton(RoleRepository::class, function () {
            return new EloquentRoleRepository(new Role());
        });

        $this->app->singleton(PermissionRepository::class, function () {
            return new EloquentPermissionRepository(new Permission());
        });

        $this->app->singleton(ProvinceRepository::class, function () {
            return new EloquentProvinceRepository(new Province());
        });

        $this->app->singleton(DistrictRepository::class, function () {
            return new EloquentDistrictRepository(new District());
        });

        $this->app->singleton(WardRepository::class, function () {
            return new EloquentWardRepository(new Ward());
        });

        $this->app->singleton(StreetRepository::class, function () {
            return new EloquentStreetRepository(new Street());
        });

        $this->app->singleton(BranchRepository::class, function () {
            return new EloquentBranchRepository(new Branch());
        });

        $this->app->singleton(CompareAssetGeneralRepository::class, function () {
            return new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
        });

        $this->app->singleton(EstimatePriceRepository::class, function () {
            return new EloquentEstimatePriceRepository(new CompareAssetGeneral());
        });

        $this->app->singleton(DictionaryRepository::class, function () {
            return new EloquentDictionaryRepository(new Dictionary());
        });

        $this->app->singleton(ApartmentRepository::class, function () {
            return new EloquentApartmentRepository(new Apartment());
        });

        $this->app->singleton(BuildingPriceRepository::class, function () {
            return new EloquentBuildingPriceRepository(new BuildingPrice());
        });

        $this->app->singleton(DistanceRepository::class, function () {
            return new EloquentDistanceRepository(new Distance());
        });

        $this->app->singleton(UnitPriceRepository::class, function () {
            return new EloquentUnitPriceRepository(new UnitPrice());
        });

        $this->app->singleton(CompareGeneralPicRepository::class, function () {
            return new EloquentCompareGeneralPicRepository(new CompareGeneralPic());
        });

        $this->app->singleton(BlockListRepository::class, function () {
            return new EloquentBlockListRepository(new BlockList());
        });

        $this->app->singleton(DonavaOldEstatesRepository::class, function () {
            return new EloquentDonavaOldEstatesRepository(new DonavaOldEstates());
        });

        $this->app->singleton(DonavaOldUserRepository::class, function () {
            return new EloquentDonavaOldUserRepository(new DonavaOldUser());
        });

        $this->app->singleton(MigrateStatusRepository::class, function () {
            return new EloquentMigrateStatusRepository(new MigrateStatus());
        });

        $this->app->singleton(EstimatePriceLogRepository::class, function () {
            return new EloquentEstimatePriceLogRepository(new EstimatePriceLog());
        });

        $this->app->singleton(CustomerRepository::class, function () {
            return new EloquentCustomerRepository(new Customer());
        });

        $this->app->singleton(AppraiseDictionaryRepository::class, function () {
            return new EloquentAppraiseDictionaryRepository(new AppraiseDictionary());
        });

        $this->app->singleton(AppraiserRepository::class, function () {
            return new EloquentAppraiserRepository(new Appraiser());
        });

        $this->app->singleton(AppraiserCompanyRepository::class, function () {
            return new EloquentAppraiserCompanyRepository(new AppraiserCompany());
        });

        $this->app->singleton(AppraisalConstructionCompanyRepository::class, function () {
            return new EloquentAppraisalConstructionCompanyRepository(new AppraisalConstructionCompany());
        });

        $this->app->singleton(AppraiseOtherInformationRepository::class, function () {
            return new EloquentAppraiseOtherInformationRepository(new AppraiseOtherInformation());
        });

        $this->app->singleton(AppraiseLawDocumentRepository::class, function () {
            return new EloquentAppraiseLawDocumentRepository(new AppraiseLawDocument());
        });

        $this->app->singleton(AddressLogRepository::class, function () {
            return new EloquentAddressLogRepository(new AddressLog());
        });

        $this->app->singleton(AppraiseRepository::class, function () {
            return new EloquentAppraiseRepository(new Appraise());
        });

        $this->app->singleton(CertificateAssetRepository::class, function () {
            return new EloquentCertificateAssetRepository(new CertificateAsset());
            //return new EloquentCertificateAssetRepository(new Appraise());
        });

        $this->app->singleton(CompareAssetVersionRepository::class, function () {
            return new EloquentAssetVersionRepository(new CompareAssetVersion());
        });

        $this->app->singleton(AppraiseAssetRepository::class, function () {
            return new EloquentAppraiseAssetRepository(new CompareAssetGeneral());
        });

        $this->app->singleton(AppraiseVersionRepository::class, function () {
            return new EloquentAppraiseVersionRepository(new AppraiseVersion());
        });

        $this->app->singleton(CertificateRepository::class, function () {
            return new EloquentCertificateRepository(new Certificate());
        });

        $this->app->singleton(ViewCertificateBrieftRepository::class, function () {
            return new EloquentViewCertificateBriefRepository(new ViewCertificateBrief());
        });

        $this->app->singleton(OtherCertificateAssetRepository::class, function () {
            return new EloquentOtherCertificateAssetRepository(new OtherCertificateAsset());
        });

        $this->app->singleton(PersonalPropertiesRepository::class, function () {
            return new EloquentPersonalPropertiesRepository(new PersonalProperty());
        });

        $this->app->singleton(MachineCertificateAssetRepository::class, function () {
            return new EloquentMachineCertificateAssetRepository(new MachineCertificateAsset());
        });

        $this->app->singleton(VerhicleCertificateAssetRepository::class, function () {
            return new EloquentVerhicleCertificateAssetRepository(new VerhicleCertificateAsset());
        });

        $this->app->singleton(TechnologyCertificateAssetRepository::class, function () {
            return new EloquentTechnologyCertificateAssetRepository(new TechnologicalLineCertificateAsset());
        });

        $this->app->singleton(ProjectRepository::class, function () {
            return new EloquentProjectRepository(new Project());
        });

        $this->app->singleton(ApartmentAssetRepository::class, function () {
            return new EloquentApartmentAssetRepository(new ApartmentAsset());
        });
        
        $this->app->singleton(RealEstateRepository::class, function () {
            return new EloquentRealEstateRepository(new RealEstate());
        });

        $this->app->singleton(ReportCertificateInterface::class, function () {
            return new ReportCertificate();
        });
        $this->app->singleton(ReportInterface::class, function () {
            return new Report();
        });
        //:end-bindings:
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            LoginHistoryRepository::class,
            UserRepository::class,
            RoleRepository::class,
            PermissionRepository::class,
        ];
    }
}
