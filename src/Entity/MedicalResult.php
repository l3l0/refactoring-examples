<?php

namespace App\Entity;

use App\MedicalTreatment\Domain\AgreementNumber;
use App\MedicalTreatment\Domain\TreatmentDecision;
use App\Repository\MedicalResultRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicalResultRepository::class)]
class MedicalResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $agreementNumber = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $resultDocumentId = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $requiredDecisionDate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $treatmentDecisionType = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $clientIpAddress = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $treatmentDecision = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $decisionDate = null;

    #[ORM\Column(type: 'string', length: 32)]
    protected string $token;

    public function __construct()
    {
        $this->token = md5(uniqid((string) mt_rand(), true));
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getResultDocumentId(): ?string
    {
        return $this->resultDocumentId;
    }

    public function setResultDocumentId(string $resultDocumentId): self
    {
        $this->resultDocumentId = $resultDocumentId;

        return $this;
    }

    public function getRequiredDecisionDate(): ?DateTimeInterface
    {
        return $this->requiredDecisionDate;
    }

    public function setRequiredDecisionDate(?DateTimeInterface $requiredDecisionDate): self
    {
        $this->requiredDecisionDate = $requiredDecisionDate;

        return $this;
    }

    public function getTreatmentDecision(): ?string
    {
        return $this->treatmentDecision;
    }

    public function setTreatmentDecision(?string $treatmentDecision): self
    {
        $this->treatmentDecision = $treatmentDecision;

        return $this;
    }

    public function getTreatmentDecisionType(): ?string
    {
        return $this->treatmentDecisionType;
    }

    public function setTreatmentDecisionType(?string $treatmentDecisionType): self
    {
        $this->treatmentDecisionType = $treatmentDecisionType;

        return $this;
    }

    public function getClientIpAddress(): ?string
    {
        return $this->clientIpAddress;
    }

    public function setClientIpAddress(?string $clientIpAddress): self
    {
        $this->clientIpAddress = $clientIpAddress;

        return $this;
    }

    public function getDecisionDate(): ?DateTimeImmutable
    {
        return $this->decisionDate;
    }

    public function setDecisionDate(?DateTimeImmutable $decisionDate): self
    {
        $this->decisionDate = $decisionDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function getAgreementNumber(): ?AgreementNumber
    {
        return $this->agreementNumber ? new AgreementNumber($this->agreementNumber) : null;
    }

    /**
     * @param string|null $agreementNumber
     */
    public function setAgreementNumber(?string $agreementNumber): void
    {
        $this->agreementNumber = $agreementNumber;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function decideAboutTreatment(TreatmentDecision $treatmentDecision, DateTimeImmutable $decisionDate): void
    {
        $this->treatmentDecision = $treatmentDecision->value;
        $this->decisionDate = $decisionDate;
    }

    public function isAlreadyDecidedAboutTreatment(): bool
    {
        return $this->decisionDate !== null;
    }
}
