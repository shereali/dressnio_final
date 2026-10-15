<?php

namespace App\Repositories;

use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CrudRepository implements CrudRepositoryInterface
{
    /** @var class-string<Model>|null */
    protected ?string $modelClass = null;

    /** @var class-string<JsonResource>|null */
    protected ?string $resourceClass = null;

    /** @var array<int, string> */
    protected array $with = [];

    /** @var array<int, string> */
    protected array $scopes = [];

    public CrudMessage $message;

    public function __construct()
    {
        $this->message = new CrudMessage();
    }

    /**
     * Set the Eloquent model class.
     *
     * @param class-string<Model> $model
     */
    public function setModel(string $model): self
    {
        $this->modelClass = $model;
        return $this;
    }

    /**
     * Set the API Resource class.
     *
     * @param class-string<JsonResource> $resource
     */
    public function setResource(string $resource): self
    {
        $this->resourceClass = $resource;
        return $this;
    }

    /**
     * Set eager load relationships.
     *
     * @param array<int, string> $with
     */
    public function setWith(array $with): self
    {
        $this->with = $with;
        return $this;
    }

    /**
     * Get a fresh query builder for the model.
     */
    protected function newQuery(): Builder
    {
        if (! $this->modelClass) {
            throw new \RuntimeException('Model class is not set on CrudRepository.');
        }

        $query = $this->modelClass::query();

        if (! empty($this->with)) {
            $query->with($this->with);
        }

        return $query;
    }

    /**
     * Transform a model or collection using the configured resource class.
     */
    protected function transform(mixed $data): mixed
    {
        if ($this->resourceClass && $data !== null) {
            if ($data instanceof LengthAwarePaginator) {
                return $this->resourceClass::collection($data)->response()->getData(true);
            }
            if ($data instanceof Model) {
                return new $this->resourceClass($data);
            }
            if (is_iterable($data)) {
                return $this->resourceClass::collection($data);
            }
        }

        return $data;
    }

    public function fetchData(?int $perPage = null): JsonResponse
    {
        $perPage = $perPage ?? (int) request()->query('per_page', 15);
        $search = request()->query('search');
        $status = request()->query('status');
        $orderBy = request()->query('order_by', 'id');
        $orderDirection = request()->query('order_direction', 'desc');

        $query = $this->newQuery();

        if ($search && method_exists($this->modelClass, 'scopeSearch')) {
            $query->search($search);
        } elseif ($search && in_array('name', (new $this->modelClass)->getFillable())) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($status !== null && in_array('is_active', (new $this->modelClass)->getFillable())) {
            $query->where('is_active', filter_var($status, FILTER_VALIDATE_BOOLEAN));
        }

        $query->orderBy($orderBy, $orderDirection);

        if ($perPage === -1 || request()->query('all') === 'true') {
            $records = $query->get();
            return ApiResponse::success($this->transform($records), 'Records fetched successfully');
        }

        $paginated = $query->paginate($perPage);
        $transformed = $this->transform($paginated);

        if (is_array($transformed) && isset($transformed['data'])) {
            return ApiResponse::success(
                $transformed['data'],
                'Records fetched successfully',
                200,
                $transformed['meta'] ?? []
            );
        }

        return ApiResponse::success($transformed, 'Records fetched successfully');
    }

    public function create(array $data): JsonResponse
    {
        $record = $this->modelClass::create($data);

        if (! empty($this->with)) {
            $record->load($this->with);
        }

        $this->message->setData($this->transform($record));
        return $this->message->createMsg();
    }

    public function bulkCreation(array $data): JsonResponse
    {
        $created = [];
        foreach ($data as $item) {
            $created[] = $this->modelClass::create($item);
        }

        return ApiResponse::created($this->transform($created), 'Bulk records created successfully');
    }

    public function find(int $id): JsonResponse
    {
        $record = $this->newQuery()->find($id);

        if (! $record) {
            return ApiResponse::notFound($this->message->getMessage() . ' not found');
        }

        return ApiResponse::success($this->transform($record));
    }

    public function update(int $id, array $data): JsonResponse
    {
        $record = $this->modelClass::find($id);

        if (! $record) {
            return ApiResponse::notFound($this->message->getMessage() . ' not found');
        }

        $record->update($data);

        if (! empty($this->with)) {
            $record->load($this->with);
        }

        $this->message->setData($this->transform($record));
        return $this->message->updateMsg();
    }

    public function destroy(int $id): JsonResponse
    {
        $record = $this->modelClass::find($id);

        if (! $record) {
            return ApiResponse::notFound($this->message->getMessage() . ' not found');
        }

        $record->delete();
        return $this->message->deleteMsg();
    }
}
