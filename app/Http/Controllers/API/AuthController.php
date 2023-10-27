<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MsDriver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        /**
         * @unauthenticated
         */
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255|min:2',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            echo $validator->messages()->toJson();
        } else {

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password'])
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
            //return JSON process insert failed
            //  return response()->json([
            //     'success' => false,
            // ], 409);
        }
    }
    /**
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = MsDriver::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Not AUthenticated'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function profile(Request $request)
    {

        $msDriver = Auth::user(); // Retrieve the currently authenticated ms_driver

        return response()->json([
            'user' => $msDriver,
        ]);
    }
    /**
     * @unauthenticated
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });

            return response()->json(['message' => 'Logged out'], 200);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
    /**
     * @unauthenticated
     */
    public function login_v2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError('Login Failed.', ['email' => 'Cannot Find Email'], 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->sendError('Login Failed.', ['password' => 'Wrong Password'], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('Token')->plainTextToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success, 'User login successful.');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
        }
    }
}
