<?php

namespace App\Interfaces;

use Illuminate\Http\UploadedFile;
use App\Models\SourceCode;

interface SourceCodeInterface
{
    public function uploadSourceCode(UploadedFile $file, SourceCode $sourceCode);
}
