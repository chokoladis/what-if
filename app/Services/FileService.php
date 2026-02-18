<?php

namespace App\Services;

use App\Exceptions\FileSaveException;
use App\Interfaces\Services\FileProxyRedisInterface;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FileService implements FileProxyRedisInterface
{

    const MAX_FILE_SIZE = 5242880; //bites
    const MAX_FILE_SIZE_KB = self::MAX_FILE_SIZE / 1024;
    const MAX_FILE_SIZE_MB = self::MAX_FILE_SIZE / 1048576;
    const ALLOW_IMG_EXT = ['jpg', 'png', 'jpeg', 'gif'];

    public static function createThumbWebp(string $filePath)
    {

//        todo rework
        $imgManager = new ImageManager(new Driver());

        $realPath = public_path() . Storage::url($filePath);
        $image = $imgManager->read($realPath);

        $size = filesize($filePath); // bites
        $kbSize = $size / 1024;

        $pathInfo = pathinfo($filePath);
        $mainPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        $newFilepath = public_path() . Storage::url($mainPath);

        if ($kbSize > 100) {

            $image->resize(300, 300)->toWebp(80)->save($newFilepath);

        } elseif (file_exists($newFilepath)) {

            $size = filesize($newFilepath);
            $kbSize = $size / 1024;

            if ($kbSize > 100) {

                $image = $imgManager->read($newFilepath);
                $image->resize(300, 300)->toWebp(70)->save($newFilepath);
            }

        } else {
            return false;
        }

        return $mainPath;
    }

    public static function save(TemporaryUploadedFile|UploadedFile $img, string $mainDir = 'main')
    {
//        сделать сохранение по папкам-юзерам и названиям файлов ? (для проверки на существование и по контрольной сумме)
        $disk = Storage::disk('public');

        $subDir = substr($img->hashName(), 0, 3);
        $folder = "{$mainDir}/{$subDir}";

        if (!$disk->exists($folder)) {
            $disk->makeDirectory($folder);
        }

        $ext = $img->extension();
        $name = strlen($img->hashName()) > 45 ? substr($img->hashName(), 0, 45) . '.' . $ext : $img->hashName();
        $filePath = "{$subDir}/{$name}";

        $res = $disk->putFileAs($folder, $img, $name);
        if (false === $res) {
            throw new FileSaveException();
        }

        return File::create([
            'name' => $name,
            'expansion' => $ext,
            'path' => $filePath,
            'relation' => $mainDir
        ]);
    }

    static function getPhoto(?File $file, string $subdir)
    {
        $nophoto_src = Storage::url('main/nophoto.jpg');

        if ($file && $file->path) {
            $disk = Storage::disk('public');

            $chunkPath = $file->relation . '/' . $file->path;

            return $disk->exists($chunkPath) ? $disk->url($chunkPath) : $nophoto_src;
        }

        return $nophoto_src;
    }

    static function getPhotoFromIndex(?array $file, string $subdir)
    {
        $nophoto_src = Storage::url('main/nophoto.jpg');

        if ($file && $file['path']) {
            $filePath = Storage::url($subdir . '/' . $file['path']);
            return file_exists($_SERVER['DOCUMENT_ROOT'].$filePath) ? $filePath : $nophoto_src;
        }

        return $nophoto_src;
    }

    public static function getFromRedis(?File $file, string $subdir)
    {
        $src = self::getPhoto($file, $subdir);

//        try {
        $fileDataRaw = Redis::get($src);
        if (!$fileDataRaw) {
            $fileDataRaw = file_get_contents(public_path($src));
            $test = Redis::set($src, $fileDataRaw);
            $exp = Redis::expire($src, 3600 * 3);
        }

//        } catch (\Throwable $th) {
//            dd($th->getFile(), $th->getLine(), $th->getMessage());
//        }

        return $fileDataRaw ?? null;
    }
}
