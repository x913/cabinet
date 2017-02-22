<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'contract_contracts';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [

    ];
    
    public static function GetByClientId($id) {
        return self::where('client_id', '=', $id)->first();
    }
}
