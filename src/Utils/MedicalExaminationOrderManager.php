<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\MedicalExaminationOrder;
use Doctrine\ORM\EntityManagerInterface;

class MedicalExaminationOrderManager
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function getOneByNumber(string $agreementNumber): ?MedicalExaminationOrder
    {
        $order = $this->em->getRepository(MedicalExaminationOrder::class)->findOneBy([
            'agreementNumber' => $agreementNumber,
        ]);

        if ($order !== null) {
            return $order;
        }

        return null;
    }

    public function addOrder(MedicalExaminationOrder $order): MedicalExaminationOrder
    {
        $this->em->persist($order);

        return $this->updateMedicalExaminationOrder($order);
    }

    public function updateMedicalExaminationOrder(MedicalExaminationOrder $order): MedicalExaminationOrder
    {
        $this->em->flush();

        return $order;
    }
}
