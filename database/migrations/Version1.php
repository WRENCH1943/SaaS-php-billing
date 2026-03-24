<?php

declare(strict_types=1);

namespace Billing\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version1 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tenants (
            id CHAR(36) NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE subscriptions (
            id CHAR(36) NOT NULL,
            tenant_id CHAR(36) NOT NULL,
            plan VARCHAR(255) NOT NULL,
            status VARCHAR(255) NOT NULL,
            current_period_start DATETIME NOT NULL,
            current_period_end DATETIME NOT NULL,
            trial_end DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_tenant (tenant_id),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE invoices (
            id CHAR(36) NOT NULL,
            subscription_id CHAR(36) NOT NULL,
            amount_in_paise INT NOT NULL,
            status VARCHAR(255) NOT NULL,
            due_date DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_subscription (subscription_id),
            FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE invoice_lines (
            id CHAR(36) NOT NULL,
            invoice_id CHAR(36) NOT NULL,
            description VARCHAR(255) NOT NULL,
            amount_in_paise INT NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_invoice (invoice_id),
            FOREIGN KEY (invoice_id) REFERENCES invoices(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE usage_records (
            id CHAR(36) NOT NULL,
            tenant_id CHAR(36) NOT NULL,
            user_count INT NOT NULL,
            recorded_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_tenant_recorded (tenant_id, recorded_at),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE webhook_endpoints (
            id CHAR(36) NOT NULL,
            tenant_id CHAR(36) NOT NULL,
            url VARCHAR(255) NOT NULL,
            secret VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_tenant (tenant_id),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE webhook_deliveries (
            id CHAR(36) NOT NULL,
            endpoint_id CHAR(36) NOT NULL,
            event VARCHAR(255) NOT NULL,
            payload TEXT NOT NULL,
            status VARCHAR(255) NOT NULL,
            attempts INT NOT NULL,
            delivered_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            INDEX idx_endpoint (endpoint_id),
            FOREIGN KEY (endpoint_id) REFERENCES webhook_endpoints(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE payment_attempts (
            id CHAR(36) NOT NULL,
            invoice_id CHAR(36) NOT NULL,
            gateway_response TEXT NOT NULL,
            status VARCHAR(255) NOT NULL,
            attempted_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_invoice (invoice_id),
            FOREIGN KEY (invoice_id) REFERENCES invoices(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE payment_attempts');
        $this->addSql('DROP TABLE webhook_deliveries');
        $this->addSql('DROP TABLE webhook_endpoints');
        $this->addSql('DROP TABLE usage_records');
        $this->addSql('DROP TABLE invoice_lines');
        $this->addSql('DROP TABLE invoices');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('DROP TABLE tenants');
    }
}