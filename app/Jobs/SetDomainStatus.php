<?php

namespace App\Jobs;

use App\Domain;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetDomainStatus extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->domain->cpanel_status == 1 and $this->domain->cloudflare_status == 1 and $this->domain->copy_status == 1) $this->domain->status = 1;
        elseif($this->domain->cpanel_status == 0 or $this->domain->cloudflare_status == 0 or $this->domain->copy_status == 0) $this->domain->status = 0;
        else $this->domain->status = -1;

        $this->domain->save();
    }
}
