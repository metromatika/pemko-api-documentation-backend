<?php

namespace App\Interfaces;

use App\Models\Collection;

interface CollectionInterface
{
    /**
     * Uploads source code files to a collection.
     *
     * @param array $sourceCodeFiles An array of source code files to upload.
     * @param Collection $collection The collection to upload the source code files to.
     * @return void
     */
    public function uploadSourceCode(array $sourceCodeFiles, Collection $collection);

    /**
     * Deletes source code files from a collection.
     *
     * @param Collection $collection The collection to delete the source code files from.
     * @return void
     */
    public function deleteSourceCode(Collection $collection);
}
