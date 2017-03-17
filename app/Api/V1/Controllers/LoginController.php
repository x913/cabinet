<?php

namespace App\Api\V1\Controllers;

use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginController extends Controller
{
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['credentials.email', 'credentials.password', 'credentials.response']);

        // validate recaptcha

        $client = new Client();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret'   => getenv('RE_CAP_SECRET'),
                'response' => $credentials['credentials']['response'],
                'remoteip' => request()->getClientIp()
            ]
        ]);

        try {
            $result = json_decode($response->getBody());

            if (!$result->success)
                throw new AccessDeniedHttpException('invalid recaptcha response');

            $token = $JWTAuth->attempt(
                array(
                    'email' => $credentials['credentials']['email'],
                    'password' => $credentials['credentials']['password']
                )
            );

            if (!$token) {
                throw new AccessDeniedHttpException();
            }
        } catch (JWTException $e) {
            throw new HttpException(500);
        } catch (Exception $e) {
            throw new HttpException(500);
        }


        return response()
            ->json([
                'status' => 'ok',
                'token' => $token
            ]);
    }
}
