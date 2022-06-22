<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\MedicalExaminationOrder;
use App\Entity\MedicalResult;
use App\Form\FormUtils;
use App\Form\MedicalExaminationOrderType;
use App\Form\MedicalResultFormType;
use App\MedicalTreatment\Domain\Exception\TreatmentDecisionAlreadyDone;
use App\MedicalTreatment\Query\MedicalTreatmentDecisionQuery;
use App\MedicalTreatment\Query\MediclaTreatmentDecisionQuery\AgreementNumberNotFound;
use App\MedicalTreatment\UseCase\DecideAboutTreatmentCommand;
use App\Utils\MedicalExaminationOrderManager;
use App\Utils\MedicalResultManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ApiController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger)
    {}

    #[Route(path: '/api/medical-examination-order', methods: ['POST'])]
    public function createMedicalExaminationOrderAction(
        Request $request,
        MedicalExaminationOrderManager $orderManager
    ): JsonResponse {
        $order = new MedicalExaminationOrder();
        $form = $this->createForm(MedicalExaminationOrderType::class, $order);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var MedicalExaminationOrder $order
             */
            $order = $form->getData();
            $order->setAccountId(Uuid::fromString($this->getUser()->getUserIdentifier()));
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setUpdatedAt(new \DateTimeImmutable());
            $order->setOrderingId($this->getUser()->getUserIdentifier());
            $order = $orderManager->addOrder($order);

            return $this->json([
                'message' => 'examination order created',
                'medicalExaminationOrder' => $order,
            ]);
        }

        return $this->json(['errors' => FormUtils::getErrors($form)], JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route(path: '/api/medical-result', methods: ['POST'])]
    public function createMedicalResultAction(
        Request $request,
        MedicalResultManager $medicalResultManager
    ): JsonResponse {
        $medicalResult = new MedicalResult();
        $form = $this->createForm(MedicalResultFormType::class, $medicalResult);
        $form->submit($request->request->all());

        $this->logger->info('Create medical result decision request', [
            'ip' => $request->getClientIp(),
            'request' => $request->request->all(),
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $medicalResult = $medicalResultManager->addMedicalResult($medicalResult);
            $response = $this->json($medicalResult);

            $this->logger->info('Create medical result decision response', [
                'ip' => $request->getClientIp(),
                'request' => $request->request->all(),
                'response' => $response->getContent(),
            ]);

            return $response;
        }

        return $this->json(['errors' => FormUtils::getErrors($form)], JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route(path: '/api/medical-result/{token}/decision', methods: ['POST'])]
    public function resultsDecisionAction(
        string                        $token,
        MedicalTreatmentDecisionQuery $decisionQuery,
        MessageBusInterface $bus
    ): JsonResponse {
        $user = $this->getUser();
        $userId = $user->getUserIdentifier();

        if (!$decisionQuery->medicalExaminationOrderExists(
            $token,
            Uuid::fromString($userId)
        )) {
            return $this->json([
                'error' => 'examination order not found for token and account',
                'token' => $token,
                'account_id' => $userId
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $bus->dispatch(new DecideAboutTreatmentCommand(
                $token,
                $userId
            ));
            return $this->json([
                'message' => 'decision updated'
            ]);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('Error during handling message for decision result', ['exception' => $e]);
            if ($e->getNestedExceptionOfClass(TreatmentDecisionAlreadyDone::class)) {
                return $this->json([
                    'errors' => 'decision already made'
                ], Response::HTTP_CONFLICT);
            }
            return $this->internalServerErrorResponse();
        } catch (\Exception $e) {
            $this->logger->critical('Error during update result', ['exception' => $e]);
            return $this->internalServerErrorResponse();
        }
    }

    private function internalServerErrorResponse(): JsonResponse
    {
        return $this->json([
            'errors' => ['Internal server error']
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
