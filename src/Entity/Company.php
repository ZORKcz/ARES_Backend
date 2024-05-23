<?php

namespace App\Entity;

use App\GiftServices\Entity\Traits\CreatedAt;
use App\GiftServices\Entity\Traits\DeletedAt;
use App\GiftServices\Entity\Traits\UpdatedAt;
use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    use DeletedAt;
    use UpdatedAt;
    use CreatedAt;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $ico = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $addressCity = null;

    #[ORM\Column(length: 50)]
    private ?string $addressStreet = null;

    #[ORM\Column(length: 10)]
    private ?string $addressHousenumber = null;

    #[ORM\Column(length: 9)]
    private ?string $addressPostalCode = null;

    #[ORM\Column(length: 50)]
    private ?string $addressCounty = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIco(): ?string
    {
        return $this->ico;
    }

    public function setIco(string $ico): static
    {
        $this->ico = $ico;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function setAddressCity(string $addressCity): static
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    public function getAddressStreet(): ?string
    {
        return $this->addressStreet;
    }

    public function setAddressStreet(string $addressStreet): static
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    public function getAddressHousenumber(): ?string
    {
        return $this->addressHousenumber;
    }

    public function setAddressHousenumber(string $addressHousenumber): static
    {
        $this->addressHousenumber = $addressHousenumber;

        return $this;
    }

    public function getAddressPostalCode(): ?string
    {
        return $this->addressPostalCode;
    }

    public function setAddressPostalCode(string $addressPostalCode): static
    {
        $this->addressPostalCode = $addressPostalCode;

        return $this;
    }

    public function getAddressCounty(): ?string
    {
        return $this->addressCounty;
    }

    public function setAddressCounty(string $addressCounty): static
    {
        $this->addressCounty = $addressCounty;

        return $this;
    }
}
