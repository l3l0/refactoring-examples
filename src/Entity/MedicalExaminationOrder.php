<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MedicalExaminationOrderRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MedicalExaminationOrderRepository::class)]
class MedicalExaminationOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'guid')]
    private ?string $orderingId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'string', length: 11)]
    private ?string $patientIdentificationNumber = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $version = null;

    #[ORM\Column(type: 'string', length: 32)]
    private ?string $token = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $agreementNumber = null;

    #[ORM\Column(type: 'uuid', nullable: false)]
    private ?Uuid $accountId = null;

    public function __construct()
    {
        $this->token = md5(uniqid((string) mt_rand(), true));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderingId(): ?string
    {
        return $this->orderingId;
    }

    public function setOrderingId(string $orderingId): self
    {
        $this->orderingId = $orderingId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPatientIdentificationNumber(): ?string
    {
        return $this->patientIdentificationNumber;
    }

    public function setPatientIdentificationNumber(string $patientIdentificationNumber): self
    {
        $this->patientIdentificationNumber = $patientIdentificationNumber;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getAgreementNumber(): ?string
    {
        return $this->agreementNumber;
    }

    public function setAgreementNumber(?string $agreementNumber): self
    {
        $this->agreementNumber = $agreementNumber;

        return $this;
    }

    public function getAccountId(): ?Uuid
    {
        return $this->accountId;
    }

    public function setAccountId(?Uuid $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }
}
