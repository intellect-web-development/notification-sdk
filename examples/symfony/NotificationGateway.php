<?php

declare(strict_types=1);

namespace App\Common\Notification;

use IWD\NotificationSdk\NotificationClient;
use IWD\NotificationSdk\Recipient;

/**
 * Пример адаптера продукта: единственная точка HTTP-связи с Notification.
 * Скопировать в продукт при opt-in; домен NotificationClient не импортирует.
 *
 * Команды ensureRecipient и send продукт кладёт в шину классами
 * IWD\NotificationSdk\Message\Command\* на транспорт notification-commands-transport.
 * Если сервис недоступен, исключение клиента уходит наверх: кабинет отвечает 502.
 */
final readonly class NotificationGateway
{
    public function __construct(private NotificationClient $notificationClient)
    {
    }

    public function recipient(int $userId): ?Recipient
    {
        return $this->notificationClient->recipient($userId);
    }

    /**
     * @param array<string, bool> $subscriptions
     */
    public function changeSubscriptions(int $userId, array $subscriptions): Recipient
    {
        return $this->notificationClient->changeSubscriptions($userId, $subscriptions);
    }

    public function unsubscribe(string $token): void
    {
        $this->notificationClient->unsubscribe($token);
    }
}
