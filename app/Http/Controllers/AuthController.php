<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller {
    public function register(Request $request) {
        $user = User::create([
             'email'    => $request->email,
             'password' => $request->password,
             'name'     => $request->email, 
             'email'    => $request->email, 
             'password' => $request->email, 
             'firstname' => $request->email, 
             'lastname' => $request->email, 
             'mobile'   => $request->email, 
             'uuid' => $request->email, 
             'country_code' => $request->email, 
             'language_code' => $request->email, 
             'access'   => $request->access
         ]);

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    public function login() {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->guard()->getTTL() * 60
        ]);
    }
}
