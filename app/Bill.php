<?php

namespace App;

use App\BaseClient;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'bill_bills';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [];
    protected $visible = ['client_id', 'bill_id', 'bill_sum', 'bill_number', 'bill_date_str'];

    public function client()
    {
        return $this->belongsTo('App\BaseClient', 'client_id');
    }

    public static function GetByClientAndBillId($cid, $bid) {
        return self::where('client_id', '=', $cid)
            ->where('bill_id', '=', $bid)
            ->first();
    }

}
