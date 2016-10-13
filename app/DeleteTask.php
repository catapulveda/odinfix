<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeleteTask extends Model
{
    protected $fillable = ['from', 'to', 'prefix'];

    public function getStatus()
    {
        if($this->status == 2) return '<span class="label label-success">Processing</span>';
        elseif($this->status == 1) return '<span class="label label-success">Success / Total deleted: ' . $this->total_deleted . '</span>';
        elseif($this->status == 0) return '<span class="label label-warning">Queue</span>';
        else return '<span class="label label-danger">Error</span><br>' . $this->error_msg;
    }
}

