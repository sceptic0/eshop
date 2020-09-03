<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttributeOptionsRepository")
 */
class AttributeOptions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Attribute", inversedBy="attributeOptions")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false)
     */
    private $attribute;

    /**
     * @ORM\OneToMany(targetEntity="AttributeProduct", mappedBy="attributeOption")
     */
    private $attributeProduct;

    /**
     * @ORM\Column(type="string")
     */
    private $value;

    public function __construct()
    {
        $this->attributeProduct = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return Collection|AttributeProduct[]
     */
    public function getAttributeProduct(): Collection
    {
        return $this->attributeProduct;
    }

    public function addAttributeProduct(AttributeProduct $attributeProduct): self
    {
        if (!$this->attributeProduct->contains($attributeProduct)) {
            $this->attributeProduct[] = $attributeProduct;
            $attributeProduct->setAttributeOption($this);
        }

        return $this;
    }

    public function removeAttributeProduct(AttributeProduct $attributeProduct): self
    {
        if ($this->attributeProduct->contains($attributeProduct)) {
            $this->attributeProduct->removeElement($attributeProduct);
            // set the owning side to null (unless already changed)
            if ($attributeProduct->getAttributeOption() === $this) {
                $attributeProduct->setAttributeOption(null);
            }
        }

        return $this;
    }
}
