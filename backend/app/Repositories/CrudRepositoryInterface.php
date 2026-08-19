<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;

interface CrudRepositoryInterface
{
    public function fetchData(?int $perPage = null): JsonResponse;
    public function create(array $data): JsonResponse;
    public function bulkCreation(array $data): JsonResponse;
    public function find(int $id): JsonResponse;
    public function update(int $id, array $data): JsonResponse;
    public function destroy(int $id): JsonResponse;
}
