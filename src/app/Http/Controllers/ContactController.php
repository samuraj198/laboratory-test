<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function __construct(private ContactService $contactService)
    {}

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $contact = $this->contactService->store($request->validated());

        return back();
    }
}
