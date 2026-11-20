<?php

declare(strict_types=1);

namespace IWD\NotificationSdk\Message\Command;

use Symfony\Component\Messenger\Attribute\AsMessage;

/**
 * Отправить письмо на адрес напрямую.
 * Сервис собирает HTML. Получателя и подписку не проверяет.
 * Выключенный или неизвестный Customer — ack без отправки.
 */
#[AsMessage(
    transport: 'notification-commands-transport',
    serializedTypeName: 'notification.command.sendToAddress',
)]
readonly class SendToAddressMessage
{
    public function __construct(
        public string $to,
        public string $integrationSystemCode,
        public string $unsubscribeBaseUrl,
        public string $emailSubject,
        public string $emailTitle,
        public string $emailInfo,
        public ?string $emailSubtitle = null,
        public ?string $emailButtonLink = null,
        public ?string $emailButtonLabel = null,
        public ?string $emailWelcomeText = null,
        public ?string $emailMistake = null,
        public bool $emailNeedUnsubscribe = false,
        public ?string $appName = null,
        public ?string $appUrl = null,
    ) {
    }
}
