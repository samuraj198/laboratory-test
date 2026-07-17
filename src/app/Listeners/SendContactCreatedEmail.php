<?php

namespace App\Listeners;

use App\Events\ContactCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendContactCreatedEmail implements ShouldQueue
{
    use Queueable;

    public function handle(ContactCreated $event): void
    {
        Mail::html("
            <h2>Создан новый запрос</h2>
            <ul>
                <li><b>Имя:</b> {$event->contact->name}</li>
                <li><b>Телефон:</b> {$event->contact->phone}</li>
                <li><b>Email:</b> {$event->contact->email}</li>
                <li><b>Комментарий:</b> {$event->contact->comment}</li>
            </ul>
", function ($message) use ($event) {
            $message->to($event->contact->email)
                ->subject('Создан новый запрос на сайте');
        });
    }
}
