<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Log;

class CleanUpDocxFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-docx-files-render:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up docx files older than 1 hour';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $now = now()->timezone('Asia/Ho_Chi_Minh');
        $year = $now->format('Y');
        $month = $now->format('m');

        $directoriesToCheck = [
            env('STORAGE_DOCUMENTS') . '/certification_briefs' . '/' . $year . '/' . $month,
            env('STORAGE_DOCUMENTS') . '/certification_briefs',
        ];

        foreach ($directoriesToCheck as $directory) {
            if (File::exists(storage_path('app/public/' . $directory))) {
                $files = scandir(storage_path('app/public/' . $directory));
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $filePath = storage_path('app/public/' . $directory . '/' . $file);
                        if (file_exists($filePath)) {
                            $fileInfo = pathinfo($filePath);
                            if (isset($fileInfo['extension']) && (strtolower($fileInfo['extension']) === 'docx' || strtolower($fileInfo['extension']) === 'xlsx') || strtolower($fileInfo['extension']) === 'zip') {
                                unlink($filePath);
                                Log::info('Deleted file successfully: ' . $file);
                            }
                        }
                    }
                }
            }
        }
    }
}
