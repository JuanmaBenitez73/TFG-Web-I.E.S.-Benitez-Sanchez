<?php

namespace App\Entity;

use App\Repository\OrganizationChartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationChartRepository::class)]
class OrganizationChart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $affiliates = null;

    #[ORM\Column(length: 255)]
    private ?string $departments = null;

    #[ORM\Column(length: 255)]
    private ?string $staff = null;

    #[ORM\Column(length: 255)]
    private ?string $contact_information = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAffiliates(): ?string
    {
        return $this->affiliates;
    }

    public function setAffiliates(?string $affiliates): static
    {
        $this->affiliates = $affiliates;

        return $this;
    }

    public function getDepartments(): ?string
    {
        return $this->departments;
    }

    public function setDepartments(string $departments): static
    {
        $this->departments = $departments;

        return $this;
    }

    public function getStaff(): ?string
    {
        return $this->staff;
    }

    public function setStaff(string $staff): static
    {
        $this->staff = $staff;

        return $this;
    }

    public function getContactInformation(): ?string
    {
        return $this->contact_information;
    }

    public function setContactInformation(string $contact_information): static
    {
        $this->contact_information = $contact_information;

        return $this;
    }
}
