<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MsTrackingTruckResource;
use App\Models\MsTrackingTruck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MsTrackingTruckController extends Controller
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
        $query = MsTrackingTruck::query();
        $query->orderBy(isset($order_by) ? $order_by : 'id', isset($order_direction) ? $order_direction : 'asc');
        // Apply pagination
        $items = $query->paginate((int)$per_page, ['*'], 'page', (int)$page);

        $response = MsTrackingTruckResource::collection($items);
        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);
    }

    public function show($id)
    {
        // Retrieve a single user by ID
        $resp = MsTrackingTruck::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new MsTrackingTruck($resp);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'sorting'     => 'required|string|unique:ms_tracking_trucks',
            'title'    => 'required|string|unique:ms_tracking_trucks',
            'description'    => 'required|string'
        ]);

        if ($validator->fails()) {
            echo $validator->messages()->toJson();
        } else {
            $driver = MsTrackingTruck::create([
                'sorting' => $request['sorting'],
                'title' => $request['title'],
                'description' => $request['description'],
            ]);


            $response = [
                'driver' => $driver,
                'message' => 'Success create data'
            ];
            $responses = MsTrackingTruckResource::collection($response);
            return response($responses, 201);
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $sorting = $input['sorting'];

        $check = MsTrackingTruck::where('sorting', $sorting)
            ->whereNotIn('id', [$id])
            ->get();


        if (count($check) > 0) {
            return response()->json(['message' => 'Sudah ada yang punya index ini']);
        } else {
            // Update an existing user
            $resp = MsTrackingTruck::find($id);

            if (!$resp) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $resp->update($request->all());
            return new MsTrackingTruck($resp);
        }
    }

    public function destroy($id)
    {
        // Delete a user
        $resp = MsTrackingTruck::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $resp->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
