<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MsFilesResource;
use App\Models\MsFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\ObsStorageService;
use Illuminate\Support\Facades\Storage;


class MsFilesController extends Controller
{
    protected $OBS;

    /**
     * Display a listing of the resource.
     */
    public function __construct(ObsStorageService $obs)
    {
        $this->middleware('auth:sanctum');
        $this->OBS = $obs;
        // $this->ObsstorageService = $ObsstorageService;
    }
     public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required:integer',
            'per_page' => 'required:integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400); // Return a 400 Bad Request response for validation errors
        }

        // $page = $request->query('page');
        $page = $request->query('page');
        $per_page = $request->query('per_page');
        $order_by = $request->query('order_by');
        $order_direction = $request->query('order_direction');

        // Apply ordering
        $query = MsFiles::query();
        $query->orderBy(isset($order_by) ? $order_by : 'pid', isset($order_direction) ? $order_direction : 'asc');
        // Apply pagination
        $items = $query->paginate((int)$per_page, ['*'], 'page', (int)$page);

        $response = MsFilesResource::collection($items);

        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $fileData = $request->file('attachment');
        // // $file=base64_decode($file);
        // // $fileName = $file->getClientOriginalName();
        // $attachment = $request['attachment'];
        // $attachment = base64_decode($attachment);
        // $filename= $request['pi_table'].'-'.date('YmdHis');
        // // $upload=json_decode($this->OBS->uploadFile('pli/tracking_jobs/'.$filename, $fileData)); // get response upload name path

        // $newFileName = 'raflesian.jpg'; // Set the new file name
        // $savePath = 'pli/tracking_jobs/' . $newFileName;
        // $save = Storage::disk('s3')->put($savePath, $fileData);
        $fileData = $request->file('attachment'); // Get the uploaded file from the request
        $attachment = $request['attachment'];
        $attachment = base64_decode($attachment);
        $filename = $request['modul'] . '-' . date('YmdHis').'.jpg';
        // $newFileName = 'raflesian.jpg'; // Set the new file name
        $newPath = $request['pi_table']; // Set the new file name
        // Store the uploaded file in the specified path on S3 with the new file name
        // return response()->json(['messsage' =>'attachment'], 400); // Return validation errors as JSON
        // exit();


        $cek_id = DB::table('ms_files')
                ->select(
                    DB::raw('RIGHT(pid, 3) AS nomor_pid'),
                    'pid AS nomor_last'
                )
            ->orderBy('created_datetime', 'desc')
            ->orderBy('pid', 'desc')
            ->limit(1)
            ->first();
            $pid_last = str_pad((int)$cek_id->nomor_pid + 1, strlen($cek_id->nomor_pid), '0', STR_PAD_LEFT);
            $new_pid="FL001".date('YmdHis').$pid_last;
            $id_file="FL001".date('Ymd').$pid_last;


        $validator = Validator::make($request->all(), [
            'modul' => 'required|string',
            'pi_table' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
        }

        $ms_files = MsFiles::create([
            'pid'=>$new_pid,
            'modul'=>$request['modul'],
            'pi_table'=>$request['pi_table'],
            // 'id_file'=>$id_file,
            'id_file'=>'FLOOO002',
            'file_name'=>$filename,
            'subject'=>$request['subject'],
            'description'=>$request['description'],
            'extension'=>$request['extension'],
            'created_by'=>$request['created_by'],
            'created_datetime'=>$request['created_datetime'],
            'created_ip_address'=>$request['created_ip_address'],
            'created_by_browser'=>$request['created_by_browser'],
            'modified_by'=>$request['modified_by'],
            'modified_datetime'=>$request['modified_datetime'],
            'modified_ip_address'=>$request['modified_ip_address'],
            'modified_browser'=>$request['modified_browser'],
            'is_active'=>1,
            'is_deleted'=>0,
            'table_code'=>'FL001',
            'expired_date'=>date('Y-m-d'),
            'dept'=>$request['dept']
        ]);
        $save = Storage::disk('s3')->putFileAs('pli/tracking_jobs/'.$newPath, $fileData, $filename,['ACL' => 'public-read']);
        // json_decode($this->OBS->uploadFile('pli/tracking_jobs/'.$filename, $fileData)); // get response upload name path
        $response = [
            // 'ms_files' => new MsFilesResource($ms_files), // Use the resource here
            'ms_files' => new MsFilesResource($ms_files), // Use the resource here
            'message' => 'Success create data',
            // 'save'=>$save
        ];

        return response()->json(['data'=>$response,'file'=>'upload'], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $file='pli/prisma.png';
        // $filebase64= json_decode($this->ObsstorageService->getFileBase64($file));


        // Retrieve a single user by ID
        $resp = MsFiles::where('pid', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'Files not found'], 404);
        }

        return response()->json(['data' => $resp,'file'=>$file],200);
        // return response()->json(['data' => $resp,'file'=>$filebase64],200);
    }
    public function files_job($pi_table)
    {
        $file='pli/prisma.png';
        // $filebase64= json_decode($this->ObsstorageService->getFileBase64($file));


        // Retrieve a single user by ID
        $resp = MsFiles::where('pi_table', $pi_table)->get();

        if (!$resp) {
            return response()->json(['message' => 'Files not found'], 404);
        }
        $arrData=[];
        foreach ($resp as $key => $value) {
            $attachment=$value->file_name;
            $pi_table=$value->pi_table;
            $fileAttachment='pli/tracking_jobs/'.$pi_table.'/'.$attachment;
            // $attachFIle= ($attachment)?json_decode($this->OBS->getFileBase64($fileAttachment)):'';
            $filePath='pli/tracking_jobs/'.$pi_table.'/'.$attachment;
            $fileUrl = Storage::disk('s3')->url($filePath);



            $items=array(
                'pid'=>$value->pid,
                'module'=>$value->modul,
                'pi_table'=>$value->pi_table,
                'id_file'=>$value->id_file,
                'file_name'=>$value->file_name,
                'attachment'=>'https://mobiles-app.obs.ap-southeast-3.myhuaweicloud.com'.$fileUrl,
                'subject'=>$value->subject,
                'description'=>$value->description,
                'extension'=>$value->extension,
            );
            $arrData[]=$items;
        }
        return response()->json(['data' => $arrData,'file'=>$file],200);
        // return response()->json(['data' => $resp,'file'=>$filebase64],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $pid)
    {
        // Update an existing user
        // $resp = MsDriver::find($pid);
        $resp = MsFiles::where('pid', $pid)->first();

        if (!$resp) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $resp->update($request->all());
        return response()->json(['data'=>$resp,'message'=>'success update data'],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
