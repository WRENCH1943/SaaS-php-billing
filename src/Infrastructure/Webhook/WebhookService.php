<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Webhook;

use Billing\Domain\Entity\WebhookDelivery;
use Billing\Domain\Entity\WebhookEndpoint;
use Billing\Domain\Enum\WebhookEvent;
use Billing\Domain\Repository\WebhookDeliveryRepository;
use Billing\Domain\Repository\WebhookEndpointRepository;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

class WebhookService
{
    public function __construct(
        private WebhookEndpointRepository $endpointRepo,
        private WebhookDeliveryRepository $deliveryRepo,
        private Client $httpClient
    ) {}

    public function dispatch(WebhookEvent $event, array $payload, Uuid $tenantId): void
    {
        $endpoints = $this->endpointRepo->findByTenant($tenantId);

        foreach ($endpoints as $endpoint) {
            $this->sendToEndpoint($endpoint, $event, $payload);
        }
    }

    private function sendToEndpoint(WebhookEndpoint $endpoint, WebhookEvent $event, array $payload): void
    {
        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha256', $payloadJson, $endpoint->secret);

        $delivery = new WebhookDelivery(
            Uuid::uuid4(),
            $endpoint->id,
            $event,
            $payloadJson,
            'pending',
            0,
            null
        );

        $this->deliveryRepo->save($delivery);

        // Send asynchronously or queue, but for now sync with retry
        $this->attemptDelivery($delivery, $endpoint->url, $signature);
    }

    private function attemptDelivery(WebhookDelivery $delivery, string $url, string $signature): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            try {
                $response = $this->httpClient->post($url, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Webhook-Signature' => $signature,
                    ],
                    'body' => $delivery->payload,
                    'timeout' => 10,
                ]);

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    $updatedDelivery = new WebhookDelivery(
                        $delivery->id,
                        $delivery->endpointId,
                        $delivery->event,
                        $delivery->payload,
                        'delivered',
                        $attempt,
                        new \DateTimeImmutable()
                    );
                    $this->deliveryRepo->save($updatedDelivery);
                    return;
                }
            } catch (\Exception $e) {
                // Log error
            }

            if ($attempt < 5) {
                sleep(pow(2, $attempt)); // Exponential backoff
            }
        }

        $failedDelivery = new WebhookDelivery(
            $delivery->id,
            $delivery->endpointId,
            $delivery->event,
            $delivery->payload,
            'failed',
            5,
            null
        );
        $this->deliveryRepo->save($failedDelivery);
    }

    public function validateUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parsed = parse_url($url);
        if ($parsed['scheme'] !== 'https') {
            return false;
        }

        $host = $parsed['host'];
        if (in_array($host, ['localhost', '127.0.0.1', '::1'])) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            // It's an IP, check if private or reserved
            if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }
}