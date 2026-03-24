# SaaS PHP Billing & Subscription Engine

A multi-tenant billing module for B2B SaaS platforms.

## Setup

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database
4. Run migrations: `php vendor/bin/doctrine-migrations migrate`
5. Run tests: `vendor/bin/phpunit`

## Architecture

### Architectural Decision Records

#### Multi-Tenancy Implementation
**Decision:** Use Doctrine filters for automatic tenant scoping on database queries.  
**Rejected:** Manual WHERE tenant_id = ? clauses in every query, separate databases per tenant.  
**Rationale:** Ensures data isolation without code duplication and reduces risk of data leaks. Prevents developers from forgetting to scope queries.

#### Money Handling
**Decision:** Store monetary values as integers in paise (1/100 of rupee) to avoid floating-point precision errors.  
**Rejected:** Using floats or external money libraries like moneyphp/money.  
**Rationale:** Prevents rounding errors in financial calculations, keeps dependencies minimal.

#### Payment Gateway Abstraction
**Decision:** Implement PaymentGatewayInterface with mock implementation for testing.  
**Rejected:** Direct integration with specific gateways.  
**Rationale:** Allows easy switching between payment providers and enables comprehensive testing.

#### Retry Logic
**Decision:** Use exponential backoff for failed operations (payments, webhooks) with Fiber-based cooperative multitasking.  
**Rejected:** Simple loops with sleep.  
**Rationale:** Better resource utilization and responsiveness in concurrent environments.

#### Webhook Security
**Decision:** Use HMAC-SHA256 signatures for webhook payloads.  
**Rejected:** No signatures or weaker hashes.  
**Rationale:** Ensures webhook authenticity and prevents replay attacks.

## Known Limitations

- No UI, only API endpoints
- Mock payment gateway only (no real payments)
- No queue system for background jobs
- Basic error handling

## PHP 8.x Features Used

- **Enums** for Plan, SubscriptionStatus, InvoiceStatus, WebhookEvent
- **Readonly properties** in domain entities for immutability
- **Union types** in Money value object (int for amount)
- **Named arguments** in service method calls for clarity
- **Fibers** in RetryService for cooperative retry logic