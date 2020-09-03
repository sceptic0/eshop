<?php

namespace App\Helper;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var cart
     */
    private $cart;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->setCart();
    }

    /**
     * Set cart in session
     */
    public function setCart()
    {
        $this->cart = $this->session->get('cart');
    }

    /**
     * Get cart products from session
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Function that adds product to session
     * @param $product
     */
    public function addToSession($product)
    {
        $session = $this->session;

        if ($session->has('cart')) {
            $products = (array)$session->get('cart');
            array_push($products, $product->getHash());
            $session->set('cart', $products);
        } else {
            $products = array($product->getHash());
            $session->set('cart', $products);
        }
    }

    /**
     * Function that removes product from session
     * @param $product
     */
    public function removeFromSession($product)
    {
        $session = $this->session;

        if ($session->has('cart')) {
            $products = $session->get('cart');
            $addToSession = array_filter($products, function ($cartProduct) use ($product) {
                return $product !== $cartProduct;
            });
            $session->remove('cart');
            $session->set('cart', $addToSession);
        }
    }

    /**
     * Calculate cart total
     * @param $cartProducts
     * @return float|int
     */
    public function getCartTotal($cartProducts)
    {
        $productsQty = array_count_values($cartProducts);
        $products = $this->entityManager->getRepository(Product::class)->findWhereInArray(array_values($cartProducts));
        $total = 0;
        foreach ($products as $k => $product) {
                $total +=  $productsQty[$product['hash']]* $product['price'];
        }

        return $total;
    }

    /**
     * Function that checks the product quantity
     * @param $productHash
     * @param $qty
     * @return mixed
     */
    public function checkQuantity($productHash, $qty)
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['hash' => $productHash]);

        $data['maxQty'] = $product->getQty();
        if ($product->getQty() >= $qty) {
            $data['status'] = true;
            return $data;
        }

        $data['status'] = false;
        return $data;
    }
}