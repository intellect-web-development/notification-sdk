<?php

declare(strict_types=1);

namespace IWD\NotificationSdk\Message\Command;

use Symfony\Component\Messenger\Attribute\AsMessage;

/**
 * Отправить уже собранное письмо и/или текст Telegram.
 * Сервис сам выбирает адрес. Выключенный или неизвестный Customer — ack без отправки.
 */
#[AsMessage(
    transport: 'notification-commands-transport',
    serializedTypeName: 'notification.command.send',
)]
readonly class SendMessage
{
    public const string ROUTE_SELECTED = 'selected';
    public const string ROUTE_SUBSCRIBED_EMAIL = 'subscribed_email';

    public function __construct(
        public int $userId,
        public string $integrationSystemCode,
        public string $route,
        public string $unsubscribeBaseUrl,
        public ?string $emailSubject = null,
        public ?string $emailTitle = null,
        public ?string $emailSubtitle = null,
        public ?string $emailButtonLink = null,
        public ?string $emailButtonLabel = null,
        public ?string $emailWelcomeText = null,
        public ?string $emailInfo = null,
        public ?string $emailMistake = null,
        public bool $emailNeedUnsubscribe = false,
        public ?string $telegramText = null,
    ) {
    }
}
