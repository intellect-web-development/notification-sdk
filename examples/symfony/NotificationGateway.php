<?php

declare(strict_types=1);

namespace IWD\NotificationSdk\Examples\Symfony;

use IWD\NotificationSdk\NotificationClient;
use IWD\NotificationSdk\Recipient;

/**
 * Пример адаптера продукта: единственная точка HTTP-связи с Notification.
 * В продукте копируйте в App\PlatformProjection\Notification (или свой BC) при opt-in; домен NotificationClient не импортирует.
 *
 * Команды ensureRecipient, send и sendToAddress продукт кладёт в шину классами
 * IWD\NotificationSdk\Message\Command\* на транспорт notification-commands-transport.
 * HTML письма собирает сервис из полей. sendToAddress — адрес напрямую, без userId.
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
