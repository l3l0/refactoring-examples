<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MedicalExaminationOrderRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(type: 'integer')]
    private ?int $patientIdentificationNumber = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $version = null;

    #[ORM\Column(type: 'string', length: 32)]
    private ?string $token = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $agreementNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderingId(): ?string
    {
        return $this->ordering_id;
    }

    public function setOrderingId(string $ordering_id): self
    {
        $this->ordering_id = $ordering_id;

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

    public function getPatientIdentificationNumber(): ?int
    {
        return $this->patientIdentificationNumber;
    }

    public function setPatientIdentificationNumber(int $patientIdentificationNumber): self
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
}
