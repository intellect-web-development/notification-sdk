# notification-sdk

HTTP-клиент и контракты команд универсального сервиса Notification (доставка писем и Telegram). Пакет `intellect-web-development/notification-sdk`, namespace `IWD\NotificationSdk\`.

Чеклист подключения продукта: корневой `../notification/INTEGRATION.md`.

Сервис не знает оферты и торговые сигналы. Продукт решает, кому писать, собирает текст и либо читает подписку по HTTP, либо кладёт команду в `notification.commands`.

```php
$recipient = $client->recipient(7);
$client->changeSubscriptions(7, ['email' => false]);
$client->unsubscribe($token);
```

Токен — персональный JWT (аудитория `notification`, scopes `notification:recipients:read` и `notification:recipients:write`).

Команды: `notification.command.ensureRecipient`, `notification.command.send`. Классы — `IWD\NotificationSdk\Message\Command\*`. Транспорт продукта — `examples/symfony/messenger.yaml` (`queues: false`).
