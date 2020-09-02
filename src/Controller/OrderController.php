<?php

namespace App\Controller;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Form\ClientDetailsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/order", name="order.")
 */
class OrderController extends AbstractController
{

    /**
     * @Route("/store", name="store")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request, \Swift_Mailer $mailer)
    {

        $session = $request->getSession();

        // check cart session
        if (!$session->has('cart'))
            return $this->redirectToRoute('home');

        $cartItems = $session->get('cart');

        $countProducts = array_count_values($cartItems);

        $form = $this->createForm(ClientDetailsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $products = $em->getRepository(Product::class)->findWhereIn(array_unique($cartItems));
            $order = $form->getData();

            $total = 0;
            foreach ($products as $product) {
                // get qty and calculate total price
                $qty = $countProducts[$product->getHash()];
                $total += $product->getPrice() * $qty;
            }


            $order->setTotal($total);
            $order->setUser($this->getUser());
            $em->persist($order);
            $em->flush();

            foreach ($products as $product) {
                $qty = $countProducts[$product->getHash()];

                $orderProduct = new OrderProduct();
                $orderProduct->setProduct($product);
                $orderProduct->setUser($this->getUser());
                $orderProduct->setOrder($order);
                $orderProduct->setQty($qty);
                $orderProduct->setUnitPrice($product->getPrice());
                $totalProduct = $qty * $product->getPrice();
                $orderProduct->setTotal($totalProduct);

                $em->persist($orderProduct);
                $em->flush();
            }

            // remove all cart items
            $session->remove('cart');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('frontend/cart/client_details.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
