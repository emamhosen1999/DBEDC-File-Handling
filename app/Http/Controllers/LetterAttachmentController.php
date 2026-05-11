<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LetterAttachmentController extends Controller
{
    public function download(Letter $letter): StreamedResponse
    {
        Gate::authorize('view', $letter);

        abort_unless($letter->file_path, 404, 'No attachment for this letter');
        abort_unless(Storage::disk('local')->exists($letter->file_path), 404, 'Attachment missing');

        return Storage::disk('local')->download(
            $letter->file_path,
            $letter->file_name ?? basename($letter->file_path)
        );
    }
}
