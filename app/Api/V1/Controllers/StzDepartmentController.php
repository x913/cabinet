<?php
/**
 * Created by PhpStorm.
 * User: k3kc
 * Date: 02.03.2017
 * Time: 15:22
 */

namespace App\Api\V1\Controllers;
use App\Service;
use App\StzDepartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Dingo\Api\Routing\Helpers;


class StzDepartmentController extends Controller
{
    use Helpers;

    public function index() {
        $user = JWTAuth::parseToken()->authenticate();
        return StzDepartment::where('client_id', '=', $user->client_id)
            ->select(
                'stz_departments.id',
                'stz_departments.name as stz_department_name',
                'stz_departments.full_name as stz_department_full_name',
                'stz_department_ownership_types.name as stz_department_ownership_type_name'
            )
            ->leftJoin('stz_department_ownership_types', function ($join) {
                $join->on('stz_department_ownership_types.id', '=', 'stz_departments.stz_department_ownership_type_id');
            })
            ->get()
            ->toArray();
    }
}