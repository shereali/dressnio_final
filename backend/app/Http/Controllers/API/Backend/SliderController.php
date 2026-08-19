<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CrudRepository;
use App\Http\Requests\Backend\SliderRequest;
use App\Http\Resources\Backend\SliderResource;

class SliderController extends Controller
{

    public $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Slider::class);
        $this->repository->setResource(SliderResource::class);
        $this->repository->message->setMessage('Slider');

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
    public function store(SliderRequest $request)
    {
        // return $request->all();
        return $this->repository->create($request->all());
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
    public function update(SliderRequest $request, string $id)
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
