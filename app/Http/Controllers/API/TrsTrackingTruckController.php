<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrsTrackingTruckResource;
use App\Models\TrsTrackingTruck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TrsTrackingTruckController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {

        $page = $request->query('page', 1);
        $per_page = $request->query('per_page');
        $order_by = $request->query('order_by');
        $order_direction = $request->query('order_direction');

        // Apply ordering
        $query = TrsTrackingTruck::query();
        $query->orderBy(isset($order_by) ? $order_by : 'id', isset($order_direction) ? $order_direction : 'asc');
        // Apply pagination
        $items = $query->paginate((int)$per_page, ['*'], 'page', (int)$page);
        $response = TrsTrackingTruckResource::collection($items);
        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);
    }

    public function show($id)
    {

        // Retrieve a single user by ID
        $resp = TrsTrackingTruck::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($resp);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_dispacth'     => 'required|string',
            'id_tracking'    => 'required|int',
            'tracking_date'    => 'required',
            'title'    => 'required|string',
            'description'    => 'required|string',
            'attachment'    => 'required|string'
        ]);

        if ($validator->fails()) {
            echo $validator->messages()->toJson();
        } else {
            $row = TrsTrackingTruck::create([
                'id_dispacth' => $request['id_dispacth'],
                'id_tracking' => $request['id_tracking'],
                'tracking_date' => $request['tracking_date'],
                'title' => $request['title'],
                'description' => $request['description'],
                'attachment' => $request['attachment'],
                'is_done' => $request['is_done'],
                'is_active' => $request['is_active'],
            ]);


            $response = [
                'data' => $row,
                'message' => 'Success create data'
            ];
            $responses = TrsTrackingTruckResource::collection($response);
            return response($responses, 201);
        }
    }

    public function update(Request $request, $id)
    {
        // Update an existing user
        $resp = TrsTrackingTruck::find($id);

        if (!$resp) {
            return response()->json(['message' => 'Tracking not found'], Response::HTTP_NOT_FOUND);
        }

        $resp->update($request->all());
        return response()->json($resp);
    }

    public function destroy($id)
    {
        // Delete a user
        $resp = TrsTrackingTruck::find($id);

        if (!$resp) {
            return response()->json(['message' => 'Tracking not found'], Response::HTTP_NOT_FOUND);
        }

        $resp->delete();
        return response()->json(['message' => 'Tracking deleted']);
    }
}
