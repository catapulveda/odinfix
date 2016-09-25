<?php
namespace App\Api;

class MultiLogin
{
    protected $token;

    function __construct($token)
    {
        $this->token = $token;

        Curl::Start();
    }

    public function getProfile($id)
    {

    }

    function createProfile($data)
    {
        unset($data['token']);

        $request = json_encode($data);

        echo "\n\nRequest:\n\n";

        echo $request;

        $url = 'https://api.multiloginapp.com/v1/profile/create?token=' . urlencode($this->token);

        $page = Curl::PostQuery($url, $request, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        echo "\n\n-----------------------\n\nResponse:\n\n";

        echo $page;

        $result = json_decode($page, true);

        if(!isset($result['status'])) throw new \Exception('Unknown error');
        if($result['status'] == 'OK') return $result['value'];
        if(isset($result['value'])) throw new \Exception($result['value']);
        throw new \Exception('Unknown error');
    }

    function removeProfile($id)
    {
        $url = 'https://api.multiloginapp.com/v1/profile/remove?profileId=' . urlencode($id) . '&token=' . urlencode($this->token);

        $page = Curl::OpenPage($url, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $result = json_decode($page, true);

        if(!isset($result['status'])) throw new \Exception('Unknown error');
        if($result['status'] == 'OK') return true;
        if(isset($result['value'])) throw new \Exception($result['value']);
        throw new \Exception('Unknown error');
    }

    function getProfiles()
    {
        $profiles = [];

        $url = 'https://api.multiloginapp.com/v1/profile/list?token=' . $this->token;

        $page = Curl::OpenPage($url, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $result = json_decode($page, true);

        if(!is_array($result)) throw new \Exception('Response problem');
        if(!isset($result['data'])) throw new \Exception('Response problem');

        $profiles = $result['data'];

        while (isset($result['paging']['next']))
        {
            $page = Curl::OpenPage($result['paging']['next'], [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);

            $result = json_decode($page, true);

            if(!is_array($result)) throw new \Exception('Response problem');
            if(!isset($result['data'])) throw new \Exception('Response problem');

            foreach ($result['data'] as $item)
            {
                $profiles[] = $item;
            }
        }

        return $profiles;
    }
}