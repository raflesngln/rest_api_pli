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
            $page = $request->query('page',1);
            $email = $request->query('email');
            $per_page = $request->query('per_page');
            $order_by = $request->query('order_by');
            $order_direction = $request->query('order_direction');
            $search = $request->query('search') ?? '';
            $offset = ($page - 1) * $per_page;

            $query = DB::table('job_shipment_status')
            ->select('job_shipment_status.*', DB::raw('1 as koli'))
            ->where('job_shipment_status.email', '=', $email);
            // ->skip($offset)
            // ->take($per_page);
            if ($search !== '') {
                $query->where('job_shipment_status.shipper_name', 'like', "%".$search."");
            }
            $results = $query->skip($offset)->take($per_page)->get();
            // $results = $query->get();

        return response()->json(['data' => $results, 'page' => $page, 'per_page' =>$per_page], 200);

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
    public function show(Request $request, string $id)
    {

            $query = DB::table('job_shipment_status')
            ->select('job_shipment_status.*', DB::raw('1 as koli'));

            if ($id !== '') {
                $query->where('job_shipment_status.id_job', '=', $id);
            }
            $results = $query->get();

        return response()->json(['data' => $results,'id_job'=>$id], 200);
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
