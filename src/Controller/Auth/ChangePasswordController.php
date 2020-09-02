<?php

namespace App\Controller\Auth;

use App\Form\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordController extends AbstractController
{

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("profile/change/password", name="change_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, TranslatorInterface $translator)
    {
        if ($user = $this->getUser()) {
            $form = $this->createForm(ChangePasswordFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted()){
                $oldPassword = $form['oldPassword']->getData();
                if (!$this->checkUserPassword($oldPassword, $user)) {
                    $form->get('oldPassword')->addError(new FormError($translator->trans('current_password')));
                }
                if($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $formData = $form->getData();

                    $encodedPassword = $encoder->encodePassword($user, $formData['password']);

                    $user->setPassword($encodedPassword);

                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('password_changed_successfully', $translator->trans('password_changed_successfully'));
                    return $this->redirectToRoute('homepage');
                }
            }

            return $this->render('frontend/security/change_password/index.html.twig', [
                'form'=> $form->createView()
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    public function checkUserPassword($oldPassword, $user) {
        return $this->passwordEncoder->isPasswordValid($user, $oldPassword);
    }
}
