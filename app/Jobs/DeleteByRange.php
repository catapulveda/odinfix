<?php

namespace App\Jobs;

use App\DeleteTask;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Api\MultiLogin;
use App\MultiloginItem;
use App\Api\Config;

class DeleteByRange extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $task;

    public function __construct(DeleteTask $task)
    {
        $this->task = $task;
    }

    public function handle()
    {
        $task = $this->task;
        $task->status = 2;
        $task->save();

        try {
            $delete_profiles = [];

            for ($i = $task->from; $i <= $task->to; $i = $i + 1) {
                $delete_profiles[] = $task->prefix . $i;
            }

            $multilogin = new MultiLogin(Config::get('token'));
            $profiles = $multilogin->getProfiles();

            sleep(5);

            $total_deleted = 0;

            foreach ($delete_profiles as $delete_profile) {
                foreach ($profiles as $profile) {
                    if ($delete_profile == $profile['name']) {
                        sleep(2);

                        # Delete
                        $multilogin->removeProfile($profile['sid']);

                        $total_deleted = $total_deleted + 1;

                        $item = MultiloginItem::where('ext_id', $profile['sid'])->first();

                        if ($item) {
                            $item->status = -2;
                            $item->save();
                        }
                    }
                }
            }

            $task->total_deleted = $total_deleted;
            $task->status = 1;
            $task->save();
        }
        catch (\Exception $e)
        {
            $task->status = -1;
            $task->error_msg = $e->getMessage();
            $task->save();
        }
    }
}
