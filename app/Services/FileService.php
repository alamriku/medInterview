<?php

namespace App\Services;

use Illuminate\Support\Str;

class FileService
{
    public function storeFile($data)
    {

        $fileName = Str::random(15).time() . '.' . $data->getClientOriginalExtension();
        $data->move(public_path() . '/image', $fileName);
        return $fileName;
    }

    public function removeFile($filePath)
    {
        unlink(public_path("$filePath"));
    }
}
