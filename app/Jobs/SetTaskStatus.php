<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Task;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetTaskStatus extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->task->inProcess() == 0)
        {
            $this->task->status = 1;
            //exec('service nginx restart');
            //exec('service php5-fpm restart');
        }
        $this->task->save();
    }
}
