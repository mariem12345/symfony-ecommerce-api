<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Product
    {
        return $this->entityManager->find(Product::class, $id);
    }

    public function findByCriteria(array $criteria, int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p');

        $this->applyCriteria($queryBuilder, $criteria);

        $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Product::class, 'p');

        $this->applyCriteria($queryBuilder, $criteria);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private function applyCriteria(QueryBuilder $queryBuilder, array $criteria): void
    {
        if (isset($criteria['name'])) {
            $queryBuilder
                ->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $criteria['name'] . '%');
        }

        if (isset($criteria['category'])) {
            $queryBuilder
                ->andWhere('p.category = :category')
                ->setParameter('category', $criteria['category']);
        }
    }
}
