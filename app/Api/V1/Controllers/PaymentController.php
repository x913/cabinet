<?php

namespace App\Api\V1\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\BaseClient;
use App\Payment;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use Illuminate\Http\Response;


class PaymentController extends Controller
{
    use Helpers;

    public function index() {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::GetByClientId($currentUser->client_id);
        return $client->payments()
            ->select('payment_id', 'payment_number', DB::raw("to_char(payment_date, 'DD.MM.YYYY') as payment_date"), DB::raw('payment_sum::numeric(10,2) as payment_sum'))
            ->orderBy('payment_date', 'desc')
            ->get()
            ->toArray();
    }

}
