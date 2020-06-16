<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EmailSender;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function resetPassword(Request $request) {

        $user = User::where("email", $request->email)->first();
    
        if ($user == null) {
            return response()->json(['success' => false, 'message' => 'Email not found.'], 404);
        }

        // Create Password Reset Token
        DB::table('password_resets')->insert([
            'id' => Str::uuid(),
            'email' => $user->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);

        //Get the token just created above
        $tokenData = DB::table('password_resets')->where('email', $user->email)->first();

        //Generate, the password reset link. The token generated is embedded in the link
        $link = config('base_url') . 'password/reset/' . $tokenData->token . '?email=' . urlencode($user->email);

        if (EmailSender::sendEmail($user->email, 'Medlog Password Reset', $link) == true) {
            return response()->json(['success' => true,'message' => 'Password reset email sent successfully.']);
        } else {
            return response()->json(['success' => false,'message' => 'Failed to send password reset email, please try again.']);
        }
    }

    public function setPassword(Request $request) {

        //Validate input
        // $validator = Validator::make($request->all(), [
        //     'email' => 'required|exists:users,email',
        //     'password' => 'required|confirmed'
        // ]);

        //check if input is valid before moving on
        // if ($validator->fails()) {
        //     return response()->json(['success' => false, 'message' => 'Required data insufficient.'], 400);
        // }

        $password = $request->password;

        // Validate the token
        $tokenData = DB::table('password_resets')->where('token', $request->token)->first();
        if ($tokenData == null) {
            return response()->json(['success' => false, 'message' => 'No valid user associated with token.'], 404);
        }

        // Get user for email
        $user = User::where('email', $tokenData->email)->first();
        if ($user == null) {
            return response()->json(['success' => false, 'message' => 'Email not found.'], 404);
        }

        // Check token expiry
        $createdDate = Carbon::parse($tokenData->created_at);
        $now = Carbon::now();

        if ($now->timestamp - $createdDate->timestamp > 1200) {
            // Delete the token
            DB::table('password_resets')->where('email', $user->email)->delete();
            return response()->json(['success' => false, 'message' => 'Token expired.'], 401);
        }

        //Hash and update the new password
        $user->password = $password;
        $user->update();

        // Delete the token
        DB::table('password_resets')->where('email', $user->email)->delete();

        //Send Email Reset Success Email
        if (EmailSender::sendEmail($user->email, 'Medlog Password Reset', 'Password was reset succesfully.') == true) {
            return response()->json(['success' => false, 'message' => 'Password reset succesfully.'], 202);
        } else {
            return response()->json(['success' => false, 'message' => 'Password reset falied.'], 500);
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
