<?php

namespace App\Api\V1\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\BaseClient;
use App\Bill;
use App\Contract;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use Illuminate\Http\Response;


class BillController extends Controller
{
    use Helpers;

    public function index() {
        $user = JWTAuth::parseToken()->authenticate();

        return Bill::with('Contract')
            ->select('contract_id', 'client_id', 'bill_sum', 'bill_id', 'bill_number', DB::raw("to_char(bill_date, 'DD.MM.yyyy') as bill_date_str"))
            ->where('bill_bills.client_id', '=', $user->client_id)
            ->orderBy('bill_date', 'desc')
            ->get()->toArray();


        //$client = BaseClient::GetByClientId($currentUser->client_id);
        //return $client->bills()->select('bill_sum', 'bill_id', 'bill_number', DB::raw("to_char(bill_date, 'DD.MM.yyyy') as bill_date_str"))->orderBy('bill_date', 'desc')->get()->toArray();
    }

    public function show($id)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::GetByClientId($currentUser->client_id);
        $bill = $client->bill($id);

        $client = new Client([
           // 'base_uri' => 'http://pbill.local/template.cgi?tpl=report/report_invoice_print.tpl&action=remote',
        ]);


        $result = $client->request('POST', 'http://pbill.local/template.json.cgi?tpl=report/report_invoice_print.tpl&action=remote', [
            'form_params' => [
                'oper_id' => $bill->oper_id,
                #'date_from' => $bill->bill_date,
                #'date_till' => $bill->bill_date,
                'date_from' => '01.01.2017',
                'date_till' => '31.01.2017 23:59:59',
                'poa_id' => '',
                'group_id' => '',
                //'banner' => 'on',
                //'bank' => 'on',
                'bill_id' => $bill->bill_id,
                'offset_value' => 1,
                'limit_value' => 50,
                'client_id' => $bill->client_id,
                'bill_type' => $bill->bill_type,
                'print_form' => 'invoice'
            ]
        ]);

        return response('<!DOCTYPE html><html><head><meta charset="utf-8"></head>' . $result->getBody())->withHeaders([
            'Content-type' => 'text/html; charset=koi8-r',
        ]);

    }

}
