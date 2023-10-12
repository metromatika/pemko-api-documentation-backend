<?php

namespace App\Interfaces;

use App\Models\Collection;

interface CollectionInterface
{
    public function uploadSourceCode(array $sourceCodeFiles, Collection $collection);

    public function deleteSourceCode(Collection $collection);
}
