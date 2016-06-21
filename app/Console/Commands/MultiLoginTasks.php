<?php

namespace App\Console\Commands;

use App\Jobs\CreateProfile;
use App\Jobs\SetMultiloginTaskStatus;
use App\MultiloginItem;
use Illuminate\Console\Command;
use App\MultiloginTask;
use App\Jobs\DeleteMultiloginItem;
use App\DeleteTask;
use App\Jobs\DeleteByRange;

class MultiLoginTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'multilogin:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Multilogin';

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
            $items = MultiloginItem::where('status', 0)->get();

            foreach ($items as $item) {
                sleep(2);
                dispatch(new CreateProfile($item));
                dispatch(new SetMultiloginTaskStatus($item->task));
            }

            sleep(2);

            $tasks = MultiloginTask::where('status', -1)->get();

            foreach ($tasks as $task) {
                $items = $task->items;

                foreach ($items as $item) {
                    dispatch(new DeleteMultiloginItem($item));

                    sleep(2);
                }
            }

            $deleted_tasks = DeleteTask::where('status', 0)->get();

            foreach ($deleted_tasks as $deleted_task)
            {
                dispatch(new DeleteByRange($deleted_task));
            }
        }
    }
}
