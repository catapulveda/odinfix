<?php

namespace App\Jobs;

use App\Api\Config;
use App\Api\Curl;
use App\Api\MultiLogin;
use App\Jobs\Job;
use App\MultiloginItem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateProfile extends Job implements ShouldQueue
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
        $this->item->status = 2;
        $this->item->save();

        try {
            Curl::$verbose = true;

            $data = Config::get();

            if($this->item->proxy)
            {
                $proxy = json_decode($this->item->proxy);

                foreach ($proxy as $key => $value)
                {
                    $data[$key] = $value;
                }
            }

            $data['name'] = $this->item->name;
            //$data['user'] = $this->item->name;

            foreach ($data as $key => $value)
            {
                if($value === false or strlen($value) == 0) unset($data[$key]);
            }

            Curl::$verbose = true;

            $multilogin = new MultiLogin(Config::get('token'));

            $this->item->ext_id = $multilogin->createProfile($data);
            $this->item->status = 1;
        }
        catch (\Exception $e)
        {
            $this->item->error_msg = $e->getMessage();
            $this->item->status = -1;
        }

        $this->item->save();
    }
}
