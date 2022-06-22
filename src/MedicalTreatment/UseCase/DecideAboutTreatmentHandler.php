<?php

declare(strict_types=1);

namespace App\MedicalTreatment\UseCase;

use App\MedicalTreatment\Domain\Exception\MedicalResultNotFound;
use App\MedicalTreatment\Domain\Exception\TreatmentDecisionAlreadyDone;
use App\MedicalTreatment\Domain\MedicalResultRepository;
use App\MedicalTreatment\Domain\TreatmentDecision;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DecideAboutTreatmentHandler implements MessageHandlerInterface
{
    public function __construct(private readonly MedicalResultRepository $medicalResultRepository)
    {}

    /**
     * @param DecideAboutTreatmentCommand $command
     * @throws MedicalResultNotFound
     * @return void
     */
    public function __invoke(DecideAboutTreatmentCommand $command): void
    {
        $medicalResult = $this->medicalResultRepository->getOneByToken($command->token);
        if ($medicalResult->getTreatmentDecision() !== null) {
            throw TreatmentDecisionAlreadyDone::forMedicalResultId((int) $medicalResult->getId());
        }

        $medicalResult->decideAboutTreatment(TreatmentDecision::YES, new \DateTimeImmutable());
        $this->medicalResultRepository->add($medicalResult, true);
    }
}
