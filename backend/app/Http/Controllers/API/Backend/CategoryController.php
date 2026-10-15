<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\CrudRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public CrudRepository $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Category::class);
        $this->repository->message->setMessage('Category');
    }

    public function index(): JsonResponse
    {
        return $this->repository->fetchData();
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:categories,slug',
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'banner'      => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'order'       => 'nullable|integer',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->create($data);
    }

    public function show(int $id): JsonResponse
    {
        return $this->repository->find($id);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:categories,slug,' . $id,
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'banner'      => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'order'       => 'nullable|integer',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->update($id, $data);
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->repository->destroy($id);
    }
}
