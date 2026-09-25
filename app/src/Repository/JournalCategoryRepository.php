<?php

namespace App\Repository;

use App\Entity\JournalCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JournalCategory>
 *
 * @method JournalCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method JournalCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method JournalCategory[]    findAll()
 * @method JournalCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalCategory::class);
    }

    public function add(JournalCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JournalCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
