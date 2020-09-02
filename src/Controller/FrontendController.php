<?php

namespace App\Controller;

use App\Entity\Product;
use Proxies\__CG__\App\Entity\Store;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class FrontendController extends AbstractController {

    /**
     * @Route("/", name="homepage", host="dev.eshopv2.ro")
     */
    public function index()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->render('frontend/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/lang/{_locale}", name="locale")
     */
    public function setLocale()
    {
        return $this->redirectToRoute('homepage');
    }
}
