<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{

    public const ORDER_ACCEPTED = 1;

    public const ORDER_REJECTED = 2;

    public const ORDER_PENDING = 3;

    public const ORDER_STORED = 'PLACED_ORDER';

    public const ORDER_MANAGED = 'MANAGED_ORDER';

    public function __construct()
    {
       $this->setCreatedAt(new \DateTime('now'));
       $this->setHash(uniqid());
       $this->orderProduct = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="order")
     *  @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $phone;
    /**
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="order")
     */
    private $orderProduct;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    public function getOrder(): Collection
    {
        return $this->orderProduct;
    }

    public function addOrder(OrderProduct $order): self
    {
        if (!$this->orderProduct->contains($order)) {
            $this->orderProduct[] = $order;
            $order->setOrder($this);
        }

        return $this;
    }

    public function removeOrder(OrderProduct $order): self
    {
        if ($this->orderProduct->contains($order)) {
            $this->orderProduct->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getOrder() === $this) {
                $order->setOrder(null);
            }
        }

        return $this;
    }
}
