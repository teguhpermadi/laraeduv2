<?php

namespace App\Http\Controllers;

class RdmExportController extends Controller
{
    public function download(string $file)
    {
        $path = storage_path('app/rdm-exports/'.$file);

        if (! file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($path, $file)->deleteFileAfterSend(true);
    }
}
