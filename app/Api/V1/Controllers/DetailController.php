<?php

namespace App\Api\V1\Controllers;

use DB;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\BaseClient;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;


class DetailController extends Controller
{
    use Helpers;

    public function index() {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::GetByClientId($currentUser->client_id);
        //return $client->bills()->select('bill_id', 'bill_number', 'bill_date', DB::raw('bill_sum::numeric(10,2) as bill_sum'))->orderBy('bill_date', 'desc')->get()->toArray();
    }

    public function show($date)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = new Client([]);

        $result = $client->request('POST', 'http://pbill.local/php/reports/details/details_non_local_1.php', [
            'form_params' => [
                'oper_id' => 'RT',
                'client_ids' => $currentUser->client_id,
                'dateFrom' => '2017-01-01',
                'dateTo' => '2017-01-31 23:59',
                'detailsType' => 1
            ]
        ]);

        return response($result->getBody())->withHeaders([
            'Content-type' => 'application/pdf',
        ]);

    }

}
