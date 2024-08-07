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
    protected $OBS;
    // public function __construct(ObsStorageService $ObsstorageService)
    public function __construct(ObsStorageService $obs)
    {
        $this->middleware('auth:sanctum');
        $this->OBS = $obs;
        // $this->ObsstorageService = $ObsstorageService;
    }
/**
 * List Data Tracking Trucks
 * @return \Illuminate\Http\Response
*/
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer',
            'per_page' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400); // Return a 400 Bad Request response for validation errors
        }

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
/**
 * Detail Tracking Trucks
 * @return \Illuminate\Http\Response
*/
    public function show(int $id)
    {

        $resp = TrsTrackingTruck::where('id', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }
        return response()->json(['data' => $resp],200);
    }

/**
 * Progress of Tracking Dispatch
 * @return \Illuminate\Http\Response
*/
    public function tracking_progress(Request $request, $id)
    {
        $data = DB::table('ms_tracking_trucks as a')
            ->select('a.id as id_tracking', 'a.sorting', 'a.title', 'a.description', 'b.id as id_track', 'b.id_dispatch','b.created_by', 'b.title as title_track', 'b.tracking_date', 'b.description as desc_track', 'b.attachment', 'b.is_done', 'b.is_active', 'b.kilometer', 'b.created_at', 'b.updated_at')
            ->leftJoin('trs_tracking_trucks as b', function ($join) use ($id) {
                $join->on('a.id', '=', 'b.id_tracking')
                    ->where('b.id_dispatch', '=', $id);
            })
        ->orderBy('a.id', 'asc')
        ->get();

        $file='pli/prisma.png';
        $filebase64= 'aaa';//json_decode($this->OBS->getFileBase64($file));

        if (!$data) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }

        $arrData=[];
        foreach ($data as $key => $value) {
            $attachment=$value->attachment;
            $imageTrack= ($attachment)?json_decode($this->OBS->getFileBase64($attachment)):'';

            $items=array(
                "id_tracking"=> $value->id_tracking,
                "sorting"=> $value->sorting,
                "created_by"=> $value->created_by,
                "title"=> $value->title,
                "description"=> $value->description,
                "id_track"=> $value->id_track,
                "id_dispatch"=> $value->id_dispatch,
                "title_track"=> $value->title_track,
                "tracking_date"=> $value->tracking_date,
                "desc_track"=> $value->desc_track,
                "attachment"=> $value->attachment,
                "image"=> $imageTrack,
                "is_done"=> $value->is_done,
                "is_active"=> $value->is_active,
                "kilometer"=> $value->kilometer,
                "created_at"=> $value->created_at,
                "updated_at"=> $value->updated_at
            );
            $arrData[]=$items;
        }
        return response()->json(['data' => $arrData,'img'=>$data],200);
        // return response()->json(['data' => $data,'img'=>$filebase64],200);
    }


/**
 * New Data Tracking
 * @return \Illuminate\Http\Response
*/
    public function store(Request $request)
    {
        // echo 'okoko'; exit();

        $id_dispatch= $request['id_dispatch'];
        $id_tracking= $request['id_tracking'];

        $file = $request->file('attachment');
        // $file=base64_decode($file);
        // $fileName = $file->getClientOriginalName();
        $attachment = $request['attachment'];
        $attachment = base64_decode($attachment);

        $filename= 'track_'.$id_dispatch.'_'.date('YmdHis');

        // $upload=json_decode($this->OBS->uploadFile('pli/tracking/'.$filename, $file));
        $upload=json_decode($this->OBS->uploadFile('tracking-mobile/ocean/'.$filename, $file));
        // echo ( $upload->path_file);
        // exit();

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
        $upload=json_decode($this->OBS->uploadFile('tracking-mobile/ocean/'.$filename, $file)); // get response upload name path
        $row = TrsTrackingTruck::create([
            'id_dispatch' => $request['id_dispatch'],
            'id_tracking' => $request['id_tracking'],
            'tracking_date' => $request['tracking_date'],
            'title' => $request['title'],
            'created_by' => $request['created_by'],
            'kilometer' => isset($request['kilometer'])?$request['kilometer']:9,
            'description' => $request['description'],
            'attachment' =>$upload->path_file,
            'koli' =>$request->koli,
            'pic' =>$request->pic,
            'is_done' => $request['is_done'], // You can add this line if 'is_done' is a field in your table
            'is_active' => $request['is_active'], // You can add this line if 'is_active' is a field in your table
        ]);

        $response = [
            'data' => $row,
            'message' => 'Success create data',
        ];

        return response()->json(['data'=>$response,'file'=>$upload], 201);
    }

    /**
 * Update Data Tracking
 * @return \Illuminate\Http\Response
*/
    public function update(Request $request, $id)
    {
        $resp = TrsTrackingTruck::where('id', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'Tracking not found'], 404);
        }
        $resp->update($request->all());
        return response()->json(['data'=>$resp, 'message' => 'success update data'],200);
    }

    /**
 * DeleteTracking
 * @return \Illuminate\Http\Response
*/
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
