<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;

class TelephoneType extends Model
{
    protected $table = 'telephone_telephones_type';
    public $timestamps = false;

    public static function Types() {
        $user = JWTAuth::parseToken()->authenticate();
        return TelephoneType::where('oper_id', '=', $user->oper_id);
    }

}
