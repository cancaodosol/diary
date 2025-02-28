<?php

namespace App\Repository;

use App\Entity\Diary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use DateTime;

/**
 * @extends ServiceEntityRepository<Diary>
 *
 * @method Diary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Diary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Diary[]    findAll()
 * @method Diary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diary::class);
    }

    public function add(Diary $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Diary $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByDate(string $date)
    {
        return $this->findOneBy(['date' => DateTime::createFromFormat('Y-m-d', $date)]);
    }

    public function findInTerm(\DateTimeInterface $startdate, \DateTimeInterface $enddate)
    {
        if($startdate > $enddate) return $this->findInTerm($enddate, $startdate);

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\Diary u
            WHERE u.date >= :startdate and u.date <= :enddate
            ORDER BY u.date DESC, u.text ASC'
        )->setParameter('startdate', $startdate)
         ->setParameter('enddate', $enddate);

        // returns an array of Product objects
        return $query->getResult();
    }
}
