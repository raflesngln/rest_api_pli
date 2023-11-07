<?php

    namespace App\Services;
    use Illuminate\Support\Facades\Storage;
class StorageService
{
    public function put($path, $contents, $options = [])
    {
        return Storage::disk('s3')->put($path, $contents, $options);
    }

    public function get($path)
    {
        return Storage::disk('s3')->get($path);
    }

    public function getFileBase64($file){
        $getfile = Storage::disk('s3')->get($file);
        $mimeType = Storage::disk('s3')->mimeType($file);
        $base64 = base64_encode($getfile);
        $mimeToExtension = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            // Add more mappings as needed
         ];
         $extension = $mimeToExtension[$mimeType] ?? 'unknown';
         return json_encode(['base64'=>'data:'.$mimeType.';base64,'.$base64,'type'=>$extension]);
    }

    function uploadFile($base64) {
        $fileData = base64_decode($base64);
        $fileName = date('_tracking_Y-m-d H:i:s').'.png';
        $save= Storage::disk('s3')->put('pli/'.$fileName, $fileData);
        if($save){
            return json_encode(['status'=>'success']);
        }else{
            return json_encode(['status'=>'false']);
        }

    }

    public function delete($path)
    {
        return Storage::disk('s3')->delete($path);
    }

    public function makeDirectory($path)
    {
        return Storage::disk('s3')->makeDirectory($path);
    }

    public function deleteDirectory($path)
    {
        return Storage::disk('s3')->deleteDirectory($path);
    }
}
