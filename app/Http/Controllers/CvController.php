<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    public function download(Cv $cv): Response
    {
        abort_if($cv->user_id !== auth()->id(), 403);

        $content = Storage::disk('private')->get($cv->file_path);

        return response($content, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $cv->file_name . '"',
        ]);
    }
}
