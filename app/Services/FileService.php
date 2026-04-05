<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\FileDTO;
use App\Exceptions\FileSaveException;
use App\Models\File;
use App\Models\TempFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FileService
{

    const MAX_FILE_SIZE = 5242880; //bites
    const MAX_FILE_SIZE_KB = self::MAX_FILE_SIZE / 1024;
    const MAX_FILE_SIZE_MB = self::MAX_FILE_SIZE / 1048576;
    const ALLOW_IMG_EXT = ['jpg', 'png', 'jpeg', 'gif'];

    public static function saveTemp(TemporaryUploadedFile|UploadedFile $img): TempFile
    {
        $fileDTO = self::prepareFileDTO($img, 'temp');

        return TempFile::create([
            'name' => $fileDTO->name,
            'expansion' => $fileDTO->ext,
            'path' => $fileDTO->filePath,
            'original_name' => $img->getClientOriginalName()
        ]);
    }

    private static function prepareFileDTO(TemporaryUploadedFile|UploadedFile $img, string $mainDir = 'main'): FileDTO
    {
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

        return new FileDTO(
            $name,
            $ext,
            $filePath,
            $mainDir
        );
    }

    static function getPhoto(?File $file): string
    {
        $nophoto_src = Storage::url('main/nophoto.jpg');

        if ($file && $file->path) {
//                todo cache ?
            $disk = Storage::disk('public');
            $chunkPath = $file->relation . '/' . $file->path;

            return $disk->exists($chunkPath) ? $disk->url($chunkPath) : $nophoto_src;
        }

        return $nophoto_src;
    }

    public static function saveFromQueue(TempFile $file, string $mainDir = 'main'): File
    {
        $disk = Storage::disk('public');

        $sourcePath = 'temp/' . $file->path;

        if (!$disk->exists($sourcePath)) {
            throw new FileSaveException('Temp file not found');
        }

        $subDir = explode('/', $file->path)[0];
        $folder = "{$mainDir}/{$subDir}";

        if (!$disk->exists($folder)) {
            $disk->makeDirectory($folder);
        }

        $res = $disk->move($sourcePath, $folder . '/' . $file->name);
        if (false === $res) {
            throw new FileSaveException();
        }

        $data = [
            'name' => $file->name,
            'expansion' => $file->expansion,
            'path' => $file->path,
            'relation' => $mainDir
        ];

        $file->delete();

        return File::create($data);
    }

    static function getPhotoFromIndex(?array $file, string $subdir)
    {
        $nophoto_src = Storage::url('main/nophoto.jpg');

        if ($file && $file['path']) {
            $filePath = Storage::url($subdir . '/' . $file['path']);
            return file_exists($_SERVER['DOCUMENT_ROOT'] . $filePath) ? $filePath : $nophoto_src;
        }

        return $nophoto_src;
    }

    public static function save(TemporaryUploadedFile|UploadedFile $img, string $mainDir = 'main'): File
    {
//        сделать сохранение по папкам-юзерам и названиям файлов ? (для проверки на существование и по контрольной сумме)
        $fileDTO = self::prepareFileDTO($img, $mainDir);

        return File::create([
            'name' => $fileDTO->name,
            'expansion' => $fileDTO->ext,
            'path' => $fileDTO->filePath,
            'relation' => $fileDTO->mainDir
        ]);
    }
}
