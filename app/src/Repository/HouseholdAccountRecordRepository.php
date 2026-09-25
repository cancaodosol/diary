<?php

namespace App\Repository;

use App\Entity\HouseholdAccountRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HouseholdAccountRecord>
 *
 * @method HouseholdAccountRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method HouseholdAccountRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method HouseholdAccountRecord[]    findAll()
 * @method HouseholdAccountRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HouseholdAccountRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HouseholdAccountRecord::class);
    }

    public function add(HouseholdAccountRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HouseholdAccountRecord $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 家計簿一覧表示用に、日付の新しい順（同日内はID降順）でレコードを取得する。
     * 一覧表示に使う仕訳分類・一元記録は fetch join で N+1 を避ける。
     *
     * @return HouseholdAccountRecord[]
     */
    public function findForList(): array
    {
        return $this->createQueryBuilder('r')
            ->addSelect('journalCategory')
            ->addSelect('unitaryNote')
            ->join('r.journalCategory', 'journalCategory')
            ->join('r.unitaryNote', 'unitaryNote')
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
