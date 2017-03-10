<?php

namespace App;

use DB;
use App\Service;
use App\TelephoneType;
use App\Contract;
use App\FixedFee;
use App\Bill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;


class BaseClient extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'base_clients';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        //'client_id',
        //'name',
        //'balance'
        'details_type',
        'contact_email',
        'contact_phone'
    ];

    protected $visible = [
        'client_id', 'balance', 'inn', 'kpp', 'name', 'full_name', 'address_jur', 'address_post', 'contact_phone', 'contact_email', 'details_type',
        'Bills', 'Contracts', 'FixedFees'
    ];

    public function telephoneServices()
    {
        return $this->hasMany('App\Service', 'client_id', 'client_id')
            ->select(
                'telephone_services.id',
                'telephone_services.user_id',
                'telephone_services.address_mount',
                'telephone_services.descr',
                'telephone_telephones_type.descr as phone_type',
                'telephone_services.stz_department_id',
                'stz_departments.name',
                'stz_departments.full_name'
            )
            ->join('telephone_telephones_type', function($join) {
                $join->on('telephone_telephones_type.type_id', '=', 'telephone_services.phone_type');
                $join->on('telephone_telephones_type.oper_id', '=', 'telephone_services.oper_id');
            })
            ->leftJoin('stz_departments', function($join) {
                $join->on('stz_departments.client_id', '=', 'telephone_services.client_id');
                $join->on('stz_departments.id', '=', 'telephone_services.stz_department_id');
            })
            ->whereNull('date_expire');
   
    }

/*    public function contracts() {
        return   $this->hasMany('App\Contract', 'client_id', 'client_id')
            ->select(
                'contract_contracts.is_main',
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
    }*/

 /*   public function fixedFees() {
        return $this->hasMany('App\FixedFee', 'client_id', 'client_id');
    }*/

    public function bills() {
        return $this->hasMany('App\Bill', 'client_id', 'client_id');
    }

    public function payments() {
        return $this->hasMany('App\Payment', 'client_id', 'client_id');
    }

    public function bill($id) {
        return Bill::GetByClientAndBillId($this->client_id, $id);
    }

    public function clientBills() {
        return $this->hasMany('App\Bill', 'client_id', 'client_id'); //->limit(5);
    }

    public function Contracts()
    {
        return $this->hasMany('App\Contract', 'client_id', 'client_id')
            ->select(
                'contract_contracts.client_id',
                'contract_contracts.is_main',
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
            ->whereNotNull('contract_contracts.contract_date')
            ->orderBy('contract_contracts.date_active', 'desc');
    }

    public function FixedFees()
    {
        return $this->hasMany('App\FixedFee', 'client_id', 'client_id')
            ->select('client_id', DB::raw('sum(fixed_fee_services.tariff) as tariff, sum(fixed_fee_services.count) as cnt'), 'fixed_fee_types.descr')
            ->join('fixed_fee_types', 'fixed_fee_types.type_id', '=', 'fixed_fee_services.type_id')
            ->groupBy('fixed_fee_types.descr', 'client_id')
            ->orderBy('fixed_fee_types.descr');
    }


    public static function GetByClientId($id) {

        $data = BaseClient::with('clientBills')->with('Contracts')->with('FixedFees')->where('base_clients.client_id', '=', $id)->firstOrFail(); # !working
        return $data;
        #return self::where('client_id', '=', $id)->firstOrFail();
    }

}
