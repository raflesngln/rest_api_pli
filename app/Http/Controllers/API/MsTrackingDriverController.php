<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MsTrackingDriver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class MsTrackingDriverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {

        // Retrieve a list of users
        $resp = MsTrackingDriver::all();
        return response()->json($resp);
    }

    public function show($id)
    {
        // Retrieve a single user by ID
        $resp = MsTrackingDriver::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($resp);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'index'     => 'required|string|unique:ms_tracking_drivers',
            'title'    => 'required|string',
        ]);

        if ($validator->fails()) {
            echo $validator->messages()->toJson();
        }else{

            $user = MsTrackingDriver::create([
                'index' => $request['index'],
                'title' => $request['title'],
                'deskripsi' =>$request['deskripsi']
            ]);


            $response = [
                'user' => $user,
                'message' =>'Success create data'
            ];

            return response($response, 201);
        }


    }

    public function update(Request $request, $id)
    {
        // Update an existing user
        $resp = MsTrackingDriver::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $resp->update($request->all());
        return response()->json($resp);
    }

    public function destroy($id)
    {
        // Delete a user
        $resp = MsTrackingDriver::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $resp->delete();
        return response()->json(['message' => 'User deleted']);
    }
}


