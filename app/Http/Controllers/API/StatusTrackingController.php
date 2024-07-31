<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\StatusTrackingJobResource;
use App\Models\TrsTrackingTruck;
use App\Models\MsJobStatusTracking;
use Illuminate\Support\Facades\DB;

class StatusTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // return response()->json($request, 201);
        // exit();

    $cek_id = DB::table('tr_shipment_status')
        ->select(
            DB::raw('RIGHT(pid, 3) AS nomor_pid'),
            DB::raw('RIGHT(id_tr_shipment_status, 3) AS nomor_id_shipment'), // This seems like a duplicate selection, you might want to remove one
            'pid AS nomor_last'
        )
    ->orderBy('created_datetime', 'desc')
    ->orderBy('pid', 'desc')
    ->limit(1)
    ->first();
    $pid_last = str_pad((int)$cek_id->nomor_pid + 1, strlen($cek_id->nomor_pid), '0', STR_PAD_LEFT);
    $new_pid="TSS01".date('YmdHis').$pid_last;
    $id_shipment_last = str_pad((int)$cek_id->nomor_id_shipment + 1, strlen($cek_id->nomor_id_shipment), '0', STR_PAD_LEFT);
    $new_id_shipment="TSS01".date('Ymd').$id_shipment_last;


        // $status_name= $request['status_name'];
        $validator = Validator::make($request->all(), [
            'group_name' => 'nullable|string', // Assuming it's optional
            'id_tracking' => 'required|string',
            'status_name' => 'nullable|string',
            'tracking_name' => 'nullable|string',
            'id_job' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
        }

        $data = MsJobStatusTracking::create([
            'pid' =>$new_pid,
            'id_tr_shipment_status' => $new_id_shipment,
            'id_job'=>$request['id_job'],
            'id_group_shipment_status'=>$request['id_group_shipment_status'],
            'group_name'=>$request['group_name'],
            'tracking_name'=>$request['tracking_name'],
            'tracking_order'=>$request['tracking_order'],
            'tracking_level'=>$request['tracking_level'],
            'moda_transport'=>$request['moda_transport'],
            'primary_id'=>$request['primary_id'],
            'id_tracking'=>$request['id_tracking'],
            'color_status'=>$request['color_status'],
            'status_name'=>$request['status_name'],
            'icon_name'=>$request['icon_name'],
            'created_by'=>$request['created_by'],
            'created_datetime'=>date('Y-m-d H:i:s'),
            'modified_by'=>$request['modified_by'],
            'status_code'=>$request['status_code'],
            'is_publish'=>$request['is_publish'],
            'additional'=>$request['additional'],
            'bc20'=>$request['bc20'],
            'bc23'=>$request['bc23'],
            'rh'=>$request['rh'],
            'order'=>$request['order'],
            'pibk'=>$request['pibk'],
            'level'=>$request['level'],
            'is_active' => 1,
            'is_deleted' => 0,
        ]);

        $response = [
            'data' => new $data, // Use the resource here
            'message' => 'Success create data',
        ];

        return response()->json(['data' => $response], 201);

    }

    public function ms_tracking_status(Request $request)
    {
        $search = $request->query('search') ?? '';
        $id_job = $request->query('id_job', '');
        $results = DB::table('ms_tracking as a')
            ->select('b.is_active as is_active_status','b.pid as pid_status','b.id_job','b.is_active as status_active_tracking', 'a.*')
            ->leftJoin('tr_shipment_status as b', function($join) use ($id_job) {
                $join->on('a.id_tracking', '=', 'b.id_tracking');

                // Check if $id_job is not empty, then apply the where condition
                if ($id_job !== '') {
                    $join->where('b.id_job', '=', $id_job);
                    $join->where('b.is_active', '=', 1);
                }
            })
            ->get();
        return response()->json(['data' => $results], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $file='pli/prisma.png';
        // $filebase64= json_decode($this->ObsstorageService->getFileBase64($file));

        // Retrieve a single user by ID
        $resp = MsJobStatusTracking::where('pid', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        return response()->json(['data' => $resp],200);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {




            // Find the record by id
            $record = MsJobStatusTracking::where('pid', $id)->first();
            // Check if the record exists
            if (!$record) {
                return response()->json(['message' => 'Record not found'], 404);
            }
            $record->update($request->all());
            return response()->json(['data'=>$record,'message'=>'success update data'],200);

            // Validate inputs
            // $request->validate([
            //     'tracking_name' => 'required|string',
            //     'status_name' => 'required|string',
            //     'is_active' => 'required|integer',
            // ]);
            // // Update the record with validated inputs
            // $record->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
