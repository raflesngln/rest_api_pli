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

class JobsDispacthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
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
        ->limit(10)
        ->get();



    //     // Apply ordering
    //     $query = JobsDispacth::query();
    //     $query->orderBy($order_by, $order_direction);
    //    // Apply pagination
    //    $items = $query->paginate((int)$per_page, ['*'], 'page', (int)$page);

        // return response()->json(['data'=>$items,'page'=>$page,'per_page'=>$per_page]);
        return response()->json(['data'=>$results,'page'=>$page,'per_page'=>$per_page]);
    }

    public function show($id)
    {
        // Retrieve a single user by ID
        $resp = JobsDispacth::find($id);

        if (!$resp) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($resp);
    }

}
