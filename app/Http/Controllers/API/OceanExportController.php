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
            ->select('*')
            ->where('job_shipment_status.email', '=', $email);
            // ->skip($offset)
            // ->take($per_page);
            if ($search !== '') {
                $query->where('job_shipment_status.shipper_name', 'like', "%".$search."%");
            }
            $results = $query->skip($offset)->take($per_page)->get();
            $resp = $query->get();
            $arr=[];
            foreach($resp as $row){
                $id_job=$row->id_job;
                $get_status = DB::table('tr_shipment_status')
                            ->select(
                                'group_name','tracking_name',
                                DB::raw('RIGHT(pid, 3) AS nomor_pid'),
                                DB::raw('RIGHT(id_tr_shipment_status, 3) AS nomor_id_shipment'), // If needed
                                'pid AS nomor_last'
                            )
                            ->where('id_job', $id_job) // Add the where clause
                            ->orderBy('created_datetime', 'desc')
                            ->orderBy('pid', 'desc')
                            ->limit(1)
                            ->first();
                $data=array(
                    'driver'=>$row->driver,
                    'driver_name'=>$row->driver_name,
                    'email'=>$row->email,
                    'container_number'=>$row->container_number,
                    'id_job'=>$row->id_job,
                    'do_number'=>$row->do_number,
                    'item_type'=>$row->item_type,
                    'pi_table'=>$row->pi_table,
                    'shipper_name'=>$row->shipper_name,
                    'address_1'=>$row->address_1,
                    'address_2'=>$row->address_2,
                    'iso_country'=>$row->iso_country,
                    'country_name'=>$row->country_name,
                    'state_name'=>$row->state_name,
                    'city_name'=>$row->city_name,
                    'subdistrict_name'=>$row->subdistrict_name,
                    'created_datetime'=>date('Y-m-d H:i:s'),
                    'village_name'=>$row->village_name,
                    'zip_code'=>$row->zip_code,
                    'scheduled_stuffing'=>$row->scheduled_stuffing,
                    'last_status'=>$get_status->tracking_name,
                    'group_name'=>$get_status->group_name,
                );
                $arr[]=$data;
            }

        // return response()->json(['data' => $resp, 'page' => $page, 'per_page' =>$per_page], 200);
        return response()->json(['data' => $arr, 'page' => $page, 'per_page' =>$per_page], 200);

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
            ->select('job_shipment_status.*');

            if ($id !== '') {
                $query->where('job_shipment_status.id_job', '=', $id);
            }
            $results = $query->first();
        $pi_table=$results->pi_table;
        $files_data = DB::table('ms_files')
        ->select('*')
        ->where('pi_table', '=', $pi_table)
        ->where('is_deleted', '=', 0)
        ->get();

        return response()->json(['data' => $results,'id_job'=>$id,'files'=>$files_data], 200);
    }
    public function tracking_status_ocean(Request $request)
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
            $id_job = $request->query('id_job','');
            $page = $request->query('page',1);
            $email = $request->query('email');
            $per_page = $request->query('per_page');
            $order_by = $request->query('order_by');
            $order_direction = $request->query('order_direction');
            $search = $request->query('search') ?? '';
            $offset = ($page - 1) * $per_page;

            $query = DB::table('tr_shipment_status')
            ->select('*')
            ->where('id_job', '=', $id_job)
            ->where('is_deleted', '=', 0);
            // ->skip($offset)
            // ->take($per_page);
            if ($search !== '') {
                $query->where('id_tracking', 'like', "%".$search."%");
            }
            $results = $query->skip($offset)->take($per_page)->get();
            // $results = $query->get();

        return response()->json(['data' => $results, 'page' => $page, 'per_page' =>$per_page], 200);
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
