<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MsDriverResource;
use App\Models\MsDriver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\ObsService;
use App\Services\ObsStorageService;

class MsDriverController extends Controller
{
    protected $ObsstorageService;

    public function __construct(ObsStorageService $ObsstorageService)
    {
        $this->middleware('auth:sanctum');
        $this->ObsstorageService = $ObsstorageService;
    }

/**
 * Display List Data
 * @queryParam team int The team to pull tasks for.
 * @return \Illuminate\Http\Response
*/
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
        $query = MsDriver::query();
        $query->orderBy(isset($order_by) ? $order_by : 'id', isset($order_direction) ? $order_direction : 'asc');
        // Apply pagination
        $items = $query->paginate((int)$per_page, ['*'], 'page', (int)$page);

        $response = MsDriverResource::collection($items);

        return response()->json(['data' => $response, 'page' => $page, 'per_page' => $per_page]);
    }
/**
 * Display Detail Data
 * @queryParam team int The team to pull tasks for.
 * @return \Illuminate\Http\Response
*/
    public function show($id)
    {
        $file='pli/prisma.png';
        $filebase64= json_decode($this->ObsstorageService->getFileBase64($file));


        // Retrieve a single user by ID
        $resp = MsDriver::where('driver_no', $id)->first();

        if (!$resp) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        return response()->json(['data' => $resp,'file'=>$filebase64],200);
    }

/**
 * Create New Data
 * @queryParam team int The team to pull tasks for.
 * @return \Illuminate\Http\Response
*/
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'driver_no' => 'required|string|unique:ms_driver',
            'driver_name' => 'required|string',
            'driver_contact_number1' => 'nullable|string', // Assuming it's optional
            'driver_contact_number2' => 'nullable|string', // Assuming it's optional
            'is_active' => 'required|integer',
            'is_deleted' => 'required|integer',
            'ip' => 'nullable|string', // Assuming it's optional
            'create_by' => 'nullable|string', // Assuming it's optional
            'vendor_id' => 'nullable|string', // Assuming it's optional
            'email' => 'required|string|email|unique:ms_driver',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
        }

        $driver = MsDriver::create([
            'driver_no' => $request['driver_no'],
            'driver_name' => $request['driver_name'],
            'driver_contact_number1' => $request['driver_contact_number1'],
            'driver_contact_number2' => $request['driver_contact_number2'],
            'is_active' => $request['is_active'],
            'is_deleted' => $request['is_deleted'],
            'ip' => $request['ip'],
            'create_by' => $request['create_by'],
            'vendor_id' => $request['vendor_id'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $response = [
            'driver' => new MsDriverResource($driver), // Use the resource here
            'message' => 'Success create data',
        ];

        return response()->json(['data' => $response], 201);
    }

/**
 * Updateof Data
 * @queryParam team int The team to pull tasks for.
 * @return \Illuminate\Http\Response
*/
    public function update(Request $request, $driver_no)
    {
        // Update an existing user
        // $resp = MsDriver::find($driver_no);
        $resp = MsDriver::where('driver_no', $driver_no)->first();

        if (!$resp) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $resp->update($request->all());
        return response()->json(['data'=>$resp,'message'=>'success update data'],200);
    }

    public function update_password(Request $request, $id)
    {
        $driver = MsDriver::where('driver_no', $id)->first();
        if ($driver) {
            $old_pass = $request->old_password;
            $new_pass = $request->new_password;
            $new_pass_retype = $request->retype_new_password;
            $passdb = $driver->password;
               // Check password
                if (password_verify($old_pass, $passdb)) {

                    // how to validate the new_password and retype_new_password must same in  laravel =>
                    $validator = Validator::make($request->all(), [
                        'old_password' => 'required|string',
                        'new_password' => 'required|string|min:6',
                        'retype_new_password' => 'required|string|same:new_password',
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->messages()], 400); // Return validation errors as JSON
                    }

                    // check if new_pass and new_pass_retype is same and greather then 5
                    $driver->password =bcrypt($new_pass);
                    $driver->save();
                    return response()->json(['message'=>'success update Password'],200);
                } else {
                    return response()->json(['message'=>'old pass wrong'],404);
                }
            // $driver->save();
        } else{
            return response()->json(['message'=>'no driver foun'],404);
        }
    }

    /**
 * Delete of Data
 * @queryParam team int The team to pull tasks for.
 * @return \Illuminate\Http\Response
*/
    public function destroy($driver_no)
    {
        // Delete a user
        $resp = MsDriver::where('driver_no', $driver_no)->first();

        if (!$resp) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $resp->delete();
        return response()->json(['message' => 'User deleted'],204);
    }
}
