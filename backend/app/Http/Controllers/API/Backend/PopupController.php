<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Popup;
use App\Repositories\CrudRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public CrudRepository $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Popup::class);
        $this->repository->message->setMessage('Popup');
    }

    public function index(): JsonResponse
    {
        return $this->repository->fetchData();
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'subtitle'      => 'nullable|string',
            'content'       => 'nullable|string',
            'image'         => 'nullable|string',
            'button_text'   => 'nullable|string',
            'button_url'    => 'nullable|string',
            'delay_seconds' => 'nullable|integer',
            'is_active'     => 'nullable|boolean',
        ]);

        return $this->repository->create($request->all());
    }

    public function show(int $id): JsonResponse
    {
        return $this->repository->find($id);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'subtitle'      => 'nullable|string',
            'content'       => 'nullable|string',
            'image'         => 'nullable|string',
            'button_text'   => 'nullable|string',
            'button_url'    => 'nullable|string',
            'delay_seconds' => 'nullable|integer',
            'is_active'     => 'nullable|boolean',
        ]);

        return $this->repository->update($id, $request->all());
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->repository->destroy($id);
    }
}
