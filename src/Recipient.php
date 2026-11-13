<?php

declare(strict_types=1);

namespace IWD\NotificationSdk;

/** Получатель Customer, которому выписан токен */
final readonly class Recipient
{
    /**
     * @param list<TransportSubscription> $transports
     */
    public function __construct(
        public int $userId,
        public array $transports,
    ) {
    }
}
