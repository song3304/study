<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['coverUrl'];
    
    public function getCoverUrlAttribute()
    {
        return !empty($this->cover)?url('attachment?id=').$this->cover:'';
    }
}
