<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\MedicalExaminationOrder;
use App\Entity\MedicalResult;
use App\Form\FormUtils;
use App\Form\MedicalExaminationOrderType;
use App\Form\MedicalResultFormType;
use App\Utils\MedicalExaminationOrderManager;
use App\Utils\MedicalResultManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        Request                        $request,
        string                         $token,
        MedicalExaminationOrderManager $orderManager,
        MedicalResultManager           $medicalResultManager
    ): JsonResponse {
        if (!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        $medicalResult = $medicalResultManager->getOneMedicalResultByToken($token);

        if ($medicalResult === null) {
            $this->createNotFoundException(sprintf('Medical result not found for %s token', $token));
        }

        $order = $orderManager->getOneByNumber($medicalResult->getAgreementNumber());

        if ($order === null) {
            return $this->json([
                'error' => 'examination order not found for medical result',
                'agreement_number' => $medicalResult->getAgreementNumber()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($order->getAccountId()?->toRfc4122() !== $user->getUserIdentifier()) {
            throw $this->createAccessDeniedException();
        }

        if ($medicalResult->getTreatmentDecision() !== null) {
            return $this->json(['error' => 'decision already made'], Response::HTTP_CONFLICT);
        }

        $form = $this->createFormBuilder()
            ->add('save', SubmitType::class)
            ->getForm();
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $medicalResult->setTreatmentDecision('yes');
            $medicalResult->setDecisionDate(new \DateTimeImmutable());

            $medicalResult = $medicalResultManager->updateMedicalResult($medicalResult);

            return $this->json([
                'message' => 'decision updated',
                'medicalExaminationOrder' => $order,
                'medicalResult' => $medicalResult
            ]);
        }

        return $this->json([
            'errors' => FormUtils::getErrors($form),
            'medicalExaminationOrder' => $order,
            'medicalResult' => $medicalResult
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
