<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Context\ExecutionContext;

class DashboardChangePasswordController extends AbstractController {

    /**
     * @Route("/dashboard/change-password", name="dashboard_change_password")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response {
        $errors = [];
        if ($request->isMethod('POST')) {
            $user = $this->getUser();
            $params = $request->request->all();
            $constraint = new Assert\Collection([
                'current_password' => [
                    new Assert\NotBlank(['message' => 'Please enter your current password']),
                    new Assert\Callback(['callback' => function ($password, ExecutionContext $ec) use ($user, $encoder) {
                            if (null === $user->getPassword() || !$encoder->isPasswordValid($user, $password)) {
                                $ec->addViolation('Please enter your correct current password');
                            }
                        }])
                ],
                'new_password' => [
                    new Assert\Length(['min' => 3, 'minMessage' => 'Your password is too short. It should have {{ limit }} characters or more']),
                    new Assert\NotBlank(['message' => 'Please enter your new password'])
                ],
                'repeat_new_password' => [
                    new Assert\NotBlank(['message' => 'Please repeat your new password']),
                    new Assert\Callback(['callback' => function ($value, ExecutionContext $ec) {
                            if ($ec->getRoot()['new_password'] !== $value) {
                                $ec->addViolation('Passwords do not match');
                            }
                        }])
                ]
            ]);

            $validator = Validation::createValidator();
            $violations = $validator->validate($params, $constraint);

            if (0 !== count($violations)) {
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
            } else {
                $encodedPassword = $encoder->encodePassword($user, $params['new_password']);
                $user->setPassword($encodedPassword);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', 'Password successfully changed!');
                return $this->redirectToRoute('dashboard_index');
            }
        }

        return $this->render('dashboard/changepassword/change_password.html.twig', [
                    'errors' => $errors
        ]);
    }

}
