<?php


namespace App\Helper;


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

    public function setCart()
    {
        $this->cart = $this->session->get('cart');
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function addToSession($product)
    {
        $session = $this->session;

        if ($session->has('cart')) {
            $products = (array)$session->get('cart');
            array_push($products, $product->getId());
            $session->set('cart', $products);
        } else {
            $products = array($product->getId());
            $session->set('cart', $products);
        }
    }


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


    public function getCartTotal($cartProducts)
    {
        $myCollectionIds = array_map(function ($obj) {
            return ['id' => $obj->getProduct()->getId(), 'qty' => $obj->getQty()];
        }, $cartProducts);
        $products = $this->entityManager->getRepository(Product::class)->findWhereInArray(array_column($myCollectionIds,'id'));
        $total = 0;
        foreach ($products as $k => $product) {
            foreach ($myCollectionIds as $collection) {
                if ($collection['id'] === $product['id']) {
                    $total += $collection['qty'] * $product['price'];
                }
            }
        }

        return $total;
    }
}