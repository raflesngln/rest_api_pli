<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JobsDispacth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\JobDispatchResource;

class JobsDispacthController extends Controller
{


    public function __construct()
    {
        // $this->middleware('auth:sanctum');
    }

    public function index_flc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page'     => 'required',
            'per_page'    => 'required',
        ]);

        if ($validator->fails()) {
            echo $validator->messages()->toJson();
        } else {
            $page = $request->query('page', 1);
            $per_page = $request->query('per_page');
            $order_by = $request->query('order_by');
            $order_direction = $request->query('order_direction');

            // $results = DB::table('ms_dispatch as a')
            //         ->leftJoin('ms_container_detail as d', 'a.id_container_detail', '=', 'd.id')
            //         ->leftJoin('ms_job_container as c', 'c.id_job_container', '=', 'd.id_job_container')
            //         ->leftJoin('ms_job as j', 'j.id_job', '=', 'c.id_job')
            //         ->select('j.id_job', 'j.customer_name', 'a.delivery_loc', 'a.driver', 'a.est_time', DB::raw('1 as koli'))
            //         ->where('j.moda_transport', 'TRUCK')
            //         ->where('j.cargo_type', 'FCL')
            //         ->groupBy('d.id')
            //         ->limit(10)
            //         ->get();
            $results = DB::table('ms_dispatch as a')
                ->leftJoin('ms_container_detail as d', 'a.id_container_detail', '=', 'd.id')
                ->leftJoin('ms_job_container as c', 'c.id_job_container', '=', 'd.id_job_container')
                ->leftJoin('ms_job as j', 'j.id_job', '=', 'c.id_job')
                ->select(
                    'd.id',
                    DB::raw('MAX(j.id_job) as id_job'),
                    DB::raw('MAX(j.customer_name) as customer_name'),
                    DB::raw('MAX(a.delivery_loc) as delivery_loc'),
                    DB::raw('MAX(a.driver) as driver'),
                    DB::raw('MAX(a.est_time) as est_time'),
                    DB::raw('COUNT(*) as koli')
                )
                ->where('j.moda_transport', 'TRUCK')
                ->where('j.cargo_type', 'FCL')
                ->groupBy('d.id')
                ->offset(($page - 1) * $per_page)
                ->limit($per_page)
                ->get();
        }
        $response = JobDispatchResource::collection($results);
        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);
    }
    public function index_lcl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page'     => 'required',
            'per_page'    => 'required',
        ]);

        if ($validator->fails()) {
            echo $validator->messages()->toJson();
        } else {
            $page = $request->query('page', 1);
            $per_page = $request->query('per_page');
            $order_by = $request->query('order_by');
            $order_direction = $request->query('order_direction');

            $results = DB::table('ms_dispatch as a')
                ->leftJoin('ms_job_volume as v', 'a.id_volume', '=', 'v.id_volume')
                ->leftJoin('ms_job as j', 'j.id_job', '=', 'v.id_job')
                ->select(
                    DB::raw('MAX(a.id) as id'),
                    DB::raw('MAX(a.id_volume) as id_volume'),
                    DB::raw('MAX(j.customer_name) as customer_name'),
                    DB::raw('MAX(a.delivery_loc) as delivery_loc'),
                    DB::raw('MAX(a.driver) as driver'),
                    DB::raw('MAX(a.est_time) as est_time'),
                    DB::raw('COUNT(*) as koli')
                )
                ->where('j.moda_transport', 'TRUCK')
                ->where('j.cargo_type', 'LCL')
                ->groupBy('v.id_volume')
                ->offset(($page - 1) * $per_page)
                ->limit($per_page)
                ->get();
            $response = JobDispatchResource::collection($results);
        }
        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);
    }

    public function show_fcl($id)
    {

        $results = DB::table('ms_dispatch as a')
            ->leftJoin('ms_container_detail as d', 'a.id_container_detail', '=', 'd.id')
            ->leftJoin('ms_job_container as c', 'c.id_job_container', '=', 'd.id_job_container')
            ->leftJoin('ms_job as j', 'j.id_job', '=', 'c.id_job')
            ->select(
                'd.id',
                DB::raw('MAX(j.id_job) as id_job'),
                DB::raw('MAX(j.customer_name) as customer_name'),
                DB::raw('MAX(a.delivery_loc) as delivery_loc'),
                DB::raw('MAX(a.driver) as driver'),
                DB::raw('MAX(a.est_time) as est_time'),
                DB::raw('COUNT(*) as koli')
            )
            ->where('j.moda_transport', 'TRUCK')
            ->where('j.cargo_type', 'FCL')
            ->where('d.id', $id)
            ->groupBy('d.id')
            ->limit(10)
            ->get();

        $response = JobDispatchResource::collection($results);
        return response()->json(['data' => $response, 'id' => $id,]);
    }
    public function show_lcl($id)
    {
        $results = DB::table('ms_dispatch as a')
            ->leftJoin('ms_job_volume as v', 'a.id_volume', '=', 'v.id_volume')
            ->leftJoin('ms_job as j', 'j.id_job', '=', 'v.id_job')
            ->select(
                DB::raw('MAX(a.id) as id'),
                DB::raw('MAX(a.id_volume) as id_volume'),
                DB::raw('MAX(j.customer_name) as customer_name'),
                DB::raw('MAX(a.delivery_loc) as delivery_loc'),
                DB::raw('MAX(a.driver) as driver'),
                DB::raw('MAX(a.est_time) as est_time'),
                DB::raw('COUNT(*) as koli')
            )
            ->where('j.moda_transport', 'TRUCK')
            ->where('j.cargo_type', 'LCL')
            ->where('a.id', $id)
            ->groupBy('v.id_volume')
            ->get();
        $response = JobDispatchResource::collection($results);
        return response()->json(['data' => $response, 'id' => $id,]);
    }
    // public function show($id)
    // {
    //     // Retrieve a single user by ID
    //     $resp = JobsDispacth::find($id);

    //     if (!$resp) {
    //         return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     return response()->json($resp);
    // }

}
