<?php

namespace App\Repository;

use App\Entity\UnitaryNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UnitaryNote>
 *
 * @method UnitaryNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnitaryNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnitaryNote[]    findAll()
 * @method UnitaryNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitaryNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnitaryNote::class);
    }

    public function add(UnitaryNote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UnitaryNote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findRecently(\DateTimeInterface $date)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\UnitaryNote u
            WHERE u.date >= :date
            ORDER BY u.date DESC, u.title ASC'
        )->setParameter('date', $date);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function findInTerm(\DateTimeInterface $startdate, \DateTimeInterface $enddate)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\UnitaryNote u
            WHERE u.date >= :startdate and u.date <= :enddate
            ORDER BY u.date DESC, u.title ASC'
        )->setParameter('startdate', $startdate)
         ->setParameter('enddate', $enddate);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function findByKeyword(string $keyword)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\UnitaryNote u
            WHERE u.title like :keyword or u.text like :keyword
            ORDER BY u.date DESC, u.title ASC'
        )->setParameter('keyword', '%'.$keyword.'%');

        // returns an array of Product objects
        $notes = $query->getResult();
        foreach ($notes as $note) {
            $note->setKeyword($keyword);
        }
        return $notes;
    }


    public function findNthLatest(int $n): ?UnitaryNote
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.date', 'DESC')
            ->addOrderBy('u.title', 'DESC')
            ->setFirstResult($n - 1)   // ← N番目の位置（0始まり）
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return UnitaryNote[] Returns an array of UnitaryNote objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UnitaryNote
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
