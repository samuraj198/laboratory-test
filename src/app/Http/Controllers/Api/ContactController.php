<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private ContactService $contactService)
    {}

    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = $this->contactService->store($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Запрос успешно сохранен в бд',
            'data' => $contact
        ], 201);
    }
}
