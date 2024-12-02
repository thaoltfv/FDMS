<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProjectRankDictionarySeeder::class);
        $this->call(AppraiseOtherInformationUpdateAcronymSeeder::class);
        $this->call(InsertDictionariesPhanLoaiTSSeeder::class);
        $this->call(UpdateUserIdForAppraserTableSeeder::class);
        $this->call(UpdateProcessingTimeSeeder::class);
        $this->call(CertificateDictionarySeeder::class);
        $this->call(PermissionsTableSeeder::class);

        // $this->call(AppraiseLawDocumentSeeder::class);
        // $this->call(AppraiseOtherInformationSeeder::class);
        // $this->call(UpdateDictionariesAppraisePicTableSeeder::class);
        // $this->call(UpdateDictionariesAddChucVuSeeder::class);
        // $this->call(AppraiseImportDictionariesTableSeeder::class);
        // $this->call(AppraisalConstructionCompanySeeder::class);
        // $this->call(AppraiseLawDocumentSeeder::class);
        // $this->call(AppraiseOtherInformationSeeder::class);
        // $this->call(AppraiseDictionariesTableSeeder::class);
        // $this->call(UpdateDictionariesAppraisePicTableSeeder::class);
//        $this->call(DictionariesTableSeeder::class);
//        $this->call(RolesTableSeeder::class);
//        $this->call(BranchesTableSeeder::class);
//        $this->call(UsersTableSeeder::class);
//        $this->call(UpdateUserSeeder::class);
//        $this->call(ProvincesTableSeeder::class);
//        $this->call(DistrictsTableSeeder::class);
//        $this->call(BuidingPriceTableSeeder::class);
//        $this->call(WardsTableSeeder::class);
//        $this->call(ApartmentsTableSeeder::class);
        // $this->call(StreetTableSeeder::class);
        // $this->call(DistanceTableSeeder::class);
    }
}
