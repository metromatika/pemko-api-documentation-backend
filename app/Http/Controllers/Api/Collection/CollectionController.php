<?php

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Interfaces\CollectionInterface;
use App\Models\Collection;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class CollectionController extends Controller
{
    use ApiResponser;

    /**
     * @var Collection
     */
    private Collection $collectionModel;

    /**
     * @var CollectionInterface
     */
    private CollectionInterface $collectionRepository;

    public function __construct(Collection $collectionModel, CollectionInterface $collectionRepository)
    {
        $this->collectionModel = $collectionModel;
        $this->collectionRepository = $collectionRepository;
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $collections = $this->collectionModel;

        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                $collections = $collections->admin()
                    ->when($request->has('access_type'), function ($query) use ($request) {
                        return $query->where('access_type', $request->get('access_type'));
                    });
            } else if (Auth::user()->isProgrammer()) {
                $collections = $collections->when($request->has('get'), function ($query) use ($request) {
                    if ($request->get('get') == 'self') {
                        return $query->programmer(Auth::user()->id)
                            ->when($request->has('access_type'), function ($query) use ($request) {
                                return $query->where('access_type', $request->get('access_type'));
                            });
                    }

                    return $query->programmer(Auth::user()->id)
                        ->orWhere('access_type', Collection::COLLECTION_ACCESS_TYPE_PUBLIC);
                });
            }
        } else {
            $collections = $collections->public();
        }

        $collections = $collections->when($request->has('project_name'), function ($query) use ($request) {
            return $query->where('project_name', 'like', '%' . $request->get('project_name') . '%');
        })->orderByDesc('created_at');

        $collections = $collections->paginate(6);

        if ($collections->isEmpty()) {
            return $this->successResponse([], 'No collection found');
        }

        return $this->successResponse($collections, 'Collections retrieved successfully');
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
            $collection = $this->collectionModel->create([
                'user_id' => Auth::user()->id,
                'project_name' => $request->validated('project_name'),
                'access_type' => $request->validated('access_type'),
                'json_file' => $jsonContent
            ]);

            if ($request->has('source_code_file')) {
                $this->collectionRepository->uploadSourceCode($request->file('source_code_file'), $collection);
            }
            return $collection;
        });

        return $this->successResponse($collection, 'Collection created successfully', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Collection $collection
     * @return JsonResponse
     */
    public function show(Collection $collection)
    {
        if (Auth::check()) {

            $user = Auth::user();
            $collection = $collection->load('sourceCode');

            if ($user->isProgrammer()) {
                if ($collection->user_id == $user->id) {
                    return $this->successResponse($collection, 'Collection retrieved successfully');
                }
            }

            if ($user->isAdmin()) {
                return $this->successResponse($collection, 'Collection retrieved successfully');
            }
        }

        if ($collection->access_type == Collection::COLLECTION_ACCESS_TYPE_PUBLIC) {
            return $this->successResponse($collection, 'Collection retrieved successfully');
        }

        return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
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
        if (Auth::user()->id == $collection->user_id) {
            $input = [
                'project_name' => $request->validated('project_name') ?? $collection->project_name,
                'access_type' => $request->validated('access_type') ?? $collection->access_type,
            ];

            if ($request->hasFile('json_file'))
                $input['json_file'] = $this->getJSONContent($request);

            DB::transaction(function () use ($input, $collection) {
                return $collection->update($input);
            });

            return response()->json([
                'message' => 'Collection updated successfully',
                'data' => $collection->refresh()
            ]);
        }

        return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Collection $collection
     * @return JsonResponse
     */
    public function destroy(Collection $collection)
    {
        if (Auth::user()->isProgrammer()) {
            if ($collection->user_id != Auth::user()->id) {
                return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
            }
        }

        DB::transaction(function () use ($collection) {
            $this->collectionRepository->deleteSourceCode($collection);
            $collection->delete();
        });

        return $this->noContentResponse();
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

        return $this->errorResponse('Invalid JSON file', Response::HTTP_BAD_REQUEST);
    }


}
