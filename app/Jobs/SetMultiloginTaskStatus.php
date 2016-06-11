<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\MultiloginTask;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetMultiloginTaskStatus extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $task;

    public function __construct(MultiloginTask $task)
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
        if($this->task->items()->where('status', 0)->count() == 0)
        {
            $this->task->status = 1;
            $this->task->save();
        }
    }
}
