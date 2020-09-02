<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", name="product_")
 */
class ProductController extends AbstractController
{

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/{hash}/show", name="show")
     * @param $hash
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function show($hash)
    {
        $product = $this->productRepository->findOneBy(['hash' => $hash]);

        if (is_null($product)) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('frontend/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
