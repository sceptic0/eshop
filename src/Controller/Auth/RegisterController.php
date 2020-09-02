<?php

namespace App\Controller\Auth;

use App\Entity\Store;
use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class RegisterController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/register", name="auth_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param TranslatorInterface $translator
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request, UserPasswordEncoderInterface $encoder, TranslatorInterface $translator, \Swift_Mailer $mailer)
    {

        $form = $this->createForm(RegisterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // check for csrf token
            $submittedToken = $request->request->get('_csrf_token');

            if (!$this->isCsrfTokenValid('authenticate', $submittedToken)) {
                return $this->redirectToRoute('auth_register');
            }

            if (is_null($this->getStore($request))) {
                $form->get('submit')->addError(new FormError($translator->trans('store_not_found')));
            }

            if ($form->isValid()) {
                $user = $form->getData();
                $encodedPassword = $encoder->encodePassword($user, $user->getPassword());
                $user->setFirstName($user->getFirstName())
                    ->setLastName($user->getLastName())
                    ->setEmail($user->getEmail())
                    ->setPassword($encodedPassword)
                    ->setActive(0)
                    ->setRoles(['ROLE_CLIENT'])
                    ->setStore($this->getStore($request));

                $this->em->persist($user);
                $this->em->flush();

                $link = $this->generateUrl('account_confirmation', ['hash' => $user->getAccountHash()], UrlGeneratorInterface::ABSOLUTE_URL);
                // send email
                $mail = (new \Swift_Message())
                    ->setFrom($this->getParameter('mail_from'))
                    ->setTo($user->getEmail())
                    ->setSubject($translator->trans('enable_account'))
                    ->setBody(
                        $this->render('/frontend/emails/auth/enable_account.html.twig', [
                                'link' => $link,
                                'fullName' => $user->getFullName()
                            ]
                        ),
                        'text/html'
                    );

                try {
                    $mailer->send($mail);
                } catch (\Exception $e) {

                }
                $this->addFlash('activation_email', $translator->trans('activation_email'));
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('frontend/security/register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirmation/{hash}", name="account_confirmation")
     * @param Request $request
     * @param $hash
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirm(Request $request, $hash, TranslatorInterface $translator)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['accountHash' => $hash]);


        if (is_null($user)) {
            $this->addFlash('hash_mismatch', $translator->trans('hash_mismatch'));
        } else {
            if ($user->getActive()) {
                $this->addFlash('already_enabled', $translator->trans('already_enabled'));
            } else {
                $user->setActive(true);
                $this->em->persist($user);
                $this->em->flush();
                $this->addFlash('account_enabled', $translator->trans('account_enabled'));
            }
        }

        return $this->render('frontend/confirmation.html.twig');
    }

    /**
     * Function that returns the store id from where the client registered, null if something strange happen
     * @param $request
     * @return object
     */
    private function getStore($request): ?object
    {
        $domain = $request->getHost();
        $store = $this->em->getRepository(Store::class)->findOneBy(['domain' => $domain]);

        if (is_null($store)) {
            return null;
        }

        return $store;
    }
}
