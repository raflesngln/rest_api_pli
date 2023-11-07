<?php

namespace App\Services;

use App\Helpers\Query;
use App\Obs\Obs\ObsClient;
use Att\Responisme\Exceptions\StarterKitException;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ObsService
{

    private static function createObsClient()
    {
        $obsClient = new ObsClient([
            'key' => config('filesystems.disks.obs.key'),
            'secret' => config('filesystems.disks.obs.secret'),
            'endpoint' => config('filesystems.disks.obs.endpoint'),
        ]);
        return $obsClient;
    }
    public static function getObjectUrl($objectname)
    {
        $obsClient = self::createObsClient();
        $resp = $obsClient->createSignedUrl([
            'Method' => 'GET',
            'Bucket' => config('filesystems.disks.obs.bucket'),
            'Key' => $objectname,
            'SaveAsStream' => true
        ]);
        return $resp['SignedUrl'];
    }
    public static function toStorage($prefix, $file, $pathimage, $reqExtensions = null, $isObs = false)
    {
        // $fileName   = $prefix . '_' . time() . '_' . str_replace([' '], '', $file->getClientOriginalName());
        $fileName   = $prefix . '_' . time() . '_'.$file;
        if ($reqExtensions) {
            $isrightExt = false;
            foreach ($reqExtensions as $ext) {
                if ($ext == $this->getClientOriginalExtension($file)) {
                    $isrightExt = true;
                    break;
                }
            }
            if (!$isrightExt) {
                throw new Exception("File Mush Be [" . join(", ", $reqExtensions) . "] Format");
            }
        }
        if (!$isObs) {
            Storage::disk('public')->put($pathimage . $fileName, File::get($file));
            return 'storage/' . $pathimage . $fileName;
        } else {
            $obsClient = self::createObsClient();
            $obsClient->putObject([
                'Bucket' => config('filesystems.disks.obs.bucket'),
                'Key' =>  $pathimage . $fileName,
                'Body' =>  file_get_contents($file->path()) //fopen($file->getPath(), 'r'), //$file->getPathName()
            ]);
            return $pathimage . $fileName;
        }
    }
    public static function getRealPath($path){
       // $input = "storage/course/image/1_1689652869_2_Clone_Image.png";
        $output = preg_replace("/^storage\//", "", $path);
        return $output;
    }
    public static function random_number()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // generate a pin based on 2 * 7 digits + a random character
        $pin = mt_rand(1000, 9999)
            . mt_rand(1000, 9999)
            . $characters[rand(1, strlen($characters) - 4)];
        $string = str_shuffle($pin);
        return $string;
    }
    public static function cloneFileObs($fileName, $oldfilename, $pathimage)
    {
        $obsClient = self::createObsClient();
        $obsClient->copyObject([
            'Bucket' => config('filesystems.disks.obs.bucket'),
            'Key' =>  $pathimage . $fileName,
            'CopySource' => config('filesystems.disks.obs.bucket').'/'.$oldfilename
        ]);
        return $pathimage . $fileName;
    }
    public static function isFileExist($name, $message)
    {
        if (!$file = request()->file($name)) {
            throw new Exception($message);
        }
        return $file;
    }
    public static function ifFileExist($name)
    {
        $ret = true;
        if (!$file = request()->file($name)) {
            return false;
        }
        return $ret;
    }
    public static function removeObsFile($fileName)
    {
        $obsClient = self::createObsClient();
        $obsClient->deleteObject([
            'Bucket' => config('filesystems.disks.obs.bucket'),
            'Key' => $fileName,
            'SaveAsStream' => false
        ]);
    }
    public static function removeFile($fileName)
    {
        $imageloc = str_replace('storage', '', $fileName);
        Storage::delete('/public' . $imageloc);
    }

    public function getClientOriginalExtension($fileName) {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

}
