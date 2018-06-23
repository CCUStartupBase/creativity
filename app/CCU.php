<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CCU extends Model
{
    //
    protected $table = 'news_copy';
    protected $fillable = ['CATEGORY','TITLE','CONTENT','DATE','DISPLAY' ];
    public $timestamps = false;
}
