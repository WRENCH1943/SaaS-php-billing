<?php

declare(strict_types=1);

namespace Billing\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TenantResolver implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Extract tenant from header or subdomain
        $tenantId = $request->getHeaderLine('X-Tenant-ID');

        if (!$tenantId) {
            // From subdomain, e.g. tenant1.app.com
            $host = $request->getUri()->getHost();
            $parts = explode('.', $host);
            $tenantId = $parts[0] ?? '';
        }

        // Set in context or global
        TenantContext::setTenantId($tenantId);

        return $handler->handle($request);
    }
}

class TenantContext
{
    private static ?string $tenantId = null;

    public static function setTenantId(string $tenantId): void
    {
        self::$tenantId = $tenantId;
    }

    public static function getTenantId(): ?string
    {
        return self::$tenantId;
    }
}