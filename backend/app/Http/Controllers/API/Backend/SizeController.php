<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\SizeRequest;
use App\Http\Resources\Backend\SizeResource;
use App\Repositories\CrudRepository;
use Illuminate\Validation\ValidationException;

class SizeController extends Controller
{

    public $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Size::class);
        $this->repository->setResource(SizeResource::class);
        $this->repository->message->setMessage('Size');
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
    public function store(SizeRequest $request)
    {
        // return $request->all();

        $data = [];

        switch ($request->size_type) {
            case 1:
                foreach ($request->standard as $key => $item) {
                    $data[$key]['size_type'] = $request->size_type;
                    $data[$key]['standard']  = $item;
                    $data[$key]['bust']      = $request->bust[$key];
                    $data[$key]['waist']     = $request->waist[$key];
                    $data[$key]['hip']       = $request->hip[$key];
                }
                break;
            case 2:
                foreach ($request->standard as $key => $item) {
                    $data[$key]['size_type'] = $request->size_type;
                    $data[$key]['standard']  = $item;
                    $data[$key]['lengths']   = $request->lengths[$key];
                    $data[$key]['waist']     = $request->waist[$key];
                    $data[$key]['hip']       = $request->hip[$key];
                    $data[$key]['thighs']    = $request->thighs[$key];
                }
                break;

            default:
                # code...
                break;
        }

        // return $data;

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
    public function update(SizeRequest $request, string $id)
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
