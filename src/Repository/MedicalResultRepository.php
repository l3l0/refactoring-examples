<?php

namespace App\Repository;

use App\Entity\MedicalResult;
use App\MedicalTreatment\Domain\MedicalResult as BaseMedicalResult;
use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;
use App\MedicalTreatment\Domain\MedicalResultRepository as MedicalResultRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalResult>
 *
 * @method MedicalResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalResult[]    findAll()
 * @method MedicalResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicalResultRepository extends ServiceEntityRepository implements MedicalResultRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalResult::class);
    }

    public function add(BaseMedicalResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedicalResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOneByToken(string $token): BaseMedicalResult
    {
        $medicalResult = $this->findOneBy(['token' => $token]);

        if (!$medicalResult) {
            throw MedicalResultNotFound::forToken($token);
        }

        return $medicalResult;
    }
}
