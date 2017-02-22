<?php

namespace App;

use DB;
use App\Service;
use App\Contract;
use App\FixedFee;
use App\Bill;
use Illuminate\Database\Eloquent\Model;


class BaseClient extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'base_clients';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'client_id',
        'name',
        'balance'
    ];

    public function telephoneServices()
    {
        return $this->hasMany('App\Service', 'client_id', 'client_id')->whereNull('date_expire');
    }

    public function contracts() {
        return   $this->hasMany('App\Contract', 'client_id', 'client_id')
            ->select(
                DB::raw('CASE WHEN contract_contracts.contract_date >= \'01.01.2014\' then
                            contract_contract_types.descr || \' &#8470;\' || contract_contracts.contract_number || to_char(contract_contracts.contract_date, \' от DD.MM.YY г.\')
                         else
                            contract_contracts.descr
                         end as contract_title'),
                DB::raw("to_char(contract_contracts.date_active, 'DD.MM.YYYY') as date_active"),
                DB::raw("to_char(contract_contracts.last_date, 'DD.MM.YYYY') as last_date"),
                DB::raw("case when contract_contracts.last_date is null then 0 else 1 end as expired")
            )
            ->join(
                'contract_contract_types', function ($join) {
                    $join->on('contract_contract_types.contract_type', '=', 'contract_contracts.contract_type');
                    $join->on('contract_contract_types.oper_id', '=', 'contract_contracts.oper_id');
                }
            )
            ->whereNotNull('contract_contracts.contract_date')
            ->orderBy('contract_contracts.date_active', 'desc');
    }

    public function fixedFees() {
        return $this->hasMany('App\FixedFee', 'client_id', 'client_id');
    }

    public function bills() {
        return $this->hasMany('App\Bill', 'client_id', 'client_id');
    }

    public function payments() {
        return $this->hasMany('App\Payment', 'client_id', 'client_id');
    }


    public function bill($id) {
        return Bill::GetByClientAndBillId($this->client_id, $id);
    }

    public static function GetByClientId($id) {
        return self::where('client_id', '=', $id)->first();
    }

}
