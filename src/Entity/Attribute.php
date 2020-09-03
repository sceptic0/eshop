<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttributeRepository")
 */
class Attribute
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AttributeOptions", mappedBy="attribute")
     */
    private $attributeOptions;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
        $this->attributeOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    /**
     * @return Collection|AttributeOptions[]
     */
    public function getAttributeOptions(): Collection
    {
        return $this->attributeOptions;
    }

    public function addAttributeOption(AttributeOptions $attributeOption): self
    {
        if (!$this->attributeOptions->contains($attributeOption)) {
            $this->attributeOptions[] = $attributeOption;
            $attributeOption->setAttribute($this);
        }

        return $this;
    }

    public function removeAttributeOption(AttributeOptions $attributeOption): self
    {
        if ($this->attributeOptions->contains($attributeOption)) {
            $this->attributeOptions->removeElement($attributeOption);
            // set the owning side to null (unless already changed)
            if ($attributeOption->getAttribute() === $this) {
                $attributeOption->setAttribute(null);
            }
        }

        return $this;
    }
}
