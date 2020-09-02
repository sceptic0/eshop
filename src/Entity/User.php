<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
        $this->setUserHash(uniqid());
        $this->setAccountHash(uniqid());
        $this->order = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $userHash;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $accountHash;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordResetAt;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $passwordResetHash;

    /**
     * @ORM\Column(type="boolean", options={"default": false} )
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="user")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     */
    private $store;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user")
     */
    private $order;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CLIENT';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getUserHash(): ?string
    {
        return $this->userHash;
    }

    public function setUserHash(string $userHash): self
    {
        $this->userHash = $userHash;

        return $this;
    }

    public function getPasswordResetAt(): ?\DateTimeInterface
    {
        return $this->passwordResetAt;
    }

    public function setPasswordResetAt(?\DateTimeInterface $passwordResetAt): self
    {
        $this->passwordResetAt = $passwordResetAt;

        return $this;
    }

    public function getPasswordResetHash(): ?string
    {
        return $this->passwordResetHash;
    }

    public function setPasswordResetHash(?string $passwordResetHash): self
    {
        $this->passwordResetHash = $passwordResetHash;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getFullName()
    {
        return $this->lastName . ' ' . $this->firstName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAccountHash(): ?string
    {
        return $this->accountHash;
    }

    public function setAccountHash(string $accountHash): self
    {
        $this->accountHash = $accountHash;

        return $this;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    public function getOrderProduct(): Collection
    {
        return $this->orderProduct;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProduct->contains($orderProduct)) {
            $this->orderProduct[] = $orderProduct;
            $orderProduct->setUser($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProduct->contains($orderProduct)) {
            $this->orderProduct->removeElement($orderProduct);
            // set the owning side to null (unless already changed)
            if ($orderProduct->getUser() === $this) {
                $orderProduct->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrder(): Collection
    {
        return $this->order;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->order->contains($order)) {
            $this->order[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->order->contains($order)) {
            $this->order->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

//    public function checkUserPassword($oldPassword, $user)
//    {
//        return $this->passwordEncoder->isPasswordValid($user, $oldPassword);
//    }
}
