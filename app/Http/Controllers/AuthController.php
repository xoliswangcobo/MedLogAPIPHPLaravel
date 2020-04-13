<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller {
    public function register(Request $request) {
        $user = User::where("email", $request->email)->first();
    
        if ($user != null) {
            return response()->json(['message' => 'Email already in use.'], 409);
        }

        $user = User::create([
            'firstname' => $request->firstname, 
            'lastname' => $request->lastname, 
            'mobile'   => $request->mobile,
            'email'    => $request->email,
            'email_verified_at' => $request->email_verified_at, 
            'password' => $request->password,
            'role_id'   => 1,
            'language_code' => $request->language_code, 
            'country_code' => $request->country_code, 
            'is_verified' => false, 
            'remember_token' => false, 
            'created_at' => Carbon::now()->timestamp, 
            'updated_at' => Carbon::now()->timestamp
         ]);

        return response()->json($user);
    }

    public function login() {
        $token = null;

        // // Not yet, do not limit concurrent logins
        // if (auth('api')->check() == true) {
        //     $token = auth('api')->invalidate();
        // }

        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout() {
        if (auth('api')->check() == true) {
            auth('api')->logout();
            return response()->json(['message' => 'Successfully logged out']);
        }

        return response()->json(['message' => 'Forbidden, Not logged in'], 403);
    }

    public function getAuthUser(Request $request) {
        if (auth('api')->check() == true) {
            return response()->json(auth('api')->user());
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 2
        ]);
    }
}
