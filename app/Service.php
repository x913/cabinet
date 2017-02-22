<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    protected $primaryKey = 'id';
    protected $table = 'telephone_services';
    public $timestamps = false;


    protected $fillable = [
        'address_mount', 'descr'
    ];

    public static function GetByClientId($id) {
        return self::where('client_id', '=', $id);
    }

}
