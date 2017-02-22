<?php

namespace App\Api\V1\Controllers;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function token()
    {
        $token = JWTAuth::getToken();

        if(!$token)
            throw new BadRequestHttpException('No token provided');

        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $ex) {
            throw new AccessDeniedException($ex->getMessage());
        }

        return response()
            ->json([
                'status' => 'ok',
                'token' => $token
            ]);

        return response()->withArray(['token' => $token]);

        /*
 *
 *
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $JWTAuth->attempt($credentials);

            if(!$token) {
                throw new AccessDeniedHttpException();
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        return response()
            ->json([
                'status' => 'ok',
                'token' => $token
            ]);*/
    }
}
