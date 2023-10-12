<?php

namespace App\Repositories;

use App\Interfaces\SourceCodeInterface;
use App\Models\SourceCode;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class SourceCodeRepository implements SourceCodeInterface
{

    /**
     * Upload the source code file.
     *
     * @param UploadedFile $file
     * @param SourceCode $sourceCode
     * @return SourceCode
     */
    public function uploadSourceCode(UploadedFile $file, SourceCode $sourceCode): SourceCode
    {
        $file_path = $file->storeAs('source-code', $this->generateFileName($file));
        $sourceCode->file_path = $file_path;
        return $sourceCode;
    }

    /**
     * Generate file name for the source code file.
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateFileName(UploadedFile $file): string
    {
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileExtension = $file->getClientOriginalExtension();
        return $fileName . '-' . uniqid() . '.' . $fileExtension;
    }
}
