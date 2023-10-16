<?php

namespace App\Http\Controllers\Api\SourceCode;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSourceCodeRequest;
use App\Interfaces\CollectionInterface;
use App\Models\SourceCode;
use App\Models\Collection;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SourceCodeController extends Controller
{
    use ApiResponser;

    /**
     * @var SourceCode
     */
    protected sourceCode $sourceCodeModel;

    /**
     * @var Collection
     */
    protected Collection $collectionModel;

    /**
     * @var CollectionInterface
     */
    protected CollectionInterface $collectionRepository;

    public function __construct(
        SourceCode          $sourceCodeModel,
        Collection          $collectionModel,
        CollectionInterface $collectionRepository)
    {
        $this->sourceCodeModel = $sourceCodeModel;
        $this->collectionModel = $collectionModel;
        $this->collectionRepository = $collectionRepository;
    }

    /**
     * Retrieve a listing of the source code.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $sourceCode = $this->sourceCodeModel
            ->when($user->isProgrammer(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when($user->isAdmin(), function ($query) {
                return $query;
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        if ($sourceCode->isEmpty())
            return $this->errorResponse('The requested resource could not be found.', Response::HTTP_NOT_FOUND);

        return $this->successResponse($sourceCode);
    }

    /**
     * Store a new source code.
     *
     * @param StoreSourceCodeRequest $request
     * @return JsonResponse
     */
    public function store(StoreSourceCodeRequest $request): JsonResponse
    {
        $collection = $this->collectionModel
            ->select(['id', 'user_id', 'project_name'])
            ->find($request->validated('collection_id'));


        if (!$collection) {
            return $this->errorResponse('Collection not found', Response::HTTP_NOT_FOUND);
        }

        DB::transaction(function () use ($request, $collection) {
            $this->collectionRepository->uploadSourceCode($request->file('source_code_file'), $collection);
        });

        return $this->successResponse($collection->load('sourceCode'), 'Source code created successfully', Response::HTTP_CREATED);
    }

    /**
     * Show the source code.
     *
     * @param SourceCode $sourceCode
     * @return JsonResponse
     */
    public function show(SourceCode $sourceCode)
    {
        $collection = $sourceCode->load('collection')->collection;
        if (auth()->user()->isProgrammer())
            if ($collection->user_id != Auth::user()->id)
                return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);

        return $this->successResponse($sourceCode);
    }


    public function update()
    {
        return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
    }


    public function destroy(SourceCode $sourceCode)
    {
        $collection = $sourceCode->load('collection')->collection;

        if (auth()->user()->isProgrammer())
            if ($collection->user_id != Auth::user()->id)
                return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);

        Storage::delete($sourceCode->file_path);
        $sourceCode->delete();

        return $this->noContentResponse();
    }

    /**
     * Download the source code.
     *
     * @param SourceCode $sourceCode
     * @return BinaryFileResponse
     */
    public function download(SourceCode $sourceCode)
    {
        return response()->download(storage_path('app/public/' . $sourceCode->file_path));
    }

    private function handleUploadFile(UploadedFile $file, SourceCode $sourceCode)
    {
        $file_path = $file->storeAs('source-code', $this->generateFileName($file));
        $sourceCode->file_path = $file_path;
        return $sourceCode;
    }

    /**
     * Generates a unique file name for the uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function generateFileName(UploadedFile $file)
    {
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileExtension = $file->getClientOriginalExtension();
        return $fileName . '-' . uniqid() . '.' . $fileExtension;
    }
}
