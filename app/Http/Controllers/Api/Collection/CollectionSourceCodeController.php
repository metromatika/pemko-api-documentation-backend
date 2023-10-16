<?php

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CollectionSourceCodeController extends Controller
{
    use ApiResponser;

    private Collection $collectionModel;

    public function __construct(Collection $collectionModel)
    {
        $this->collectionModel = $collectionModel;
    }

    public function getSourceCodeByCollection(Collection $collection)
    {
        $collection = $collection->load('sourceCode');

        if ($collection->sourceCode->isEmpty()) {
            return $this->successResponse([], 'No collection found');
        }

        if (Auth::user()->isProgrammer()) {
            if (Auth::user()->id !== $collection->user_id) {
                return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
            }
        }

        return $this->successResponse($collection->sourceCode);
    }
}
