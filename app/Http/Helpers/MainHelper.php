<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Storage;

class MainHelper
{
    public function csv_to_array($filename)
    {
        $text = Storage::disk('public')->get($filename);
        $lines = explode("\n", $text);
        $header = str_getcsv(array_shift($lines), ',');
        $data = [];
        foreach ($lines as $line) {
            if(empty($line)) continue;
            $row = str_getcsv($line, ',');
            $data[] = array_combine($header, $row);
        }
        return $data;
    }
}
