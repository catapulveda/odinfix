<?php

namespace App\Console\Commands;

use App\Jobs\DeleteByRange;
use App\RemoveTask;
use Illuminate\Console\Command;

class DeleteDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        while (true)
        {
            $removeTasks = RemoveTask::where('status', 0)->get();

            foreach ($removeTasks as $removeTask)
            {
                dispatch(new \App\Jobs\DeleteDomains($removeTask));
            }

            sleep(15);
        }
    }
}
