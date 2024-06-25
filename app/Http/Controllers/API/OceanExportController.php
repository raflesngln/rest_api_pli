<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OceanExportResource;
use App\Models\TrsTrackingTruck;
use App\Models\OceanExport;

class OceanExportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // select dp.driver, dr.driver_name, cdtl.container_number, mcnt.id_job, mjb.do_number,mjb.customer_name,mjb.description AS item_type
        // from ms_dispatch as dp
        // LEFT JOIN ms_driver as dr on dp.driver=dr.driver_no
        // LEFT JOIN ms_container_detail as cdtl on dp.id_container_detail=cdtl.id
        // LEFT JOIN ms_job_container as mcnt on cdtl.id_job_container=mcnt.id_job_container
        // LEFT JOIN ms_job as mjb on mcnt.id_job=mjb.id_job
        // WHERE dr.email=''
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
        $query = OceanExport::query();
        $query->orderBy(isset($order_by) ? $order_by : 'id_job', isset($order_direction) ? $order_direction : 'asc');
        // Apply pagination
        $items = $query->paginate((int)$per_page, ['*'], 'page', (int)$page);
        $response = OceanExportResource::collection($items);
        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);

    }
    public function fetchDispatches(Request $request)
    {
        // Default values if parameters are not provided
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $orderBy = $request->input('order_by', 'driver_name'); // Default column to order by
        $orderDirection = $request->input('order_direction', 'asc'); // Default order direction

        $query = OceanExport::with([
            'driver',
            'containerDetail.jobContainer.job'
        ])->whereHas('driver', function ($query) {
            $query->where('email', '');
        });

        // Apply ordering
        $query->orderBy($orderBy, $orderDirection);

        // Pagination
        $dispatches = $query->paginate($perPage, ['*'], 'page', $page);

        return OceanExportResource::collection($dispatches);
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
