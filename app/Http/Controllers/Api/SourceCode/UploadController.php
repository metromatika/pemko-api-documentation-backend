<?php

namespace App\Http\Controllers\Api\SourceCode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class UploadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     */
    public function __invoke($request)
    {

    }


    protected function createFileName(UploadedFile $file)
    {

    }
}
