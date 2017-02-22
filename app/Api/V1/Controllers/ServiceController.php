<?php
/**
 * Created by PhpStorm.
 * User: k3kc
 * Date: 15.02.2017
 * Time: 16:06
 */

namespace App\Api\V1\Controllers;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\BaseClient;
use Dingo\Api\Routing\Helpers;


class ServiceController extends Controller
{
    use Helpers;

    public function index() {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::GetByClientId($currentUser->client_id);
        return $client->telephoneServices()->orderBy('user_id')->get()->toArray();
    }

    public function update(Request $request, $id) {

        // validate

        $input = $request->all();
        $input = array_only($input, ['address_mount', 'descr']);

        $currentUser = JWTAuth::parseToken()->authenticate();
        $client = BaseClient::GetByClientId($currentUser->client_id);

        $service = Service::where(
            [
                ['id', '=', $id],
                ['client_id', '=', $client->client_id]
            ])->first();

        if($service == null) {
            // not valid client id
            print 'not valid client id';
            return;
        }

        $service->address_mount = $input['address_mount'];
        $service->descr = $input['descr'];

        $service->save();
    }

}