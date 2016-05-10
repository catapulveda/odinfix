<?php

namespace App\Jobs;

use App\Domain;
use App\Jobs\Job;
use App\RemoveTask;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteDomains extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $removeTask;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RemoveTask $removeTask)
    {
        $this->removeTask = $removeTask;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $removeTask = $this->removeTask;

        if($removeTask->status != 0) throw new \Exception('Status != 0');

        $domains = json_decode($removeTask->domains, true);

        foreach ($domains as $domain)
        {
            $domainObjects = Domain::where('domain', $domain)->get();

            if($domainObjects->count() > 0) {
                $nginx = env('NGINX_PATH') . $domain;
                $php_fpm = '/etc/php5/fpm/pool.d/' . $domain . '.conf';

                if(file_exists($nginx)) unlink($nginx);
                if(file_exists($php_fpm)) unlink($php_fpm);

                foreach ($domainObjects as $domainObject)
                {
                    $domainObject->delete();
                }
            }
        }

        $removeTask->status = 1;
        $removeTask->save();

        exec('service nginx reload');
        exec('service php7.0-fpm restart');
    }
}
