<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['nullable', 'numeric'],
            'email' => 'required|string|email',
            'password' => 'required|string|between:5,20',
        ],
        [
            'email.required' => 'error.required',
            'email.string' => 'error.invalid',
            'email.email' => 'error.invalid',
            'password.required' => 'error.required',
            'password.string' => 'error.invalid',
            'password.between' => 'error.invalid',
        ]);
 
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $firstError = reset($errors);
            $errorsResponse = [];
            foreach ($errors as $key => $value) {
                $errorsResponse[$key] = $value[0];
            }
            return response()->json([
                'message' => $firstError[0],
                'errors' => $errorsResponse,
            ], 400);
        }

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'error.invalid'
            ], 401);
        }

        return $this->jsonResponse($token);
    }
    
    protected function jsonResponse($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => auth()->user(),
            'expires_in'   => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}
