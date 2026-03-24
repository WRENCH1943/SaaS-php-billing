<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Service;

class RetryService
{
    public function retry(callable $callable, int $maxAttempts = 3, array $delays = [1, 5, 15]): mixed
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxAttempts) {
            try {
                return $callable();
            } catch (\Throwable $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt < $maxAttempts) {
                    $delay = $delays[$attempt - 1] ?? 1;
                    \Fiber::suspend();
                    sleep($delay);
                }
            }
        }

        throw $lastException;
    }
}