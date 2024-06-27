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
