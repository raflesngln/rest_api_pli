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
        // $resp = MsTrackingTruck::find($id);
        $resp = MsTrackingTruck::where('sorting', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new MsTrackingTruckResource($resp);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sorting' => 'required|string|unique:ms_tracking_trucks',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
        }

        $ms_tracking = MsTrackingTruck::create([
            'sorting' => $request['sorting'],
            'title' => $request['title'],
            'description' => $request['description'],
        ]);

        $response = [
            'ms_tracking' => new MsTrackingTruckResource($ms_tracking), // Use the resource here
            'message' => 'Success create data',
        ];

        return response()->json($response, 201); // Return the response with a 201 status code
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $sorting = $input['sorting'];

        $check = MsTrackingTruck::where('sorting', $sorting)
            ->whereNotIn('sorting', [$id])
            ->get();
        // $check = MsTrackingTruck::where('sorting', $id)->first();


        if(count($check) > 0){
            return response()->json(['message'=>'Sudah ada yang punya index ini'],204);
        }else{
            // Update an existing user
            // $resp = MsTrackingTruck::find($id);
            $resp = MsTrackingTruck::where('sorting', $id)->first();

            if (!$resp) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $resp->update($request->all());
            return response()->json(['data'=>$resp, 'message' => 'success update data'],200);
        }
    }

    public function destroy($id)
    {
        // Delete a data
        $resp = MsTrackingTruck::where('sorting', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $resp->delete();
        return response()->json(['message' => 'User deleted'],200);
    }
}
