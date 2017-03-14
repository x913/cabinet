<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use JWTAuth;
class Contract extends Model
{
    //protected $primaryKey = 'client_id';
    protected $primaryKey = 'contract_id';
    protected $table = 'contract_contracts';
    public $incrementing = false;
    public $timestamps = false;

    //protected $visible = ['contract_id', 'client_id', 'comment', 'descr'];

    /**
     * @param array $attributes
     */
    public static function ClientContracts()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return Contract::where('client_id', '=', $user->client_id)
            ->select(
                'contract_contracts.contract_id',
                'contract_contracts.client_id',
                'contract_contracts.is_main',
                'contract_contracts.limit_sum',
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
            })
            //->whereNotNull('contract_contracts.contract_date')
            ->orderBy('contract_contracts.date_active', 'desc');
    }
    
    public static function GetByClientId($id) {
        return self::where('client_id', '=', $id)->first();
    }
}
