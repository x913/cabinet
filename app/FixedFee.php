<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FixedFee extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'fixed_fee_services';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [

    ];

    //protected $visible = ['client_id', 'cnt', 'tariff'];
    
    public static function GetByClientId($id) {
        return self::where('client_id', '=', $id)->first();
    }

}
