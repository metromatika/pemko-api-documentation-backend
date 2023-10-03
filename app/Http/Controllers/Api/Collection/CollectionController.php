<?php

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Models\Collection;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;


class CollectionController extends Controller
{
    use ApiResponser;

    public Collection $collectionModel;

    public function __construct(Collection $collectionModel)
    {
        $this->collectionModel = $collectionModel;
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $collections = $this->collectionModel;

        if (auth()->check()) {
            if (auth()->user()->isAdmin()) {
                $collections = $collections->admin()
                    ->when($request->has('access_type'), function ($query) use ($request) {
                        return $query->where('access_type', $request->get('access_type'));
                    });
            } else if (auth()->user()->isProgrammer()) {
                $collections = $collections->when($request->has('get'), function ($query) use ($request) {
                    if ($request->get('get') == 'self') {
                        return $query->programmer(auth()->user()->id)
                            ->when($request->has('access_type'), function ($query) use ($request) {
                                return $query->where('access_type', $request->get('access_type'));
                            });
                    }

                    return $query->programmer(auth()->user()->id)
                        ->orWhere('access_type', Collection::COLLECTION_ACCESS_TYPE_PUBLIC);
                });
            }
        } else {
            $collections = $collections->public();
        }


        return response()->json([
            'message' => 'Successfully retrieved collections',
            'data' => $collections->paginate(10)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCollectionRequest $request
     * @return JsonResponse
     */
    public function store(StoreCollectionRequest $request)
    {
        $collection = DB::transaction(function () use ($request) {
            $jsonContent = $this->getJSONContent($request);
            return $this->collectionModel->create([
                'user_id' => auth()->user()->id,
                'title' => $jsonContent['info']['name'] ?? 'Untitled',
                'access_type' => $request->validated('access_type'),
                'json_file' => $jsonContent
            ]);
        });

        return response()->json([
            'message' => 'Collection created successfully',
            'data' => $collection
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Collection $collection
     * @return JsonResponse
     */
    public function show(Collection $collection)
    {
        if (auth()->check()) {
            if (auth()->user()->isProgrammer()) {
                if ($collection->user_id == auth()->user()->id)
                    return response()->json([
                        'message' => 'OK',
                        'data' => $collection
                    ]);

                return response()->json([
                    'error' => [
                        'code' => 401,
                        'message' => 'Unauthorized'
                    ]
                ]);
            }

            return response()->json([
                'message' => 'OK',
                'data' => $collection
            ]);

        } else {
            if ($collection->access_type == 'public')
                return response()->json([
                    'message' => 'OK',
                    'data' => $collection]);


            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => 'Unauthorized'
                ]
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCollectionRequest $request
     * @param Collection $collection
     * @return JsonResponse
     */
    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        if (auth()->user()->id == $collection->user_id) {
            $input = [
                'title' => $request->validated('title') ?? $collection->title,
                'access_type' => $request->validated('access_type') ?? $collection->access_type,
                'json_file' => $collection->json_file
            ];

            DB::transaction(function () use ($input, $collection) {
                return $collection->update($input);
            });

            return response()->json([
                'message' => 'Collection updated successfully',
                'data' => $collection->refresh()
            ]);
        }

        return response()->json([
            'error' => [
                'code' => 401,
                'message' => 'Unauthorized'
            ]
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Collection $collection
     * @return JsonResponse|Response
     */
    public function destroy(Collection $collection)
    {
        if (auth()->user()->isProgrammer()) {
            if ($collection->user_id == auth()->user()->id) {
                $collection->delete();

                return response()->noContent();
            }

            return response()->json([
                [
                    'error' => [
                        'code' => 401,
                        'message' => 'Unauthorized'
                    ]
                ]
            ]);
        }

        $collection->delete();
        return response()->noContent();
    }

    /**
     * Retrieves the content of a JSON file and returns it as an array.
     *
     * @param StoreCollectionRequest|UpdateCollectionRequest $request
     * @return JsonResponse|array
     */
    private function getJSONContent(StoreCollectionRequest|UpdateCollectionRequest $request): JsonResponse|array
    {
        $jsonFile = $request->file('json_file');

        if ($jsonFile->isValid()) {
            return json_decode($jsonFile->getContent(), true);
        }

        return response()->json([
            'error' => [
                'code' => 500,
                'message' => 'An error occurred'
            ]
        ]);
    }

}
