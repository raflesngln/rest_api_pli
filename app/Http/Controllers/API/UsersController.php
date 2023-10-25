<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {

        // $users = User::all();
        // $dataResp=$request()->query('page');
        $users = User::orderBy('id', 'desc')->limit(4)->get();

        return response()->json([
            'success' => true,
            'data' => $users,
            // 'resp' => $dataResp,
        ], 200);
    }

    public function show($id)
    {
        // if ($user->tokenCan('user:update')) {
        //     //
        // }

        // Retrieve a single user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        // Create a new user
        $user = User::create($request->all());
        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        // Update an existing user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id)
    {
        // Delete a user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
