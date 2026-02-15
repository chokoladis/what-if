<?php

namespace App\Tools;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileGarbageCollector
{
    const DIR_ACTIVE = ['categories', 'questions', 'users'];

    public function cleanFileStorage()
    {
        $disk = Storage::disk('public');

        foreach ($this->getAllActiveDir() as $dir) {

            $arPath = explode('/', $dir);
            $relation = current($arPath);

            // todo optimize
            $originalFiles = $disk->files($dir);

            if (empty($originalFiles)) { // todo remove dir
                continue;
            }

            $preparedFilesPath = array_map(function ($filePath) use ($disk) {
                $arPath = explode('/', $filePath);
                unset($arPath[0]);
                return implode('/', $arPath);
            }, $originalFiles);

            $queryModels = File::query()
                ->where('relation', $relation)
                ->whereIn('path', $preparedFilesPath)
                ->get(['path'])
                ->toArray();

            if (empty($queryModels)) {
                $disk->delete($originalFiles);
                continue;
            }

            $queryModels = array_column($queryModels, 'path');
            $diffPaths = [];
            foreach ($preparedFilesPath as $itemPath) {
                if (!in_array($itemPath, $queryModels)) {
                    $diffPaths[] = $relation.'/'.$itemPath;
                }
            }

            if (!empty($diffPaths)) {
                $disk->delete($diffPaths);
            }
        }
    }

    private function getAllActiveDir()
    {
        $disk = Storage::disk('public');

        foreach ($disk->allDirectories() as $dir) {

            $posSlash = stripos($dir, '/');
            if (is_bool($posSlash)) {
                continue;
            }

            foreach (self::DIR_ACTIVE as $activeDir) {
                $pos = stripos($dir, $activeDir);
                if ($pos === 0) {
                    break;
                }
            }

            if ($pos !== 0) {
                continue;
            }

            yield $dir;
        }
    }
}