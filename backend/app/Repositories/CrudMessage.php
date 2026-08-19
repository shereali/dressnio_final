<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;

class CrudMessage implements CrudMessageInterface
{
    public ?string $message = null;

    public mixed $data = null;

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function createMsg(): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $this->message . ' has been created',
            'data'    => $this->data,
        ], 201);
    }

    public function updateMsg(): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'One ' . $this->message . ' has been updated',
            'data'    => $this->data,
        ]);
    }

    public function deleteMsg(): JsonResponse
    {
        return response()->json([
            'status'  => 'danger',
            'message' => 'One ' . $this->message . ' has been deleted',
            'data'    => null,
        ]);
    }
}
