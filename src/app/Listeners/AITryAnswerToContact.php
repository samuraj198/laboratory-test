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
                    'content' => "Ты — жестко ограниченный классификатор сообщений из формы обратной связи сайта.
                        Твоя задача — мгновенно оценить тип вопроса пользователя и вернуть ответ СТРОГО по правилам.

                        КРИТЕРИИ ОЦЕНКИ ЗАПРОСА:
                        - ЛЕГКИЙ / ОБЩИЙ ВОПРОС: общеизвестный факт, базовая математика,
                        школьные знания, термины, общие определения.
                        - СЛОЖНЫЙ / СПЕЦИФИЧНЫЙ ВОПРОС: вопросы по работе данного сайта,
                        технические проблемы, жалобы, отзывы, вопросы о личном кабинете, заказах,
                        оплате, багах, или запросы актуальных данных (погода, курсы, время).

                        ПРАВИЛА ФОРМАТИРОВАНИЯ ОТВЕТА (НАРУШЕНИЕ ЗАПРЕЩЕНО):
                        1. Если вопрос СЛОЖНЫЙ, специфичный для сайта или
                        требует действий техподдержки — выведи ТОЛЬКО ЭТИ 7 СИМВОЛОВ: [EMPTY]
                        2. Если вопрос ЛЕГКИЙ и общий — начни писать СРАЗУ с текста ответа.
                        Пиши развернуто, понятно и по существу.

                        КАТЕГОРИЧЕСКИ ЗАПРЕЩЕНО:
                        - Писать фразы вроде 'Передаю оператору', 'Я не могу ответить', вступления или пояснения.
                        - Использовать префиксы и маркеры вроде 'Ответ:', 'Результат:', 'Комментарий:'.
                        - Если вопрос сложный — в твоем ответе должно быть ровно 7 символов: [EMPTY] и ничего больше.

                        ПРИМЕРЫ СЛОЖНЫХ ВОПРОСОВ ДЛЯ САЙТА (Ответ: [EMPTY]):
                        - Почему у меня не работает кнопка оплаты в корзине?
                        - Как мне восстановить пароль от личного кабинета вашего сайта?
                        - Где посмотреть статус моего заказа №12345?
                        - Сайт ужасно тормозит, исправьте это.

                        ПРИМЕРЫ ЛЕГКИХ ВОПРОСОВ (Ответ: сразу текст):
                        - Подскажите, что означает термин 'диджитал'? (Пример ответа:
                        Термин 'диджитал' происходит от английского...)
                        - Сколько будет 15 процентов от 2000?
                        (Пример ответа: 15 процентов от 2000 составляет ровно 300.)

                        Текст обратной связи от пользователя: \"{$event->contact->comment}\""
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
