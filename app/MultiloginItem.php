<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiloginItem extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function task()
    {
        return $this->belongsTo('App\MultiloginTask');
    }

    public function getStatus()
    {
        if($this->status == 2) return '<span class="label label-success">Processing</span>';
        elseif($this->status == 1) return '<span class="label label-success">Complete</span>';
        elseif($this->status == 0) return '<span class="label label-warning">Queue</span>';
        elseif($this->status == -2) return '<span class="label label-info">Deleted</span>';
        elseif($this->status == -3) return '<span class="label label-danger">Delete error</span>';
        else return '<span class="label label-danger">Error</span><br>' . $this->error_msg;
    }

    public function getProxy()
    {
        if($this->proxy)
        {
            $data = json_decode($this->proxy, true);

            if(!isset($data['proxyHost']))
            {
                return ' - ';
            }

            if(isset($data['proxyUser'])) return $data['proxyUser'] . ':' . $data['proxyPass'] . '@' . $data['proxyHost'] . ':' . $data['proxyPort'];
            else return $data['proxyHost'] . ':' . $data['proxyPort'];
        }
        else return ' - ';
    }
}
