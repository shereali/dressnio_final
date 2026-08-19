<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CrudRepository;
use App\Http\Requests\Backend\PostRequest;
use App\Http\Resources\Backend\PostResource;

class PostController extends Controller
{

    public $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Post::class);
        $this->repository->setResource(PostResource::class);
        $this->repository->message->setMessage('Post');

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
    public function store(PostRequest $request)
    {

        return $this->repository->create($request->all());

        // $tag = new Tag();
        // $tag->name = 'hello';
        // $tag->save();
        // $post->tags()->attach($tag->id);
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
    public function update(PostRequest $request, string $id)
    {
        return $this->repository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return  $this->repository->destroy($id);
    }
}
