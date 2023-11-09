<?php

    namespace App\Services;
    use Illuminate\Support\Facades\Storage;
class ObsStorageService
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
        // $mimeToExtension = [
        //     'image/jpeg' => 'jpg',
        //     'image/png' => 'png',
        //     'application/pdf' => 'pdf',
        //  ];
        //  $extension = $mimeToExtension[$mimeType] ?? 'unknown';
        $extension = $this->getFileExtension($file);
         return json_encode(['base64'=>'data:'.$mimeType.';base64,'.$base64,'type'=>$extension]);
    }
    public function getFileExtension($file){
        $mimeType = Storage::disk('s3')->mimeType($file);
        $mimeToExtension = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            // Add more mappings as needed
         ];
         $extension = $mimeToExtension[$mimeType] ?? 'unknown';
         return $extension;
    }

    function uploadFile($path,$file) {
        $fileData = base64_decode($file);
        $extension ='jpg';// $this->getFileExtension($file);
        $save= Storage::disk('s3')->put($path.'.'.$extension, $fileData);
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
    public function multiDeleteFile($path)
    {
        return Storage::disk('s3')->delete($path);
    }
}
