<?php

namespace App\Controller;

use App\Entity\AttributeOptions;
use App\Entity\Category;
use App\Entity\Product;
use Proxies\__CG__\App\Entity\Store;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class FrontendController extends AbstractController
{

    /**
     * @Route("/", name="homepage", host="dev.eshopv2.ro")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $searchCategory = $request->query->get('category');
        $searchAttribute = $request->query->get('attribute');

        if ($searchAttribute && $searchCategory) {
            // sanitize
            $searchAttribute = filter_var_array($request->query->get('attribute'));
            $searchCategory = filter_var($request->query->get('category'), FILTER_SANITIZE_STRING);
            // get category
            $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $searchCategory]);
            $products = $this->getDoctrine()->getRepository(Product::class)->filter($category->getId(), $searchAttribute);
        } else {
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $attributes = $this->getDoctrine()->getRepository(AttributeOptions::class)->findAll();
        $attr = [];
        foreach ($attributes as $key => $attribute) {
            $attr[$attribute->getAttribute()->getType()][] = ['id' => $attribute->getId(), 'value' => $attribute->getValue()];
        }
        return $this->render('frontend/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'attributes' => $attr
        ]);
    }

    /**
     * @Route("/lang/{_locale}", name="locale")
     */
    public function setLocale()
    {
        return $this->redirectToRoute('homepage');
    }

    function myUrlEncode($string)
    {
        $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
        return str_replace($entities, $replacements, urlencode($string));
    }
}
