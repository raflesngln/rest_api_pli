<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MsFilesResource;
use App\Models\MsFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class MsFilesController extends Controller
{
    /**
     * Display a listing of the resource.
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
        //
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
