<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrsTrackingTruckResource;
use App\Models\TrsTrackingTruck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\ObsStorageService;


class TrsTrackingTruckController extends Controller
{
    protected $ObsstorageService;
    public function __construct(ObsStorageService $ObsstorageService)
    {
        $this->middleware('auth:sanctum');
        $this->ObsstorageService = $ObsstorageService;
    }

    public function index(Request $request)
    {

        $page = $request->query('page');
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

    public function show(int $id)
    {

        $resp = TrsTrackingTruck::where('id', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }
        return response()->json(['data' => $resp],200);
    }
    public function tracking_progress(string|int $id_dispatch)
    {
        $data = DB::table('ms_tracking_trucks as a')
                ->select('a.id as id_tracking', 'a.sorting', 'a.title', 'a.description', 'b.*')
                ->leftJoin('trs_tracking_trucks as b', 'a.id', '=', 'b.id_tracking')
                ->where(function ($query) {
                    $query->where('b.id_dispatch', '=', '')
                        ->orWhereNull('b.id_dispatch');
                })
                ->orderBy('a.id')
                ->get();

        if (!$data) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }
        return response()->json(['data' => $data],200);
    }

    public function store(Request $request)
    {
        $id_dispatch= $request['id_dispatch'];
        $id_tracking= $request['id_tracking'];

        $file = $request->file('attachment');
        // $file=base64_decode($file);
        $fileName = $file->getClientOriginalName();
        $attachment = $request['attachment'];
        $attachment = base64_decode($attachment);

        $filename= $id_dispatch.'_'.date('YmdHis');

        // Check if id_dispatch and id tracking is exists not save
        $checkExists = DB::table('trs_tracking_trucks')
                    ->where('id_dispatch', '=', $id_dispatch)
                    ->where('id_tracking', '=', $id_tracking)
                    ->get();
        if(count($checkExists) > 0){
            return response()->json(['data'=>[],'message'=>'Tracking with id dispatch an did dispatch has exists'], 404);
        }
        $validator = Validator::make($request->all(), [
            'id_dispatch'     => 'required|string',
            'id_tracking'    => 'required|string',
            'tracking_date'    => 'required',
            'title'    => 'required|string',
            'description'    => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
        }
        $upload=json_decode($this->ObsstorageService->uploadFile('pli/tracking/'.$filename, $file));

        $row = TrsTrackingTruck::create([
            'id_dispatch' => $request['id_dispatch'],
            'id_tracking' => $request['id_tracking'],
            'tracking_date' => $request['tracking_date'],
            'title' => $request['title'],
            'kilometer' => isset($request['kilometer'])?$request['kilometer']:9,
            'description' => $request['description'],
            'attachment' =>$filename.'.png',
            'is_done' => $request['is_done'], // You can add this line if 'is_done' is a field in your table
            'is_active' => $request['is_active'], // You can add this line if 'is_active' is a field in your table
        ]);

        $response = [
            'data' => $row,
            'message' => 'Success create data',
        ];

        return response()->json(['data'=>$response,'file'=>'upload'], 201);
    }

    public function update(Request $request, $id)
    {
        $resp = TrsTrackingTruck::where('id', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }
        $resp->update($request->all());
        return response()->json(['data'=>$resp, 'message' => 'success update data'],200);
    }

    public function destroy($id)
    {
        // Delete a user
        $resp = TrsTrackingTruck::find($id);

        if (!$resp) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }

        $resp->delete();
        return response()->json(['message' => 'Tracking deleted']);
    }
}
