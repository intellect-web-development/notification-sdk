<?php

declare(strict_types=1);

namespace IWD\NotificationSdk;

use InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Клиент универсального сервиса Notification (доставка писем и Telegram).
 *
 * Токен — персональный JWT системы (аудитория notification). Сервис берёт код системы из claim `sub`.
 * Текст сообщения продукт собирает сам и отправляет командой, не этим клиентом.
 * 404 у recipient() превращается в null. Остальные 4xx/5xx пробрасываются.
 */
final readonly class NotificationClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl,
        private string $token,
    ) {
    }

    /** Получатель и флаги транспортов. null — его нет или он принадлежит другой системе */
    public function recipient(int $userId): ?Recipient
    {
        $this->assertUserId($userId);
        try {
            return $this->mapRecipient($this->request('GET', '/users/' . $userId . '/recipient'));
        } catch (ClientExceptionInterface $exception) {
            if (404 === $exception->getResponse()->getStatusCode()) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * Сменить флаги подписки уже существующих транспортов. Ключ — код транспорта (`email`, `telegram`).
     *
     * @param array<string, bool> $subscriptions
     */
    public function changeSubscriptions(int $userId, array $subscriptions): Recipient
    {
        $this->assertUserId($userId);
        foreach ($subscriptions as $code => $subscribed) {
            if (!is_string($code) || '' === $code || !is_bool($subscribed)) {
                throw new InvalidArgumentException('subscriptions: код транспорта и bool.');
            }
        }

        return $this->mapRecipient($this->request('PATCH', '/users/' . $userId . '/subscriptions', [
            'json' => ['subscriptions' => $subscriptions],
        ]));
    }

    /** Снять подписку по токену из ссылки. Чужой токен сервис отвечает как 404 */
    public function unsubscribe(string $token): void
    {
        if ('' === $token) {
            throw new InvalidArgumentException('token: непустая строка.');
        }
        $this->request('POST', '/transports/unsubscribe', [
            'json' => ['token' => $token],
        ]);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $options = []): array
    {
        $options['headers'] = ['Authorization' => 'Bearer ' . $this->token];
        $response = $this->httpClient->request($method, rtrim($this->baseUrl, '/') . '/api/sdk' . $path, $options);
        if (204 === $response->getStatusCode()) {
            return [];
        }

        return $response->toArray();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function mapRecipient(array $data): Recipient
    {
        $transports = [];
        foreach ($data['transports'] ?? [] as $transport) {
            $transports[] = new TransportSubscription(
                id: (int) $transport['id'],
                code: (string) $transport['code'],
                notificationSubscribed: (bool) $transport['notificationSubscribed'],
                approved: (bool) $transport['approved'],
            );
        }

        return new Recipient(userId: (int) $data['userId'], transports: $transports);
    }

    private function assertUserId(int $userId): void
    {
        if ($userId < 1) {
            throw new InvalidArgumentException('userId: положительное число.');
        }
    }
}
