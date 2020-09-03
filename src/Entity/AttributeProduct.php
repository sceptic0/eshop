<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttributeProductRepository")
 */
class AttributeProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AttributeOptions", inversedBy="attributeProduct")
     * @ORM\JoinColumn(name="attribute_option_id", referencedColumnName="id", nullable=false)
     */
    private $attributeOption;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="attributeProduct")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
     */
    private $product;

    /**
     * @ORM\Column(type="float")
     */
    private $price;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getAttributeOption(): ?AttributeOptions
    {
        return $this->attributeOption;
    }

    public function setAttributeOption(?AttributeOptions $attributeOption): self
    {
        $this->attributeOption = $attributeOption;

        return $this;
    }


}
