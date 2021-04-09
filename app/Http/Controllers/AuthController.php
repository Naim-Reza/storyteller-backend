<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * login
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //find user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User Not Found!!'], 404);
        }

        //check password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Incorrect password!!'], 400);
        }

        return response()->json(['token' => $user->createToken($request->email, ['server:update'])->plainTextToken]);
    }

    /**
     * register
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @return $token
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:3|max:20',
            'lastname' => 'required|string|min:3|max:20',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $oldUser = User::where('email', $request->email)->first();
        if ($oldUser) {
            return response()->json(['error' => 'An account is already created with this email address!!'], 400);
        }

        $userCreated = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$userCreated) {
            return response()->json(['error' => 'Something Went Wrong. Please try again!!'], 500);
        }

        $user = User::find($userCreated->id);
        return response()->json(['token' => $user->createToken($request->email, ['server:update'])->plainTextToken]);

    }

    /**
     * logout
     *
     * @return bool
     */
    public function logout(Request $request)
    {
        $user = User::find($request->user()->id);
        if (!$user) {
            return response()->json(['error' => 'User Not Found!!'], 404);
        }

        //revoke tokens
        return $user->tokens()->delete();
    }
}
