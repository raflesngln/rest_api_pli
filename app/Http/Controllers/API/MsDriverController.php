<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MsDriverResource;
use App\Models\MsDriver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MsDriverController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {

        // $page = $request->query('page');
        $page = $request->query('page', 1);
        $per_page = $request->query('per_page');
        $order_by = $request->query('order_by');
        $order_direction = $request->query('order_direction');

        // Apply ordering
        $query = MsDriver::query();
        $query->orderBy(isset($order_by) ? $order_by : 'id', isset($order_direction) ? $order_direction : 'asc');
        // Apply pagination
        $items = $query->paginate((int)$per_page, ['*'], 'page', (int)$page);

        $response = MsDriverResource::collection($items);

        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);
    }

    public function show($id)
    {

        // Retrieve a single user by ID
        $resp = MsDriver::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $response = MsDriverResource::collection($resp);
        return response()->json($response);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'driver_no'     => 'required|string|unique:ms_drivers',
            'driver_name'    => 'required|string',
            'is_active'    => 'required|integer',
            'is_deleted'    => 'required|integer',
            'driver_email'    => 'required|string|email|unique:ms_drivers',
            'driver_pass'    => 'required|string',
        ]);

        if ($validator->fails()) {
            echo $validator->messages()->toJson();
        } else {
            $driver = MsDriver::create([
                'driver_no' => $request['driver_no'],
                'driver_name' => $request['driver_name'],
                'driver_contact_number1' => $request['driver_contact_number1'],
                'driver_contact_number2' => $request['driver_contact_number2'],
                'is_active' => $request['is_active'],
                'is_deleted' => $request['is_deleted'],
                'ip' => $request['ip'],
                'create_by' => $request['create_by'],
                'vendor_id' => $request['vendor_id'],
                'driver_email' => $request['driver_email'],
                'driver_pass' => bcrypt($request['driver_pass'])
            ]);


            $response = [
                'driver' => $driver,
                'message' => 'Success create data'
            ];

            $responses = MsDriverResource::collection($response);

            return response($responses, 201);
        }
    }

    public function update(Request $request, $id)
    {
        // Update an existing user
        $resp = MsDriver::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $resp->update($request->all());
        $responses = MsDriverResource::collection($resp);
        return response()->json($responses);
    }

    public function destroy($id)
    {
        // Delete a user
        $resp = MsDriver::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $resp->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
