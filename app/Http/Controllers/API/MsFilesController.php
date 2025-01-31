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
use Illuminate\Support\Facades\Log;


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
        date_default_timezone_set('Asia/Jakarta');
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
        // // $upload=json_decode($this->OBS->uploadFile('tracking-mobile/ocean/'.$filename, $fileData)); // get response upload name path

        // $newFileName = 'raflesian.jpg'; // Set the new file name
        // $savePath = 'tracking-mobile/ocean/' . $newFileName;
        // $save = Storage::disk('s3')->put($savePath, $fileData);

        try {
            $fileData = $request->file('attachment'); // Get the uploaded file from the request
            $attachment = $request['attachment'];
            $attachment = base64_decode($attachment);
            $filename = $request['modul'] . '-' . date('YmdHis').'.jpg';
            // $newFileName = 'raflesian.jpg'; // Set the new file name
            $newPath = $request['pi_table']; // Set the new file name
    
            
    
            /*
            $file = $request->file('attachment');
            // $file=base64_decode($file);
            // $fileName = $file->getClientOriginalName();
            $attachment = $request['attachment'];
            $attachment = base64_decode($attachment);
    
            $filename= 'track_'.$id_dispatch.'_'.date('YmdHis');
    
            $upload=json_decode($this->OBS->uploadFile('pli/tracking/'.$filename, $file));
            */
    
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

                $pid_generate_func = DB::select('SELECT f_generate_pid("FL001") AS PID');
                $id_generated_func = DB::select('SELECT f_generate_id("FL001") AS ID');

            $validator = Validator::make($request->all(), [
                'modul' => 'required|string',
                'pi_table' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
            }
    
            $ms_files = MsFiles::create([
                // 'pid'=>$new_pid,
                'pid'=>$pid_generate_func[0]->PID,
                'modul'=>$request['modul'],
                'pi_table'=>$request['pi_table'],
                // 'id_file'=>$id_file,
                'id_file'=>$id_generated_func[0]->ID,
                'file_name'=>$filename,
                'subject'=>$request['subject'],
                'description'=>$request['description'],
                'extension'=>$request['extension'],
                'created_by'=>$request['created_by'],
                'created_datetime'=>date('Y-m-d H:i:s'),
                'created_ip_address'=>$request['created_ip_address'],
                'created_by_browser'=>$request['created_by_browser'],
                'modified_by'=>$request['modified_by'],
                'modified_datetime'=>'2024-07-15 15:50:19',
                'modified_ip_address'=>$request['modified_ip_address'],
                'modified_browser'=>$request['modified_browser'],
                'is_active'=>1,
                'is_deleted'=>0,
                'table_code'=>'FL001',
                'expired_date'=>date('Y-m-d'),
                'dept'=>$request['dept'],
                'latitude'=>$request['latitude'],
                'longitude'=>$request['longitude']
            ]);
            $save = Storage::disk('s3')->putFileAs('tracking-mobile/ocean/'.$newPath, $fileData, $filename,['ACL' => 'private']);
    
            // json_decode($this->OBS->uploadFile('tracking-mobile/ocean/'.$filename, $fileData)); // get response upload name path
            $response = [
                // 'ms_files' => new MsFilesResource($ms_files), // Use the resource here
                'ms_files' => new MsFilesResource($ms_files), // Use the resource here
                'message' => 'Success create data',
                'status_upload_files'=>$save,
                'pid_generate_func'=>$pid_generate_func[0]->PID,
                'id_generated_func'=>$id_generated_func[0]->ID,
            ];
    
            return response()->json(['data'=>$response,'file'=>'upload'], 201);
        } catch (\Exception $e) {
            // Catch and log any exceptions
           Log::error('Error saving file to S3:', ['error' => $e->getMessage()]);
           return response()->json(['error' => $e->getMessage()], 500);
    }
        
       
    }

    public function test_upload_file(Request $request)
    {
        ini_set('max_execution_time', 300);
        try {
                // Check if the file exists in the request
                if (!$request->hasFile('attachment')) {
                    return response()->json(['message' => 'No file uploaded'], 400);
                }
    
                $fileData = $request->file('attachment'); // Get the uploaded file from the request
    
                // Ensure the file was uploaded without errors
                if (!$fileData->isValid()) {
                    return response()->json(['message' => 'File upload error'], 400);
                }
    
                $filename = $request['modul'] . '-' . date('YmdHis').'.jpg';
                $newPath = $request['pi_table']; // Set the new file name
    
                // Store the file in the specified S3 path
                $save = Storage::disk('s3')->putFileAs('tracking-mobile/ocean/'.$newPath, $fileData, $filename, ['ACL' => 'private']);
                    // Log the save result
                    Log::debug('Save Result:', ['save' => $save]);
                return response()->json(['data' => $save, 'message' => $save]);
        } catch (\Exception $e) {
                 // Catch and log any exceptions
                Log::error('Error saving file to S3:', ['error' => $e->getMessage()]);
                return response()->json(['error' => $e->getMessage()], 500);
        }

    }
    public function test_view_file_obs(Request $request)
    {
        $fileName = "CONTAINER_FRONT-20240808074454.jpg";
        $pi_table = "TSS0220240520000000001";
        
        try {
            // Generate a temporary URL valid for 5 minutes
            $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                'tracking-mobile/ocean/' . $pi_table . '/' . $fileName, 
                now()->addMinutes(5)
            );
    
            // You can return the URL or embed it in an HTML image tag
            return response()->json(['url' => $temporaryUrl], 200);
    
        } catch (\Exception $e) {
            // Handle any errors that might occur
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        // $file='pli/prisma.png';
        // Retrieve a single user by ID
        $resp = MsFiles::where('pi_table', $pi_table)->get();

        if (!$resp) {
            return response()->json(['message' => 'Files not found'], 404);
        }
        $arrData=[];
        foreach ($resp as $key => $value) {
            $fileName=$value->file_name;
            $pi_table=$value->pi_table;
            $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                'tracking-mobile/ocean/' . $pi_table . '/' . $fileName, 
                now()->addMinutes(5)
            );
            // $fileAttachment='tracking-mobile/ocean/'.$pi_table.'/'.$attachment;
            // $filePath='tracking-mobile/ocean/'.$pi_table.'/'.$attachment;
            // $fileUrl = Storage::disk('s3')->url($filePath);
            $items=array(
                'pid'=>$value->pid,
                'module'=>$value->modul,
                'pi_table'=>$value->pi_table,
                'id_file'=>$value->id_file,
                'file_name'=>$value->file_name,
                // 'attachment'=>'https://obs-transys-pli.obs.myhuaweicloud.com'.$fileUrl,
                'attachment'=>$temporaryUrl,
                'subject'=>$value->subject,
                'description'=>$value->description,
                'extension'=>$value->extension,
            );
            $arrData[]=$items;
        }
        return response()->json(['data' => $arrData],200);
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
