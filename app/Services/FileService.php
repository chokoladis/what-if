<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\File;
use App\Models\FileCategory;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FileService {

    const MAX_FILE_SIZE = 3150000; //bites
    const MAX_FILE_SIZE_KB = self::MAX_FILE_SIZE/1024;
    const MAX_FILE_SIZE_MB = self::MAX_FILE_SIZE/1048576;

    public static function createThumbWebp(string $filePath){

        $imgManager = new ImageManager(new Driver());

        $realPath = public_path().Storage::url($filePath);
        $image = $imgManager->read($realPath);

        $size = filesize($filePath); // bites
        $kbSize = $size / 1024;

        $pathInfo = pathinfo($filePath);
        $mainPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';
        $newFilepath = public_path().Storage::url($mainPath);

        if ($kbSize > 100){
            
            $image->resize(300, 300)->toWebp(80)->save($newFilepath);

        } elseif (file_exists($newFilepath)){

            $size = filesize($newFilepath);
            $kbSize = $size / 1024;

            if ($kbSize > 100){

                $image = $imgManager->read($newFilepath);
                $image->resize(300, 300)->toWebp(70)->save($newFilepath);
            }

        } else {
            return false;
        }

        return $mainPath;
    }

    public static function save(TemporaryUploadedFile|UploadedFile $img, string $mainDir = 'main'){

        $root = public_path("storage/{$mainDir}");
        $subDir = substr($img->hashName(), 0, 3);
        $folder = "{$root}/{$subDir}";

//        $root = public_path('/storage/' . $mainDir);
//        $subDir = substr($img->hashName(), 0, 3 );
        
        try {
            if (!is_dir($folder)){
                mkdir($folder, recursive: TRUE);
            }
            
            $ext = $img->extension();
            $name = strlen($img->hashName()) > 45 ? substr($img->hashName(), 0, 45).'.'.$ext : $img->hashName();
            $filePath = "{$subDir}/{$name}";

            $destination = "{$folder}/{$name}";
    
            $data = [
                'name' => $name,
                'expansion' => $ext,
                'path' => $filePath, 
                'relation' => $mainDir
            ];

            \Illuminate\Support\Facades\File::copy($img->getRealPath(), $destination);
//            $img->move($folder, $name);

            $file = File::create($data);
            
        } catch (\Throwable $th) {
            throw $th;
        }

        return $file;
    }

    static protected function generatePhotoPath($file, $mainDir){

        $salt = auth()->user()->id.'_2901';
            
        $file_name = md5($salt.'_'.$file->getClientOriginalName());
        $file_name = mb_substr($file_name, 0, 16).'.'.$file->extension();
        
        $mk_name = substr($file_name,0,3);

        $folder = public_path() . $mainDir . $mk_name.'/';
        if (!is_dir($folder)){
            mkdir($folder, 0755);
        }

        return [ 'subdir' => $mk_name, 'file_name' => $file_name ];
    }   
}
