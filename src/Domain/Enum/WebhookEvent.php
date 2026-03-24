<?php

declare(strict_types=1);

namespace Billing\Domain\Enum;

enum WebhookEvent: string
{
    case SUBSCRIPTION_CREATED = 'subscription.created';
    case INVOICE_PAID = 'invoice.paid';
    case INVOICE_FAILED = 'invoice.failed';
    case TRIAL_ENDING = 'trial.ending';
}