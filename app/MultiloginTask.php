<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MultiloginTask extends Model
{
    protected $fillable = ['from', 'to', 'prefix'];

    public function items()
    {
        return $this->hasMany('App\MultiloginItem', 'task_id', 'id');
    }

    public function total()
    {
        return $this->items()->count();
    }

    public function success()
    {
        return $this->items()->where('status', 1)->count();
    }

    public function errors()
    {
        return $this->items()->where('status', -1)->count();
    }

    public function inProcess()
    {
        return $this->items()->where('status', 0)->count();
    }
}
