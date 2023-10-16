<?php

namespace App\Repositories;

use App\Interfaces\CollectionInterface;
use App\Interfaces\SourceCodeInterface;
use App\Models\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectionRepository implements CollectionInterface
{

    /**
     * Upload the source code file.
     *
     * @param array $sourceCodeFiles
     * @param Collection $collection
     * @return Collection
     */
    public function uploadSourceCode(array $sourceCodeFiles, Collection $collection): Collection
    {
        foreach ($sourceCodeFiles as $sourceCodeFile) {
            if ($sourceCodeFile instanceof UploadedFile) {
                $file_path = $sourceCodeFile->storeAs('source-code', $this->generateFileName($sourceCodeFile));

                $collection->sourceCode()->create([
                    'file_path' => $file_path
                ]);
            }
        }

        return $collection;
    }

    public function deleteSourceCode(Collection $collection): Collection
    {
        $sourceCode = $collection->load('sourceCode');

        foreach ($sourceCode->sourceCode as $sourceCodeFile) {
            Storage::delete($sourceCodeFile->file_path);
            $sourceCodeFile->delete();
        }
        return $collection;
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
        return $fileName . '-' . (int)(microtime(true) * 1000) . '.' . $fileExtension;
    }
}
