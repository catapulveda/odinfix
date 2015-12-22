<?php

namespace App\Jobs;

use App\Domain;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;

class AddToCloudFlare extends Job implements ShouldQueue
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
        $domains = [$this->domain->domain];

        $result = [];

        $success = 0;

        foreach ($domains as $domain)
        {
            try {
                $this->addDomain($domain, env('CF_IP'));
                $res = [
                    'domain' => $domain,
                    'status' => 1,
                    'error_msg' => '',
                    'error_code' => ''
                ];

                $success = $success + 1;
            }
            catch (Exception $e)
            {
                $res = [
                    'domain' => $domain,
                    'status' => -1,
                    'error_msg' => $e->getMessage(),
                    'error_code' => $e->getCode()
                ];
            }

            $result[] = $res;
        }

        if($success == 1) $this->domain->cloudflare_status = 1;
        else $this->domain->cloudflare_status = -1;

        $this->domain->cloudflare_response = json_encode($result, true);

        $this->domain->save();
    }

    protected function addDomain($domain, $ip)
    {
        $zone_domain = Domain::validate($domain);

        $client = new \Cloudflare\Api(env('CF_EMAIL'), env('CF_API_KEY'));

        $zone = new \Cloudflare\Zone($client);
        $zones = $zone->zones($zone_domain);

        print_r($zones);

        if(!$zones->success) throw new Exception('Not response from API');

        if($zones->result_info->total_count == 1 or $zones->result_info->total_count == 0) # Zone exists
        {
            # Delete zone
            if($zones->result_info->total_count == 1) {
                $response = $zone->delete_zone($zones->result[0]->id);

                if(!$response->success) throw new Exception("Can't delete zone");
            }

            # Create new zone
            $result = $zone->create($zone_domain);

            print_r($result);

            if(!$result->success) throw new Exception("Can't create new zone");

            $zone = $result->result;
        }
        else throw new Exception('2 zones found!');

        /*
        elseif($zones->result_info->total_count == 0) # Create new zone
        {
            $result = $zone->create($zone_domain);

            if(!$result->success) throw new Exception("Can't create new zone");

            $zone = $result->result;
        }
        else throw new Exception('2 zones exists'); # 2 or more zones
        */

        $dns = new \Cloudflare\Zone\Dns($client);
        $res = $dns->create($zone->id, 'A', $domain, $ip, 120, true);
        $res1 = $dns->create($zone->id, 'A', 'www.' . $domain, $ip, 120, true);

        if($res->success and $res1->success) return true;
        else
        {
            if(isset($res->errors) and is_array($res->errors)) {
                foreach ($res->errors as $error) {
                    throw new Exception($error->message, $error->code);
                }
            }

            if(isset($res1->errors) and is_array($res1->errors)) {
                foreach ($res1->errors as $error) {
                    throw new Exception($error->message, $error->code);
                }
            }

            throw new Exception('Unknown error');
        }
    }

    protected function addARecord($domain, $ip)
    {
        $zone_domain = Domain::validate($domain);

        $client = new \Cloudflare\Api(env('CF_EMAIL'), env('CF_API_KEY'));

        $zone = new \Cloudflare\Zone($client);
        $zones = $zone->zones($zone_domain);

        if(!$zones->success) throw new Exception('Not response from API');

        if($zones->result_info->total_count == 1 or $zones->result_info->total_count == 0) # Zone exists
        {
            # Delete zone
            if($zones->result_info->total_count == 1) {
                $response = $zone->delete_zone($zones->result[0]->id);

                if(!$response->success) throw new Exception("Can't delete zone");
            }

            # Create new zone
            $result = $zone->create($zone_domain);

            print_r($result);

            if(!$result->success) throw new Exception("Can't create new zone");

            $zone = $result->result;
        }
        else throw new Exception('2 zones found!');

        /*
        elseif($zones->result_info->total_count == 0) # Create new zone
        {
            $result = $zone->create($zone_domain);

            if(!$result->success) throw new Exception("Can't create new zone");

            $zone = $result->result;
        }
        else throw new Exception('2 zones exists'); # 2 or more zones
        */

        $dns = new \Cloudflare\Zone\Dns($client);
        $res = $dns->create($zone->id, 'A', $domain, $ip, 120, true);

        if($res->success) return true;
        else
        {
            foreach ($res->errors as $error)
            {
                throw new Exception($error->message, $error->code);
            }

            throw new Exception('Unknown error');
        }
    }
}
