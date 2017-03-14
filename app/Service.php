<?php

namespace App;

use DB;
use JWTAuth;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    protected $primaryKey = 'client_id';
    protected $table = 'telephone_services';
    public $timestamps = false;


    protected $fillable = [
        'address_mount', 'descr'
    ];

    protected $visible = [
        'client_id', 'contract_id', 'date_create', 'date_create_str', 'user_id', 'address_mount', 'PhoneType', 'Contract'
    ];

  /*  protected $visible = [
        'user_id',
        'address_mount',
        'descr',
        'id'
    ];*/

/*    public function TelephoneType() {
        return $this->hasOne('App\TelephoneType', 'type_id', 'phone_type')->first();
    }*/

    public function PhoneType() {
        return $this->hasOne('App\TelephoneType', 'type_id', 'phone_type')->where('oper_id', '=', 'RT');
    }

    // contract for current service
    public function Contract()
    {
        return $this->hasOne('App\Contract', 'contract_id', 'contract_id')
            ->select(
                'is_main', 'client_id', 'contract_id',
                DB::raw('CASE WHEN contract_contracts.contract_date >= \'01.01.2014\' then
                            contract_contract_types.descr || \' &#8470;\' || contract_contracts.contract_number || to_char(contract_contracts.contract_date, \' от DD.MM.YY г.\')
                         else
                            contract_contracts.descr
                         end as contract_title'),
                DB::raw("to_char(contract_contracts.date_active, 'DD.MM.YYYY') as date_active"),
                DB::raw("to_char(contract_contracts.contract_date, 'DD.MM.YYYY') as contract_date"),
                DB::raw("to_char(contract_contracts.last_date, 'DD.MM.YYYY') as last_date"),
                DB::raw("case when contract_contracts.last_date is null then 0 else 1 end as expired")
            )
            ->join(
                'contract_contract_types', function ($join) {
                    $join->on('contract_contract_types.contract_type', '=', 'contract_contracts.contract_type');
                    $join->on('contract_contract_types.oper_id', '=', 'contract_contracts.oper_id');
                }
            )
            ->orderBy('contract_contracts.date_active', 'desc');
    }
    
    public static function GetByClientId($id) {
        return self::where('client_id', '=', $id);
    }

}
