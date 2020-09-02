<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\StoreCreateType;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("dashboard/store", name="store_")
 */
class StoreController extends AbstractController
{
    /**
     * @var StoreRepository
     */
    private $storeRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(StoreRepository $storeRepository, EntityManagerInterface $entityManager)
    {
        $this->storeRepository = $storeRepository;
        $this->em = $entityManager;
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $form = $this->createForm(StoreCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get form data
            $form = $form->getData();

            $store = new Store();
            $store->setDomain($form->getDomain());

            //persist and flush
            $this->em->persist($store);
            $this->em->flush();

            return $this->redirectToRoute('dashboard_index');

        }

        return $this->render('/dashboard/store/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{hash}/show", name="show")
     * @param $hash
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($hash)
    {
        $store = $this->storeRepository->findOneBy(['hash' => $hash]);

        if (is_null($store))
            return $this->redirectToRoute('dashboard_index');

        return $this->render('dashboard/store/show.html.twig', [
            'store' => $store
        ]);
    }

    /**
     * @Route("/{hash}/update", name="update")
     * @param Request $request
     * @param $hash
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $hash)
    {
        $store = $this->storeRepository->findOneBy(['hash' => $hash]);

        if (is_null($store))
            return $this->redirectToRoute('dashboard_index');

       $form = $this->createForm(StoreCreateType::class, $store);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
          //get form data
           $form = $form->getData();

           $store->setDomain($form->getDomain());

           $this->em->persist($store);
           $this->em->flush();

           return $this->redirectToRoute('dashboard_index');
       }

       return $this->render('dashboard/store/update.html.twig',[
          'form' => $form->createView()
       ]);
    }

    /**
     * @Route("/{hash}/delete")
     * @param $hash
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function destroy($hash)
    {
        $store = $this->storeRepository->findOneBy(['hash' => $hash]);

        if (is_null($store))
            return $this->redirectToRoute('dashboard_index');

        $this->em->remove($store);

        return $this->redirectToRoute('dashboard_index');
    }
}
