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

        // $status_name= $request['status_name'];
        $validator = Validator::make($request->all(), [
            'pid' => 'required|string',
            'id_tr_shipment_status' => 'required|string',
            'group_name' => 'nullable|string', // Assuming it's optional
            'id_tracking' => 'required|string',
            'status_name' => 'required|string',
            'tracking_name' => 'required|string',
            'id_job' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
        }

        $driver = MsJobStatusTracking::create([
            'pid' => $request['pid'],
            'id_tr_shipment_status' => $request['id_tr_shipment_status'],
            'group_name' => $request['group_name'],
            'id_tracking' => $request['id_tracking'],
            'tracking_name' => $request['tracking_name'],
            'tracking_order' => $request['tracking_order'],
            'tracking_level' => $request['tracking_level'],
            'id_job' => $request['id_job'],
            'additional' => 'lorem',
            'color_status' => 'red',
            'table_code' => 'TSS01',
            'created_by' => 'rafles',
            'is_active' => 1,
            'is_deleted' => 0,
        ]);

        $response = [
            'driver' => new StatusTrackingJobResource($driver), // Use the resource here
            'message' => 'Success create data',
        ];

        return response()->json(['data' => $response], 201);

    }

    public function ms_tracking_status(Request $request)
    {
        $search = $request->query('search') ?? '';
        $id_job = $request->query('id_job', '');
        $results = DB::table('ms_tracking as a')
            ->select('b.id_job', 'a.*')
            ->leftJoin('tr_shipment_status as b', function($join) use ($id_job) {
                $join->on('a.id_tracking', '=', 'b.id_tracking');

                // Check if $id_job is not empty, then apply the where condition
                if ($id_job !== '') {
                    $join->where('b.id_job', '=', $id_job);
                }
            })
            ->get();
        return response()->json(['data' => $results], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {

        /*
            $resp = MsJobStatusTracking::where('pid', $id)->first();
            if (!$resp) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $resp->update($request->all());
            return response()->json(['data'=>$resp,'message'=>'success update data'],200);
        */

            // Find the record by id
            $record = MsJobStatusTracking::find($id);

            // Check if the record exists
            if (!$record) {
                return response()->json(['message' => 'Record not found'], 404);
            }

            // Validate inputs
            $request->validate([
                'id_job' => 'required|string',
                'tracking_name' => 'required|string',
            ]);

            // Update the record with validated inputs
            $record->update($request->all());
            return response()->json(['data'=>$record,'message'=>'success update data'],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
