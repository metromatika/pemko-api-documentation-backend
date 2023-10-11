<?php

namespace App\Http\Controllers\Api\SourceCode;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSourceCodeRequest;
use App\Models\SourceCode;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

    public function __construct(SourceCode $sourceCodeModel)
    {
        $this->sourceCodeModel = $sourceCodeModel;
    }

    /**
     * Retrieve a listing of the source code.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $sourceCode = $this->sourceCodeModel
            ->when(auth()->user()->isProgrammer(), function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })
            ->when(auth()->user()->isAdmin(), function ($query) {
                return $query;
            })
            ->when($request->has('name'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
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
        $sourceCode = DB::transaction(function () use ($request) {
            $sourceCode = $this->sourceCodeModel
                ->create([
                    'name' => $request->validated('name'),
                    'user_id' => auth()->user()->id
                ]);

            $sourceCode = $this->handleUploadFile($request->file('file'), $sourceCode);
            $sourceCode->save();

            return $sourceCode;
        });

        return $this->successResponse($sourceCode, Response::HTTP_CREATED);
    }

    /**
     * Show the source code.
     *
     * @param SourceCode $sourceCode
     * @return JsonResponse
     */
    public function show(SourceCode $sourceCode)
    {
        if (auth()->user()->isProgrammer())
            if ($sourceCode->user_id != auth()->user()->id)
                return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);

        return $this->successResponse($sourceCode);
    }


    public function update()
    {
        return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
    }


    public function destroy(SourceCode $sourceCode)
    {
        if (auth()->user()->isProgrammer())
            if ($sourceCode->user_id != auth()->user()->id)
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
        return response()->download($sourceCode->file_url);
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
