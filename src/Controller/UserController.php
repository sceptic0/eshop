<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("{lang}/profile", name="profile")
     */
    public function index($lang)
    {
        if (!$user = $this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('frontend/user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
