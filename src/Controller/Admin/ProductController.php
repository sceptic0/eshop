<?php

namespace App\Controller\Admin;

use App\Entity\AttributeOptions;
use App\Entity\AttributeProduct;
use App\Entity\Product;
use App\Form\Admin\CreateProductType;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("dashboard/", name="admin_product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("product", name="index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {

        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('dashboard/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("product/store", name="store")
     * @param Request $request
     * @return
     */
    public function store(Request $request)
    {
        $this->denyAccessUnlessGranted(["ROLE_ADMIN", "ROLE_EDITOR"]);

        $form = $this->createForm(CreateProductType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $product = $form->getData();

            $image = $form->get('image')->getData();
            if ($image) {

                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        sprintf('%s/%s', $this->getParameter('kernel.project_dir'), '/images'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $product->setImage($newFilename);
            }

            $product->setActive(1);

            // slugify category title
            $slugify = new Slugify();
            $slug = $slugify->slugify($product->getTitle());
            $product->setSlug($slug);

            // save product
            $em->persist($product);
            $em->flush();

            //product attribute & attribute option
            $attributeValue = $request->request->get('attribute_value');
            $attributeOption = $em->getRepository(AttributeOptions::class)->findOneBy(['id' => $attributeValue]);
            $productAttribute = new AttributeProduct();
            $productAttribute->setProduct($product);
            $productAttribute->setAttributeOption($attributeOption);
            $productAttribute->setPrice($product->getPrice());

            //save product attribute
            $em->persist($productAttribute);
            $em->flush();

            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('dashboard/product/store.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("attributes-values", name="attributes_values")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAttributeValues(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $attribute = filter_var($request->query->get('attribute'), FILTER_SANITIZE_STRING);

            $attributeValues = $this->getDoctrine()->getRepository(AttributeOptions::class)->findBy(['attribute' => $attribute]);
            $data = [];
            foreach ($attributeValues as $key => $value) {
                $data[$key]['id'] = $value->getId();
                $data[$key]['value'] = $value->getValue();
            }

            return $this->json($data, 200);
        }

        return $this->json([], 403);
    }
}
