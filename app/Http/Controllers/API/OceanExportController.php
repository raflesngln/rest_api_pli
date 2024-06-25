<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OceanExportResource;
use App\Models\TrsTrackingTruck;
use App\Models\OceanExport;
use Illuminate\Support\Facades\DB;

class OceanExportController extends Controller
{
    /**
     * Display a listing of the resource.
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
            $email = $request->query('email');
            $per_page = $request->query('per_page');
            $order_by = $request->query('order_by');
            $order_direction = $request->query('order_direction');

            $query = DB::table('job_shipment_status')
            ->select('job_shipment_status.*', DB::raw('1 as koli'))


            // ->leftJoin('ms_driver', 'mdp.driver', '=', 'ms_driver.driver_no')
            // ->leftJoin('ms_container_detail as mjobcd', 'mdp.id_container_detail', '=', 'mjobcd.id')
            // ->leftJoin('ms_job_container as mjobc', 'mjobcd.id_job_container', '=', 'mjobc.id_job_container')
            // ->leftJoin('ms_job', 'mjobc.id_job', '=', 'ms_job.id_job')
            // ->leftJoin('ms_shipper_consignee as mscon', 'mscon.id_shipper_consignee', '=', 'ms_job.id_shipper')
            ->where('job_shipment_status.email', '=', $email)
            // ->groupBy('ms_job.id_job')
            ->limit(10);

            $results = $query->get();

        return response()->json(['data' => $results, 'page' => $page, 'per_page' => $per_page], 200);

    }
    public function fetchDispatches(Request $request)
    {
        // Default values if parameters are not provided
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $orderBy = $request->input('order_by', 'driver_name'); // Default column to order by
        $orderDirection = $request->input('order_direction', 'asc'); // Default order direction
        $email = $request->query('email','');


        if (!isset($email)) {
            // Handle the case where `email` is not present in the query string
            return response()->json(['error' => 'Missing email parameter'], 400); // Or redirect, use default value, etc.
        }

        $query = OceanExport::with([
            'driver',
            'containerDetail.jobContainer.job'
        ]);
        if ($email !== '') {
            $query->whereHas('driver', function ($query) use ($email) {
                $query->where('email', $email);
            });
        }


        // Apply ordering
        $query->orderBy($orderBy, $orderDirection);

        // Pagination
        $dispatches = $query->paginate($perPage, ['*'], 'page', $page);

        // return OceanExportResource::collection($dispatches);
        $dispatches = OceanExportResource::collection($dispatches);
        // Add variable data as a property to the collection
        $dispatches->additional = ['email' => $email,'page'=>$page];
        return $dispatches;

    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
