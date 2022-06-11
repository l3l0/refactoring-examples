<?php

namespace App\Repository;

use App\Entity\MedicalExaminationOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalExamtinationOrder>
 *
 * @method MedicalExaminationOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalExaminationOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalExaminationOrder[]    findAll()
 * @method MedicalExaminationOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicalExaminationOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalExaminationOrder::class);
    }

    public function add(MedicalExaminationOrder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedicalExaminationOrder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MedicalExamtinationOrder[] Returns an array of MedicalExamtinationOrder objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MedicalExamtinationOrder
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
