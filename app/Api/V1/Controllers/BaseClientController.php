<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use JWTAuth;
use DB;
use App\BaseClient;
use App\BaseEmailAddress;
use Dingo\Api\Routing\Helpers;
use Mockery\CountValidator\Exception;
use App\Http\Requests\UpdateBaseClientRequest;
use App\Http\Requests\PostEmailRequest;


class BaseClientController extends Controller
{
    use Helpers;

    public function index() {
        $user = JWTAuth::parseToken()->authenticate();
        return BaseClient::GetByClientId($user->client_id);
    }

    public function removeEmail(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $address = BaseEmailAddress::where([
            ['id', '=', $id],
            ['client_id', '=', $user->client_id],

        ])->first();

        if($address == null)
            return response(['error' => true, 'message' => 'can`t find email address'], Response::HTTP_BAD_REQUEST);

        $address->delete();
        return response(['error' => false, 'message' => $user->client_id], Response::HTTP_ACCEPTED);
    }

    public function addEmail(PostEmailRequest $request)
    {
        $address = new BaseEmailAddress;
        $user = JWTAuth::parseToken()->authenticate();

        $id = DB::select("select nextval('base_email_address_id_seq')");
        $address->id = intval($id['0']->nextval);

        $address->client_id = $user->client_id;
        $address->oper_id = 'RT';
        $address->email = $request->email;
        $address->comment = $request->comment;

        $dt = 0;
        foreach ($request->details as $k) {
            if($k['selected'])
                $dt += $k['id'];
        }
        $address->detail_type = $dt;
        $address->save();

        return response(['error' => false, 'message' => $address->id, 'data' => $address], Response::HTTP_ACCEPTED);
    }

    public function update(UpdateBaseClientRequest $request, $id) {
        $user = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::find($user->client_id);
        if($client == null)
            throw new Exception('No such client');

        $input = $request->all();
        $input = array_only($input, [ 'contact_phone', 'emails' ]);

        $client->contact_phone = $input['contact_phone'];
        $client->save();

        foreach ($input['emails'] as $k) {

            $dt = 0;
            foreach ($k['details'] as $d) {
                if($d['selected'])
                    $dt += $d['id'];
            }

            $address = BaseEmailAddress::where([
                ['id', '=', $k['id']],
                ['client_id', '=', $user->client_id],

            ])->first();

            if($address == null) {
                continue;
            }

            $address->email = $k['email'];
            $address->comment = $k['comment'];
            $address->detail_type = $dt;
            $address->save();
        }
        return response(['error' => false, 'message' => $user->client_id], Response::HTTP_ACCEPTED);
    }


}
