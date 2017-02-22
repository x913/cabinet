<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'bill_bills';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [

    ];

    public static function GetByClientAndBillId($cid, $bid) {
        return self::where('client_id', '=', $cid)
            ->where('bill_id', '=', $bid)
            ->first();
    }

}
