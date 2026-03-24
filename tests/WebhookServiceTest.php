<?php

declare(strict_types=1);

namespace Billing\Tests;

use Billing\Domain\Repository\WebhookDeliveryRepository;
use Billing\Domain\Repository\WebhookEndpointRepository;
use Billing\Infrastructure\Webhook\WebhookService;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class WebhookServiceTest extends TestCase
{
    private WebhookService $service;

    protected function setUp(): void
    {
        $endpointRepo = $this->createMock(WebhookEndpointRepository::class);
        $deliveryRepo = $this->createMock(WebhookDeliveryRepository::class);
        $httpClient = $this->createMock(Client::class);

        $this->service = new WebhookService($endpointRepo, $deliveryRepo, $httpClient);
    }

    public function testValidateUrl(): void
    {
        $this->assertTrue($this->service->validateUrl('https://example.com/webhook'));
        $this->assertFalse($this->service->validateUrl('http://example.com/webhook'));
        $this->assertFalse($this->service->validateUrl('https://localhost/webhook'));
        $this->assertFalse($this->service->validateUrl('https://127.0.0.1/webhook'));
    }
}