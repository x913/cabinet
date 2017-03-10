<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //protected $primaryKey = 'client_id';
    protected $primaryKey = 'contract_id';
    protected $table = 'contract_contracts';
    public $incrementing = false;
    public $timestamps = false;
    
    
    public static function GetByClientId($id) {
        return self::where('client_id', '=', $id)->first();
    }
}
