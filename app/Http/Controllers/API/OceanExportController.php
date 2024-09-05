<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OceanExportResource;
use App\Models\TrsTrackingTruck;
use App\Models\OceanExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OceanExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        date_default_timezone_set('Asia/Jakarta');
    }
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
            $head_driver = $request->query('head_driver',0);
            $email = $request->query('email');
            $per_page = $request->query('per_page');
            $order_by = $request->query('order_by');
            $order_direction = $request->query('order_direction');
            $search = $request->query('search') ?? '';
            $offset = ($page - 1) * $per_page;

            $query = DB::table('job_shipment_status')
            ->select('*');
            // ->where('job_shipment_status.email', '=', $email);
            // ->skip($offset)
            // ->take($per_page);
            if ($search !== '') {
                $query->where('job_shipment_status.do_number', 'like', "%".$search."%");
                
                $query->where('job_shipment_status.do_number', 'like', $search."%")
                    ->orWhere('job_shipment_status.shipper_name', 'like', $search."%")
                    ->orWhere('job_shipment_status.id_job', 'like',$search."%");
            }
            if ($head_driver == '0') {
                $query->where('job_shipment_status.email', '=', $email);
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
                            ->where('is_deleted', 0)
                            ->orderBy('created_datetime', 'desc')
                            ->orderBy('pid', 'desc')
                            ->limit(1)
                            ->first();
                // check status GATE IN CY, if exists then get created datetime for count down
                $check_status_gcy = DB::table('tr_shipment_status as a')
                                    ->join('ms_tracking as b', 'a.id_tracking', '=', 'b.id_tracking')
                                    ->select('b.code', 'a.created_datetime', 'a.id_job')
                                    ->where('a.id_job', $id_job)
                                    ->where('a.is_deleted', 0)
                                    ->where('b.code', 'GCY')
                                    ->first();
                    
                $data=array(
                    'driver'=>$row->driver,
                    'driver_name'=>$row->driver_name,
                    'email'=>$row->email,
                    'container_number'=>$row->container_number,
                    'seal_number'=>$row->seal_number,
                    'id_job'=>$row->id_job,
                    'do_number'=>$row->do_number,
                    'pickup_loc'=>$row->pickup_loc,
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
                    'last_status'=>$get_status?$get_status->tracking_name:'Job Baru',
                    'group_name'=>$get_status?$get_status->group_name:'',
                    'done_tracking'=>$check_status_gcy?$check_status_gcy->created_datetime:''
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
            $data_files=[];
            foreach($files_data as $row){
                $fileName=$row->file_name;
                // $pi_table=$row->pi_table;
                $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                    'tracking-mobile/ocean/' . $pi_table . '/' . $fileName, 
                    now()->addMinutes(5)
                );

                $list=array(
                    'pid'=>$row->pid,
                    'pi_table'=>$row->pi_table,
                    'id_file'=>$row->id_file,
                    'file_name'=>$row->file_name,
                    'attachment'=>$temporaryUrl,
                    'modul'=>$row->modul,
                    'subject'=>$row->subject,
                    'created_by'=>$row->created_by,
                    'created_datetime'=>$row->created_datetime,
                    'is_active'=>$row->is_active,
                    'description'=>$row->description
                );
                $data_files[]=$list;

            }

        return response()->json(['data' => $results,'id_job'=>$id,'files'=>$data_files], 200);
    }
    public function show_test(Request $request, string $id)
    {
        $myfile='tracking-mobile/ocean/TSS0220240626000000001/file_135911318646.png';
        // $getfile = Storage::disk('s3')->get($myfile);
        // $mimeType = Storage::disk('s3')->mimeType($myfile);
        // $base64 = base64_encode($getfile);
        // return json_encode(['base64'=>'data:'.$mimeType.';base64,'.$base64,'type'=>'JPG']);
        // exit();
        // $mimeToExtension = [
        //     'image/jpeg' => 'jpg',
        //     'image/png' => 'png',
        //     'application/pdf' => 'pdf',
        //  ];
        //  $extension = $mimeToExtension[$mimeType] ?? 'unknown';

        // exit();

        // $attachFIle= ($attachment)?json_decode($this->OBS->getFileBase64($fileAttachment)):'';
    
        // return response()->json(['data' => $attachFIle,200);

        // $attachment="file_135911318646.png";
        // $pi_table="TSS0220240626000000001";
    
        // $fileUrl = Storage::disk('s3')->url('tracking-mobile/ocean/'.$pi_table.'/'.$attachment);


        // return response()->json(['data' => $fileUrl], 200);
        // // return response()->json(['data' => $fileUrl], 200);
        try {
            $fileName = "file_142425207366.png";
            $pi_table = "TSS0220240520000000001";
            $contents = Storage::disk('obs')->get("tracking-mobile/ocean/TSS0220240626000000001/file_135911318646.png");
        
            // Generate the file URL
            // $fileUrl = Storage::disk('s3')->url('tracking-mobile/ocean/'.$pi_table.'/'.$fileName);
            // $temporaryUrl = Storage::disk('s3')->get(
            //     'tracking-mobile/ocean/TSS0220240626000000001/' . $fileName, now()->addMinutes(5)
            // );

            print_r('contents');

            // echo $temporaryUrl;
            // echo '<img src="'.$temporaryUrl.'" alt="Image" />';
        
            // Return the file URL in the JSON response
            // return response()->json(['data' => $fileUrl], 200);
        } catch (\Exception $e) {
            // Handle any errors that might occur
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
            ->where('is_deleted', '=', 0)
            ->orderBy('created_datetime', 'desc');




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
