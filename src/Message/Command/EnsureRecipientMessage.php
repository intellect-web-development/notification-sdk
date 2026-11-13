<?php

declare(strict_types=1);

namespace IWD\NotificationSdk\Message\Command;

use Symfony\Component\Messenger\Attribute\AsMessage;

/**
 * Идемпотентно заводит получателя и делает email выбранным транспортом.
 * Повтор уже существующего получателя флаги не перезаписывает.
 */
#[AsMessage(
    transport: 'notification-commands-transport',
    serializedTypeName: 'notification.command.ensureRecipient',
)]
readonly class EnsureRecipientMessage
{
    public function __construct(
        public int $userId,
        public string $email,
        public bool $approved,
        public bool $notificationSubscribed,
        public string $integrationSystemCode,
    ) {
    }
}
