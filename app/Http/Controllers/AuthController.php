<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {
        // $this->validate($request, );

        $input = $request->all();

        $validationRules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'address' => 'required|string'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = new User();
        $user->name = $input['name'];
        $user->email = $input['email'];
        $plainPassword = $input['password'];
        $user->password = app('hash')->make($plainPassword);

        
        if ($input['role'])
        $user->role = $input['role'];
        
        $user->save();
        
        $profile = new Profile([
            'user_id' => $user->id,
            'gender' => $input['gender'],
            'birth_date' => $input['birth_date'],
            'address' => $input['address'],
        ]);

        $user->profile()->save($profile);

        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $validationRules = [
            'email' => 'required|string',
            'password' => 'required|string'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::setTTL(180)->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Logout Success'], 200);
    }

    public function getUser()
    {
        $user = Auth::user();
        $user->profile;

        $response = [
            'message' => 'Get User Success',
            'status_code' => 200,
            'data' => $user
        ];

        return response()->json($response, 200);
    }
}
