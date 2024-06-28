<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\StatusTrackingJob;
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

        $status_name= $request['status_name'];
        $validator = Validator::make($request->all(), [
            'pid' => 'required|string',
            'id_job' => 'required|string',
            'id_tracking' => 'nullable|string', // Assuming it's optional
            'moda_transport' => 'required|string',
            'status_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
        }

        $driver = MsJobStatusTracking::create([
            'pid' => $request['pid'],
            'id_tr_shipment_status' => $request['id_tr_shipment_status'],
            'id_group_shipment_status' => $request['id_group_shipment_status'],
            'id_job' => $request['id_job'],
            'tracking_name' => $request['tracking_name'],
            'moda_transport' => $request['moda_transport'],
            'primary_id' => $request['primary_id'],
        ]);

        $response = [
            'driver' => new StatusTrackingJob($driver), // Use the resource here
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
