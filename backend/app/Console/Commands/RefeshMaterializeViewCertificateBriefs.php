<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefeshMaterializeViewCertificateBriefs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refesh_materialize_view_certificate_brief:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'REFRESH MATERIALIZED VIEW';

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
        DB::statement("REFRESH MATERIALIZED VIEW view_certificate_briefs");
    }
}
