<?php
/**
 * Created by PhpStorm.
 * User: k3kc
 * Date: 15.02.2017
 * Time: 16:06
 */

namespace App\Api\V1\Controllers;
use App\Service;
use App\Contract;
use App\TelephoneType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use DB;
use App\BaseClient;
use Dingo\Api\Routing\Helpers;


class ServiceController extends Controller
{
    use Helpers;

    public function index() {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json(
            [
                'services' =>
                    Service::with('PhoneType')->with('Contract')
                    ->select(
                        'client_id', 'contract_id', 'date_create', 'user_id', 'address_mount', 'phone_type',
                        DB::raw("case when date_create < '01.01.2000' then null else to_char(date_create, 'MM.DD.YYYY') end as date_create_str")
                    )
                    ->where([
                        ['client_id', '=', $user->client_id],
                        ['enabled', '=', '1'],
                    ])->whereNull('date_expire')
                        ->orderBy('phone_type')
                        ->orderBy('user_id')
                        ->get()->toArray(),
                'contracts' => Contract::ClientContracts()->get()->toArray(),
                'types' => TelephoneType::Types()->get()->toArray()
            ]
        );

    }

    public function update(Request $request, $id) {
/*        $input = $request->all();
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

        $service->save();*/
    }

}