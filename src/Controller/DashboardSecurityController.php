<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DashboardSecurityController extends AbstractController {

    /**
     * @Route("/dashboard/login", name="dashboard_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('dashboard/security/login.html.twig', [
                    'last_username' => $lastUsername,
                    'error' => $error
        ]);
    }

    /**
     * @Route("/dashboard/logout", name="dashboard_logout")
     */
    public function logout() {

    }

}
