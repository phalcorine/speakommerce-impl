<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\OrderBillingDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderBillingDetailRepository::class)]
#[ORM\HasLifecycleCallbacks]
class OrderBillingDetail
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $fullName;

    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    private string $address;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $zipCode;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $city;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $country;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private string $cardNumber;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $cardExpiryDate;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $cardCode;

    // Relations

    #[ORM\OneToOne(inversedBy: 'billingDetail', targetEntity: Order::class)]
    #[ORM\JoinColumn]
    private Order $order;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    public function getCardExpiryDate(): ?string
    {
        return $this->cardExpiryDate;
    }

    public function setCardExpiryDate(string $cardExpiryDate): self
    {
        $this->cardExpiryDate = $cardExpiryDate;

        return $this;
    }

    public function getCardCode(): ?string
    {
        return $this->cardCode;
    }

    public function setCardCode(string $cardCode): self
    {
        $this->cardCode = $cardCode;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
