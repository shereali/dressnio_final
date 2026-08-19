<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CrudRepository;
use App\Http\Requests\Backend\TestimonialRequest;
use App\Http\Resources\Backend\TestimonialResource;

class TestimonialController extends Controller
{
    public $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Testimonial::class);
        $this->repository->setResource(TestimonialResource::class);
        $this->repository->message->setMessage('Testimonial');

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
    public function store(TestimonialRequest $request)
    {
        // return $request->all();
        $response =  $this->repository->create($request->all());
        return $response;
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
    public function update(TestimonialRequest $request, string $id)
    {
        return $this->repository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->repository->destroy($id);
    }
}
