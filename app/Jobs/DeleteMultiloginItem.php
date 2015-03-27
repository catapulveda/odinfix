<?php

namespace App\Jobs;

use App\Api\Config;
use App\Api\MultiLogin;
use App\Jobs\Job;
use App\MultiloginItem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteMultiloginItem extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $item;

    public function __construct(MultiloginItem $item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->item->status == 0 or $this->item->status == 2) throw new \Exception("Unable delete domain with 'In prcoess' status");

        try {
            if ($this->item->status == 1) {
                $multilogin = new MultiLogin(Config::get('token'));
                $multilogin->removeProfile($this->item->ext_id);
            }

            $this->item->status = -2;
        }
        catch (\Exception $e)
        {
            $this->item->status = -3;
        }

        $this->item->save();

        $this->item->delete();
    }
}
