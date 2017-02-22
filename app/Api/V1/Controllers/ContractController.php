<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\BaseClient;
use Dingo\Api\Routing\Helpers;


class ContractController extends Controller
{
    use Helpers;

    public function index() {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::GetByClientId($currentUser->client_id);
        return $client->contracts()->get()->toArray();
    }

}
