<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseEmailAddress extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'base_email_address';
    public $incrementing = false;
    public $timestamps = false;
    
}
