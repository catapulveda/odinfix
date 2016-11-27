<?php

namespace App\Jobs;

use App\Domain;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;

class AddToCpanel extends Job implements ShouldQueue
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
        $domain = $this->domain->domain;

        try {
            $cpanel = new \Gufy\CpanelPhp\Cpanel(
                [
                    'host' => env('CPANEL_HOST'), // ip or domain complete with its protocol and port
                    'username' => env('CPANEL_LOGIN'), // username of your server, it usually root.
                    'auth_type' => 'password', // set 'hash' or 'password'
                    'password' => env('CPANEL_PASSWORD'), // long hash or your user's password
                ]
            );

            $cpanel->setTimeout(1000);

            $domain = Domain::validate($domain);

            $username = preg_replace('|[^0-9A-z]*|isUS', '', $domain);

            $request = [
                'dir' => '/public_html/' . $domain,
                'newdomain' => $domain,
                'subdomain' => $username
            ];

            $result = $cpanel->cpanel('AddonDomain', 'addaddondomain', 'hqtools', $request);

            $response = json_decode($result, true);

            if (!isset($response['cpanelresult'])) throw new Exception('Cpanel Response problem');

            $data = $response['cpanelresult'];

            if (isset($data['error']))
            {
                if(!preg_match('|already exists|isUS', $data['error'])) throw new Exception($data['error']);
            }

            $this->domain->cpanel_status = 1;
        }
        catch (\Exception $e)
        {
            $this->domain->cpanel_response = $e->getMessage();
            $this->domain->cpanel_status = -1;
        }

        $this->domain->save();
    }
}
