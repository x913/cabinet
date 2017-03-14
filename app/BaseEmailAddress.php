<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseEmailAddress extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'base_email_address';
    public $incrementing = false;
    public $timestamps = false;

    protected $visible = ['id', 'email', 'detail_type', 'comment'];

    protected $fillable = ['email', 'detail_type', 'comment'];
    
}
