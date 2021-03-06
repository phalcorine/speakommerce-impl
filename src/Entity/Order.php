<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\OrderStatusType;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 32, unique: true, nullable: false)]
    private ?string $token;

    #[ORM\Column(type: 'integer')]
    private int $totalPrice = 0;

    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    private string $status = OrderStatusType::PENDING;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $customerName = '';

    // Relations

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class)]
    private $orderItems;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    private $user;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: OrderBillingDetail::class)]
    private OrderBillingDetail $billingDetail;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getBillingDetail(): ?OrderBillingDetail
    {
        return $this->billingDetail;
    }

    public function setBillingDetail(?OrderBillingDetail $billingDetail): self
    {
        // unset the owning side of the relation if necessary
        if ($billingDetail === null && $this->billingDetail !== null) {
            $this->billingDetail->setOrder(null);
        }

        // set the owning side of the relation if necessary
        if ($billingDetail !== null && $billingDetail->getOrder() !== $this) {
            $billingDetail->setOrder($this);
        }

        $this->billingDetail = $billingDetail;

        return $this;
    }
}
