<?php

declare(strict_types=1);

namespace IWD\NotificationSdk;

/** Флаги транспорта. Адреса email и chat id сервис в этот ответ не кладёт */
final readonly class TransportSubscription
{
    public function __construct(
        public int $id,
        public string $code,
        public bool $notificationSubscribed,
        public bool $approved,
    ) {
    }
}
