<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\WatchAndBuy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CrudRepository;
use App\Http\Requests\Backend\WatchAndBuyRequest;
use App\Http\Resources\Backend\WatchAndBuyResource;

class WatchAndBuyController extends Controller
{
    public $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(WatchAndBuy::class);
        $this->repository->setResource(WatchAndBuyResource::class);
        $this->repository->message->setMessage('Watch And Buy');

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
    public function store(WatchAndBuyRequest $request)
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
    public function update(WatchAndBuyRequest $request, string $id)
    {
        return $this->repository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->repository->destroy($id);
    }
}
