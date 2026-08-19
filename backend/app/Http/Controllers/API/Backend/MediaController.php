<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Backend\MediaRequest;
use App\Http\Resources\Backend\MediaResource;
use App\Repositories\CrudRepository;

class MediaController extends Controller
{
    public CrudRepository $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Media::class);
        $this->repository->setResource(MediaResource::class);
        $this->repository->message->setMessage('Media');
    }

    public function index(Request $request)
    {
        $query = Media::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderByDesc('id');

        $paginator = $query->paginate($request->integer('per_page', 30))->withQueryString();

        return ApiResponse::success(
            MediaResource::collection($paginator),
            'OK',
            200,
            ['pagination' => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ]]
        );
    }

    /**
     * Store uploaded files (images or videos) with validation and random names.
     */
    public function store(MediaRequest $request)
    {
        $files = $request->file('images', $request->file('files', []));
        if (! is_array($files)) {
            $files = [$files];
        }

        if (count($files) === 0) {
            return ApiResponse::error('No file provided', 422);
        }

        $uploaded = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $isVideo = in_array($extension, ['mp4', 'webm', 'mov', 'ogg']);

            $rules = $isVideo
                ? ['mimes:mp4,webm,mov,ogg', 'max:102400']
                : ['mimes:jpg,jpeg,png,webp,gif,svg', 'max:5120'];

            $validator = validator(['file' => $file], ['file' => $rules]);

            if ($validator->fails()) {
                return ApiResponse::error('Invalid file: ' . $file->getClientOriginalName(), 422, $validator->errors());
            }

            $filename = Str::random(24) . '.' . $extension;
            $path = $file->storeAs('public/uploads', $filename);

            $media = Media::create([
                'name'        => $filename,
                'slug'        => $filename,
                'media_url'   => '/storage/uploads/' . $filename,
                'category_id' => $request->category_id ?? 1,
                'media_type'  => $isVideo ? 2 : 1,
            ]);

            $uploaded[] = new MediaResource($media);
        }

        return ApiResponse::created(count($uploaded) === 1 ? $uploaded[0] : $uploaded, 'Files uploaded successfully');
    }

    public function show(string $id)
    {
        return $this->repository->find((int) $id);
    }

    public function update(Request $request, string $id)
    {
        return $this->repository->update($request->all(), (int) $id);
    }

    public function destroy(string $id)
    {
        $model = Media::find($id);

        if (! $model) {
            return ApiResponse::notFound('Media not found');
        }

        if ($model->media_url && str_contains($model->media_url, '/storage/uploads/')) {
            Storage::delete('public/uploads/' . basename($model->media_url));
        }

        $model->forceDelete();

        return ApiResponse::success(null, 'Media deleted successfully');
    }
}
