<?php

use Illuminate\Database\Seeder;

class UpdateProductionDB extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UpdateUserIdForAppraserTableSeeder::class);
        $this->call(ProjectRankDictionarySeeder::class);
        $this->call(AppraiseOtherInformationUpdateAcronymSeeder::class);
        $this->call(InsertDictionariesPhanLoaiTSSeeder::class);
        $this->call(UpdateUserIdForAppraserTableSeeder::class);
        $this->call(UpdateProcessingTimeSeeder::class);
        $this->call(CertificateDictionarySeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(AddDocumentDictionarySeeder::class);
    }
}
