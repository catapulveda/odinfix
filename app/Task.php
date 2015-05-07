<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function domains()
    {
        return $this->hasMany('App\Domain');
    }

    public function total()
    {
        return $this->domains()->count();
    }

    public function errors()
    {
        return $this->domains()->where('status', -1)->count();
    }

    public function inProcess()
    {
        return $this->domains()->where('status', 0)->count();
    }

    public function success()
    {
        return $this->domains()->where('status', 1)->count();
    }
}
