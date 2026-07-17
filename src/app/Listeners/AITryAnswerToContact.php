<?php

namespace App\Listeners;

use App\Events\ContactCreated;
use App\Models\Answer;
use App\Models\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Tigusigalpa\GigaChat\Laravel\GigaChat;
use Tigusigalpa\GigaChat\Models\GigaChatModels;

class AITryAnswerToContact implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    public function handle(ContactCreated $event): void
    {
        try {
            $response = GigaChat::chat([
                [
                    'role' => 'user',
                    'content' => "Ты — автоматический классификатор вопросов.
                Твоя задача — оценить сложность вопроса пользователя по шкале
                от 1 до 10 и вернуть ответ СТРОГО по правилам.

                            ПРАВИЛА ОТВЕТА:
                            1. Если вопрос СЛОЖНЫЙ (6-10 баллов), или требует актуальных данных в реальном времени
                            (например: точное время, погода, курс валют), или требует личного мнения —
                            ты должен вернуть ровно одно слово: [EMPTY]
                            2. Если вопрос ЛЕГКИЙ (1-5 баллов) и это общий факт —
                            ты должен просто развернуто и понятно ответить на него.
                            3. ЗАПРЕЩЕНО писать фразы вида 'Легкий ответ:', 'Ответ:', писать оценку,
                            баллы или рассуждения. Начинай писать СРАЗУ с самого текста ответа или только [EMPTY].

                            ПРИМЕРЫ СЛОЖНЫХ ВОПРОСОВ (на них отвечай только словом [EMPTY]):
                            - Сколько сейчас времени в Лондоне?
                            - Какая завтра погода в Москве?
                            - Какую видеокарту мне купить для игр?

                            ПРИМЕРЫ ЛЕГКИХ ВОПРОСОВ (на них отвечай обычным текстом):
                            - Что такое фотосинтез?
                            - Сколько будет 25 умножить на 4?
                            - Кто написал Войну и мир?

                            Вопрос пользователя: \"{$event->contact->comment}\""
                ]
            ], [
                'model' => GigaChatModels::GIGACHAT_2
            ]);

            $answer = trim($response['choices'][0]['message']['content'] ?? '');

            if ($answer !== '[EMPTY]' && $answer !== '') {
                Answer::firstOrCreate([
                    'contact_id' => $event->contact->id
                ], [
                    'answer' => $answer,
                ]);
                Log::info('GigaСhat ответил на вопрос с id ' . $event->contact->id);
            } else {
                Log::info('GigaСhat не смог ответить на вопрос с id ' . $event->contact->id);
            }
        } catch (\Exception $e) {
            Log::info("GigaChat AI не смог ответить на запрос. ERROR - " . $e->getMessage());

            throw $e;
        }

    }
}
