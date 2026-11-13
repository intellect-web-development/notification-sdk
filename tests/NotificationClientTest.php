<?php

declare(strict_types=1);

namespace IWD\NotificationSdk\Tests;

use InvalidArgumentException;
use IWD\NotificationSdk\NotificationClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class NotificationClientTest extends TestCase
{
    private const string RECIPIENT = '{"userId":7,"transports":[{"id":1,"code":"email","notificationSubscribed":true,"approved":true}]}';

    /** @var array{method?: string, url?: string, body?: string} */
    private array $captured = [];

    public function testRecipientMapsTransports(): void
    {
        $recipient = $this->client(self::RECIPIENT)->recipient(7);

        self::assertNotNull($recipient);
        self::assertSame('GET', $this->captured['method']);
        self::assertSame('http://notification.test/api/sdk/users/7/recipient', $this->captured['url']);
        self::assertSame(7, $recipient->userId);
        self::assertCount(1, $recipient->transports);
        self::assertSame('email', $recipient->transports[0]->code);
        self::assertTrue($recipient->transports[0]->notificationSubscribed);
    }

    public function testRecipientReturnsNullOnNotFound(): void
    {
        self::assertNull($this->client('{"message":"not found"}', 404)->recipient(7));
    }

    public function testChangeSubscriptionsPatchesFlags(): void
    {
        $recipient = $this->client(self::RECIPIENT)->changeSubscriptions(7, ['email' => false]);

        self::assertSame('PATCH', $this->captured['method']);
        self::assertSame('http://notification.test/api/sdk/users/7/subscriptions', $this->captured['url']);
        self::assertSame(
            ['subscriptions' => ['email' => false]],
            json_decode((string) $this->captured['body'], true, 512, JSON_THROW_ON_ERROR),
        );
        self::assertSame(7, $recipient->userId);
    }

    public function testUnsubscribePostsToken(): void
    {
        $this->client('{"unsubscribed":true}')->unsubscribe('signed-token');

        self::assertSame('POST', $this->captured['method']);
        self::assertSame('http://notification.test/api/sdk/transports/unsubscribe', $this->captured['url']);
        self::assertSame(
            ['token' => 'signed-token'],
            json_decode((string) $this->captured['body'], true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testEmptyTokenIsRejectedBeforeRequest(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client(self::RECIPIENT)->unsubscribe('');
    }

    private function client(string $body, int $status = 200): NotificationClient
    {
        $this->captured = [];
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use ($body, $status): MockResponse {
            $this->captured = [
                'method' => $method,
                'url' => $url,
                'body' => (string) ($options['body'] ?? ''),
            ];

            return new MockResponse($body, ['http_code' => $status, 'response_headers' => ['content-type: application/json']]);
        });

        return new NotificationClient($httpClient, 'http://notification.test', 'jwt-token');
    }
}
