<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\ForgotPasswordFormType;
use App\Form\ResetPasswordType;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgotPasswordController extends AbstractController
{

    /**
     * Restrict access for route forgot password
     * INTERVAL - Hours
     */
    public const INTERVAL = 2;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/forgot/password", name="forgot_password")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $formData = $form->getData();
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $formData['email']]);

            if (is_null($user)) {
                return $this->redirectToRoute('app_login');
            }

            // restrict sending emails
            if (!$this->restrictVerification($user)) {
                $when = $this->whenCanRequestAnotherResetPasswordLink($user);
                $form->get('email')->addError(new FormError($translator->trans('retry_send_email', ['%time%' => $when])));
            }

            if ($form->isValid()) {
                // generate token
                $token = uniqid();
                // generate link
                $link = $this->generateUrl('verify_token', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                //save token in database
                $user->setPasswordResetHash($token)
                    ->setPasswordResetAt(new \DateTime('now'));

                $this->em->persist($user);
                $this->em->flush();

                // send email
                $message = (new \Swift_Message())
                    ->setSubject($translator->trans('password_reset'))
                    ->setFrom($this->getParameter('mail_from'))
                    ->setTo($formData['email'])
                    ->setBody(
                        $this->renderView(
                            'frontend/emails/auth/reset_password.html.twig',
                            ['link' => $link]
                        ),
                        'text/html'
                    );

                try {
                    $mailer->send($message);
                } catch (\Exception $e) {
                    //
                }
                $this->addFlash('email_sent','An email was sent to your email address. Please check!');
                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('frontend/security/forgot_password/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/verify-token/{token}", name="verify_token")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function verifyToken(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $token = $request->get('token');
        $user = $this->em->getRepository(User::class)->findOneBy(['passwordResetHash' => $token]);

        if (is_null($user)) {
            return $this->redirectToRoute('forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $formData = $form->getData();
            $encodedPassword = $encoder->encodePassword($user, $formData['password']);
            $user->setPassword($encodedPassword);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('frontend/security/reset_password/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Function that checks that a user can make one forgot password attempt every INTERVAL hours
     * @param $user
     * @return bool
     */
    public function restrictVerification($user): bool
    {
        $resetPasswordAt = $this->parseResetPasswordAt($user);
        if ($resetPasswordAt) {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $now = Carbon::createFromFormat('Y-m-d H:i:s', $now)->setTimezone('Europe/Bucharest');
            if ($resetPasswordAt->diffInHours($now) < self::INTERVAL) {
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * Function that returns when the user can request another reset link
     * @param $user
     * @return string
     */
    public function whenCanRequestAnotherResetPasswordLink($user)
    {
        $resetPasswordAt = $this->parseResetPasswordAt($user);

        return $resetPasswordAt->addHours(self::INTERVAL)->format('Y-m-d H:i:s');
    }

    /**
     * Parse reset_password_at column, Carbon format
     * @param $user
     * @return Carbon
     */
    protected function parseResetPasswordAt($user)
    {
        if ($user->getPasswordResetAt()) {
            return Carbon::parse($user->getPasswordResetAt());
        }

        return null;
    }
}
