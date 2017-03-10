<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use JWTAuth;
use App\BaseClient;
use Dingo\Api\Routing\Helpers;
use Mockery\CountValidator\Exception;


class BaseClientController extends Controller
{
    use Helpers;

    public function index() {
        $user = JWTAuth::parseToken()->authenticate();
        return BaseClient::GetByClientId($user->client_id);
    }

    public function update(Request $request, $id) {
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $client = BaseClient::find($user->client_id);
            if($client == null)
                throw new Exception('No such client');

            $this->validate($request, [
                'details_type' => 'integer|between:0,7',
                'contact_email' => 'email|max:50',
                'contact_phone' => 'string|max:200'
            ]);

            $input = $request->all();
            $input = array_only($input, ['details_type', 'contact_phone', 'contact_email']);
            $client->update($input);
        } catch (Exception $ex) {
            return response(['error' => true, 'message' => $ex->getCode()], Response::HTTP_BAD_REQUEST);
        }
        return response(['error' => false, 'message' => $user->client_id], Response::HTTP_ACCEPTED);
    }


}
