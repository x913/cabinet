<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\BaseClient;
use Dingo\Api\Routing\Helpers;


class FixedFeeController extends Controller
{
    use Helpers;

    public function index() {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::GetByClientId($currentUser->client_id);

        return DB::table('fixed_fee_services')
            ->select('fixed_fee_types.descr', DB::raw('sum(fixed_fee_services.count) as cnt, sum(fixed_fee_services.tariff) as tariff'))
            ->where('fixed_fee_services.client_id', $currentUser->client_id)
            ->join('fixed_fee_types', 'fixed_fee_types.type_id', '=', 'fixed_fee_services.type_id')
            ->groupBy('fixed_fee_types.descr')
            ->get()
            ->toArray();
    }

}
