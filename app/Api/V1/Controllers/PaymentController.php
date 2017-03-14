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
        $user = JWTAuth::parseToken()->authenticate();

        return Payment::where('client_id', '=', $user->client_id)
            ->select(
                'payment_id', 'payment_number', 'payment_sum',
                DB::raw("to_char(payment_date, 'DD.MM.YYYY') as payment_date_str"),
                DB::raw("to_char(payment_date, 'MM.YYYY') as payment_date_month")
            )
            ->orderBy('payment_date', 'descr')
            ->get()
            ->toArray();
    }

}
