<?php

use App\Models\AppraiseLawDocument;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class AppraiseLawDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/appraise_law_documents.csv'));
            $sheets->map(function ($value) {
                return AppraiseLawDocument::query()
                    ->updateOrCreate(
                        array(
                            'document_type' => $value['document_type'],
                            'type' => $value['type'],
                            'date' => $value['date'],
                            'content' => $value['content'],
                        ),
                        array(
                            'provinces' => $value['provinces'],
                            'position' => $value['position'],
                            'is_defaults'=> $value['is_defaults'],
                        )
                    );
            });
        });

    }
}
