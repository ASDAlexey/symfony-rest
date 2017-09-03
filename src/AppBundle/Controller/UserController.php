<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\BaseController;

class UserController extends BaseController {
  /**
   * @Route("/sign-up", name="user_sign_up")
   * @Method("POST")
   */
  public function signUpAction(Request $request) {
    $email = $request->request->get('email');
    $username = $request->request->get('username');
    $plainPassword = $request->request->get('password');

    $user = new User();
    $user->setEmail($email);
    $user->setUsername('Alexey');
    $encodedPassword = $this->container->get('security.password_encoder')->encodePassword($user, $plainPassword);
    $user->setPassword($encodedPassword);

    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    $token = $this->get('lexik_jwt_authentication.encoder')->encode([
      'username' => 'Alexey',
      'exp' => time() + 3600 // 1 hour expiration
    ]);

    $response = $this->createApiResponse([
      'data' => [
        'email' => $user->getEmail(),
        'username' => $user->getUsername(),
        'password' => $user->getPassword(),
      ],
      'meta' => ['token' => $token],
    ], 200);

    return $response;
  }
}
