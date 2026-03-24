<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Doctrine\Repository;

use Billing\Domain\Entity\Tenant;
use Billing\Domain\Repository\TenantRepository;
use Billing\Infrastructure\Doctrine\Entity\TenantEntity;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DoctrineTenantRepository implements TenantRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function find(UuidInterface $id): ?Tenant
    {
        $entity = $this->em->find(TenantEntity::class, $id->toString());
        if (!$entity) {
            return null;
        }

        return new Tenant(
            Uuid::fromString($entity->getId()),
            $entity->getName(),
            $entity->getCreatedAt()
        );
    }

    public function save(Tenant $tenant): void
    {
        $entity = new TenantEntity($tenant->name);
        // Since readonly, but for save, assume new or update

        $this->em->persist($entity);
        $this->em->flush();
    }
}