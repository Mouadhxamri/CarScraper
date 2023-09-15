<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true,length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $mainImage = null;

    #[ORM\Column(nullable: false)]
    private ?float $price = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $fuelType = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $mileage = null;

    #[ORM\Column(nullable: true,length: 255)]
    private ?string $registrationDate = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $power = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $bodyType = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $exteriorColor = null;

    #[ORM\Column(nullable: true,length: 255)]
    private ?string $emission = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $transmission = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $equipment = null;

    #[ORM\Column(nullable: false,length: 255)]
    private ?string $externalId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(string $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function setFuelType(string $fuelType): self
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    public function getMileage(): ?string
    {
        return $this->mileage;
    }

    public function setMileage(string $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getRegistrationDate(): ?string
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(string $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(string $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getBodyType(): ?string
    {
        return $this->bodyType;
    }

    public function setBodyType(string $bodyType): self
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    public function getExteriorColor(): ?string
    {
        return $this->exteriorColor;
    }

    public function setExteriorColor(string $exteriorColor): self
    {
        $this->exteriorColor = $exteriorColor;

        return $this;
    }

    public function getEmission(): ?string
    {
        return $this->emission;
    }

    public function setEmission(string $emission): self
    {
        $this->emission = $emission;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): self
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getEquipment(): ?string
    {
        return $this->equipment;
    }

    public function setEquipment(string $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;
        return $this;
    }
}
