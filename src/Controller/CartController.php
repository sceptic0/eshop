<?php

namespace App\Controller;

use App\Entity\Product;
use App\Helper\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart", name="cart_")
 */
class CartController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Cart
     */
    private $cart;

    public function __construct(Cart $cart, EntityManagerInterface $entityManager)
    {
        $this->cart = $cart;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/add", name="add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addToCart(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            // TODO check token
            //$token = $request->request->get('token');

            $product = $request->request->get('product');

            $product = $this->entityManager->getRepository(Product::class)->findOneBy(['hash' => $product]);

            $this->cart->addToSession($product);

            $data['product_price'] = $product->getPrice();
            $data['product'] = $product->getTitle();
            $data['image'] = $product->getImage();
            $data['qty'] = 1;
            return $this->json($data, 200);
        }

        return $this->json([], 403);
    }

    /**
     * @Route("/show", name="index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productInCart(Request $request)
    {
        $cartProducts = $this->cart->getCart();
        $ids = array_unique($cartProducts);
        $values = array_count_values($cartProducts);
        $products = $this->entityManager->getRepository(Product::class)->findWhereInArray($ids);

        foreach ($products as $key => $product) {
            $products[$key]['qty'] = $values[$product['id']];
        }

        $hash = array_map(function($product){ return $product['hash'];}, $products);
        return $this->render('frontend/cart/index.html.twig', [
            'totalCartItems' => count($cartProducts),
            'cartProducts' => $products,
            'ids' => json_encode($hash)
        ]);
    }
}
