<?php

namespace App\Controller;

use App\Repository\StoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {


    /**
     * @var StoreRepository
     */
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @Route("/dashboard", name="dashboard_index")
     */
    public function index()
    {
        $stores = $this->storeRepository->findAll();
        return $this->render('dashboard/index.html.twig', [
                'stores' => $stores
            ]
        );
    }

}
