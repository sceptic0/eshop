<?php

namespace App\Controller;

use App\Entity\Product;
use App\Helper\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

            $productAdded = filter_var($request->request->get('product'), FILTER_SANITIZE_STRING);

            $product = $this->entityManager->getRepository(Product::class)->findOneBy(['hash' => $productAdded]);

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
        $hash = null;
        if (!empty($cartProducts)) {
            $hashes = array_unique($cartProducts);
            $values = array_count_values($cartProducts);
            $products = $this->entityManager->getRepository(Product::class)->findWhereInArray($hashes);

            foreach ($products as $key => $product) {
                $products[$key]['qty'] = $values[$product['hash']];
            }

            $hash = array_column($products, 'hash');
        }

        return $this->render('frontend/cart/index.html.twig', [
            'totalCartItems' => ($cartProducts !== null ) ? count($cartProducts) : 0,
            'cartProducts' => $products ?? [],
            'ids' => ($hash !== null) ? json_encode($hash) : ''
        ]);
    }


    /**
     * @Route("/remove", name="remove")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeFromCart(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $cartProducts = $this->cart->getCart();
            $removedProduct = filter_var($request->request->get('product'), FILTER_SANITIZE_STRING);
            $removedItems = 0;
            foreach ($cartProducts as $key => $cartProduct) {
                if ($cartProducts[$key] == $removedProduct) {
                    $removedItems++;
                    $this->cart->removeFromSession($cartProducts[$key]);
                    unset($cartProducts[$key]);
                }
            }
            $data['total'] = $this->cart->getCartTotal($cartProducts);
            $data['removedItems'] = $removedItems;
            return $this->json($data, 200);
        }
        return $this->json([], 403);
    }

    /**
     * @Route("/checkQty", name="check.qty")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkQty(Request $request, TranslatorInterface $translator)
    {
        if ($request->isXmlHttpRequest()) {

            $qty = filter_var($request->request->get('qty'), FILTER_SANITIZE_STRING);
            $productHash = filter_var($request->request->get('product'), FILTER_SANITIZE_STRING);

            $data['message'] = "";

            $qtyCheck = $this->cart->checkQuantity($productHash, $qty);
            if (!$qtyCheck['status']) {
                $data['message'] = $translator->trans('quantity_exceeded');
                $data['maxQty'] = $qtyCheck['maxQty'];
                $data['status'] = $qtyCheck['status'];
                return $this->json($data, 200);
            }
            $data['status'] = $qtyCheck['status'];
            return $this->json($data, 200);
        }

        return $this->json([], 403);
    }
}
