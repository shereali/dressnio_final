<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Color;
use App\Models\ColorSet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CrudRepository;
use App\Http\Requests\Backend\ColorRequest;
use App\Http\Resources\Backend\ColorResource;
use App\Http\Requests\Backend\ColorSetRequest;
use App\Http\Resources\Backend\ColorSetResource;

class ColorController extends Controller
{

    public $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(ColorSet::class);
        $this->repository->setResource(ColorSetResource::class);
        $this->repository->message->setMessage('Color');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        return $this->repository->fetchData();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ColorSetRequest $request)
    {

        $this->repository->create(
            [
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]
        );

        $result = $this->repository->message->getData();

        $this->repository->setModel(Color::class);

        $data = [];

        foreach ($request->color_code as $key => $item) {
            $data[$key]['color_set_id'] = $result->id;
            $data[$key]['color_code'] = $request->color_code[$key];
            $data[$key]['color_name'] = $request->color_name[$key];
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
    public function update(ColorSetRequest $request, string $id)
    {
        $this->repository->update(['name' => $request->name, 'slug' => Str::slug($request->name)], $id);

        Color::where('color_set_id', $id)->delete();

        $this->repository->setModel(Color::class);

        $data = [];

        foreach ($request->color_code as $key => $item) {
            $data[$key]['color_set_id'] = $id;
            $data[$key]['color_code'] = $request->color_code[$key];
        }

        return $this->repository->bulkCreation($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->repository->destroy($id);
        $color = Color::where('color_set_id', $id)->forceDelete();

        return $result;
    }
}
