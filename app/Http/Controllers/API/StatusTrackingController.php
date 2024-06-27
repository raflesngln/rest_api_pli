<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OceanExportResource;
use App\Models\TrsTrackingTruck;
use App\Models\OceanExport;
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
        //
    }

    public function ms_tracking_status(Request $request)
    {


            $id_job = $request->query('id_job','');
            $page = $request->query('page',1);
            $email = $request->query('email');
            $per_page = $request->query('per_page');
            $order_by = $request->query('order_by');
            $order_direction = $request->query('order_direction');
            $search = $request->query('search') ?? '';
            $offset = ($page - 1) * $per_page;

            $jobId = 'P-02202406030000';

            $results = DB::table('ms_tracking as a')
            ->select('b.id_job', 'a.*')
            ->leftJoin('tr_shipment_status as b', function($join) {
                $join->on('a.id_tracking', '=', 'b.id_tracking')
                     ->where('b.id_job', '=', 'P-02202405170005');
            })
            ->get();

            // $query = DB::table('ms_tracking')
            // ->select('*');

            // $results = $query->get();

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
