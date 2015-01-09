<?php

namespace App\Console\Commands;

use App\Domain;
use App\Jobs\AddToCloudFlare;
use App\Jobs\AddToCpanel;
use App\Jobs\CopyFilesWP;
use App\Jobs\CreateNginxConf;
use App\Jobs\SetDomainStatus;
use App\Jobs\SetTaskStatus;
use Illuminate\Console\Command;

class TaskExecute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:exec';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task execute';

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
        while (true) {
            $domains = Domain::where('status', 0)->get();

            foreach ($domains as $domain)
            {
                $domain->status = 2;
                $domain->save();

                dispatch(new AddToCloudFlare($domain));
                dispatch(new AddToCpanel($domain));
                //dispatch(new CreateNginxConf($domain));
                dispatch(new CopyFilesWP($domain));
                dispatch(new SetDomainStatus($domain));
                dispatch(new SetTaskStatus($domain->task));
            }

            if($domains->count() == 0) sleep(10);
        }
    }
}
