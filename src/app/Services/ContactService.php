<?php

namespace App\Services;

use App\Events\ContactCreated;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContactService
{

    public function store(array $data): Contact
    {
        $contact = Contact::create([
            'name' => Str::title($data['name']),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'comment' => $data['comment'],
        ]);
        Log::info("Запрос с id $contact->id сохранен в бд");

        event(new ContactCreated($contact));
        Log::info("Job AITryAnswerToContact для запроса с id $contact->id добавлен в очередь");
        Log::info("Отправка письма для запроса с id $contact->id добавлена в очередь");

        return $contact;
    }
}
