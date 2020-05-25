<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller {
    public function register(Request $request) {
        $user = User::where("email", $request->email)->first();
    
        if ($user != null) {
            return response()->json(['success' => false, 'message' => 'Email already in use.'], 409);
        }

        try {
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
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Can not register, please try again.', 'data' => $e], 500);
        }

        return response()->json(['success' => true,'message' => 'Registration completed successfully.']);
    }

    public function login() {
        $token = null;

        // // Not yet, do not limit concurrent logins
        // if (auth('api')->check() == true) {
        //     $token = auth('api')->invalidate();
        // }

        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Succesfully logged in.',
            'data' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60
            ]
        ]);
    }

    public function passwordReset(Request $request) {
        $user = User::where("email", $request->email)->first();
    
        if ($user == null) {
            return response()->json(['success' => false, 'message' => 'Email not found.'], 404);
        }

        // Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);

        //Get the token just created above
        $tokenData = DB::table('password_resets')->where('email', $request->email)->first();

        //Generate, the password reset link. The token generated is embedded in the link
        $link = config('base_url') . 'password/reset/' . $tokenData . '?email=' . urlencode($user->email);

        try {
            //Here send the link with CURL with an external email API 
            return true;
        } catch (Exception $e) {
            return false;
        }

        return response()->json(['success' => true,'message' => 'Password reset email sent successfully.']);
    }

    public function resetPassword(Request $request) {

        //Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required|confirmed'
        ]);

        //check if input is valid before moving on
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Required data insufficient.'], 400);
        }

        $password = $request->password;

        // Validate the token
        $tokenData = DB::table('password_resets')->where('token', $request->token)->first();

        // Redirect the user back to the password reset request form if the token is invalid
        if (!$tokenData) return view('auth.passwords.email');

        $user = User::where('email', $tokenData->email)->first();
        if ($user == null) {
            return response()->json(['success' => false, 'message' => 'Email not found.'], 404);
        }

        //Hash and update the new password
        $user->password = Hash::make($password);
        $user->update();

        // Delete the token
        DB::table('password_resets')->where('email', $user->email)->delete();

        //Send Email Reset Success Email
        if ($this->sendSuccessEmail($tokenData->email)) {
            return response()->json(['success' => false, 'message' => 'Password reset succesfully.'], 404);
        } else {
            return response()->json(['success' => false, 'message' => 'Service error occured. Request may not have completed succesfully.'], 500);
        }
    }

    public function logout() {
        if (auth('api')->check() == true) {
            auth('api')->logout();
            return response()->json(['success' => true, 'message' => 'Succesfully logged out.']);
        }

        return response()->json(['success' => false, 'message' => 'Forbidden, Not logged in.'], 403);
    }

    public function getAuthUser() {
        if (auth('api')->check() == true) {
            return response()->json(auth('api')->user());
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }
}
