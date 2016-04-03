<?php

namespace App\Console\Commands;

use App\Domain;
use Illuminate\Console\Command;

class CopyFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy wp directory to domains dir';

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
     * @return mixed
     */
    public function handle()
    {
        $domains = Domain::where('cloudflare_status', '!=', [0, 2])->where('cpanel_status', '!=', [0, 2])->where('copy_status', 0)->get();
    }
}
