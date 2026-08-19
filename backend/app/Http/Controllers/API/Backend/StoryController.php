<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Story;
use Illuminate\Support\Str;
use App\Models\ProductStory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CrudRepository;
use App\Http\Requests\Backend\StoryRequest;
use App\Http\Resources\Backend\StoryResource;

class StoryController extends Controller
{

    public $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Story::class);
        $this->repository->setResource(StoryResource::class);
        $this->repository->message->setMessage('Story');

        // $this->repository->setQuery(['parent_id' => 1]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return $this->repository->fetchData();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoryRequest $request)
    {
        // return $request->all();

        $this->repository->create(
            [
                'name' => $request->name,
                'media_id' => $request->media_id,
                'slug' => Str::slug($request->name),

            ]
        );

        $result = $this->repository->message->getData();

        $this->repository->setModel(ProductStory::class);

        $data = [];

        foreach ($request->product_id as $key => $item) {
            $data[$key]['story_id'] = $result->id;
            $data[$key]['product_id'] = $item;
        }

        return $this->repository->bulkCreation($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoryRequest $request, string $id)
    {
        $this->repository->update([
            'name' => $request->name,
            'media_id' => $request->media_id,
            'slug' => Str::slug($request->name)
        ], $id);

        ProductStory::where('story_id', $id)->delete();

        $this->repository->setModel(ProductStory::class);

        $data = [];

        foreach ($request->product_id as $key => $item) {
            $data[$key]['story_id'] = $id;
            $data[$key]['product_id'] = $item;
        }

        return $this->repository->bulkCreation($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->repository->destroy($id);
        $story = ProductStory::where('story_id', $id)->forceDelete();

        return $result;
    }
}
