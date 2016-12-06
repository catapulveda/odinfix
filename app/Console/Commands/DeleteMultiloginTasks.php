<?php

namespace App\Console\Commands;

use App\Jobs\DeleteMultiloginItem;
use App\MultiloginTask;
use App\Task;
use Illuminate\Console\Command;
use League\Flysystem\Exception;

class DeleteMultiloginTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multilogin:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete multilogin tasks';

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

            sleep(5);
        }
    }
}
