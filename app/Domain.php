<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    public function task()
    {
        return $this->belongsTo('App\Task');
    }

    public static function validate($domain)
    {
        $parts = parse_url($domain);

        if(isset($parts['host']))
        {
            $domain = $parts['host'];
        }

        $domain = preg_replace('|^www\.|isUS', '', $domain);

        return $domain;
    }

    public function getStatus()
    {
        if($this->status == 2) return '<span class="label label-success">Processing</span>';
        elseif($this->status == 1) return '<span class="label label-success">Complete</span>';
        elseif($this->status == 0) return '<span class="label label-warning">Queue</span>';
        else return '<span class="label label-danger">Error</span>';
    }

    public function copyFilesMsg()
    {
        $status = $this->copy_status;

        if($status == 0) return '<span class="label label-info"> - </span>';
        elseif($status == 1) return '<span class="label label-success">OK</span>';
        elseif($status == 3) return '<span class="label label-warning">Was not copied</span>';
        else
        {
            return '<span class="label label-danger">Error</span><br><br>' . $this->copy_response;
        }
    }

    public function cloudFlareMsg()
    {
        $status = $this->cloudflare_status;

        if($status == 0) return '<span class="label label-info"> - </span>';
        elseif($status == 1) return '<span class="label label-success">OK</span>';
        else
        {
            $data = json_decode($this->cloudflare_response, true);

            $html = '<table class="table table-bordered">';

            foreach ($data as $item)
            {
                if($item['status'] == 1) $msg = '<span class="label label-success">OK</span>';
                else $msg = '<span class="label label-danger">' . $item['error_msg'] . '</span>';

                $html = $html . '<tr><td>' . $item['domain'] . '</td><td>' . $msg . '</td></tr>';
            }

            $html = $html . '</table>';

            return $html;
        }
    }

    public function cpanelMsg()
    {
        $status = $this->cpanel_status;

        if($status == 0) return '<span class="label label-info"> - </span>';
        elseif($status == 1) return '<span class="label label-success">OK</span>';
        else
        {
            return '<span class="label label-danger">Error</span><br><br>' . $this->cpanel_response;
        }
    }

    public function getDirectory()
    {
        return env('DOMAINS_DIR') . $this->domain . '/';
    }
}
