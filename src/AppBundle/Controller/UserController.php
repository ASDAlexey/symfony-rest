<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController {
  /**
   * @Route("/api/sign-up", name="user_sign_up")
   * @Method("POST")
   */
  public function signUpAction(Request $request) {
    //        $errors = array();
    //
    //        if (!$email = $request->request->get('email')) {
    //            $errors[] = '"email" is required';
    //        }
    //        if (!$plainPassword = $request->request->get('plainPassword')) {
    //            $errors[] = '"password" is required';
    //        }
    //        if (!$username = $request->request->get('username')) {
    //            $errors[] = '"username" is required';
    //        }
    //
    //        $userRepository = $this->getUserRepository();
    //
    //        // make sure we don't already have this user!
    //        if ($existingUser = $userRepository->findUserByEmail($email)) {
    //            $errors[] = 'A user with this email is already registered!';
    //        }
    //
    //        // make sure we don't already have this user!
    //        if ($existingUser = $userRepository->findUserByUsername($username)) {
    //            $errors[] = 'A user with this username is already registered!';
    //        }
    //
    $email = $request->request->get('email');
    $username = $request->request->get('username');
    $plainPassword = $request->request->get('password');

    $user = new User();
    $user->setEmail($email);
    $user->setUsername($username);
    $encodedPassword = $this->container->get('security.password_encoder')->encodePassword($user, $plainPassword);
    $user->setPassword($encodedPassword);

    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    //    $token = $this->get('lexik_jwt_authentication.encoder.abstract')->encode(['username' => $user->getUsername()]);

    $response = $this->createApiResponse([
      'data' => [
        'email' => $user->getEmail(),
        'username' => $user->getUsername(),
        'password' => $user->getPassword(),
      ],
      //      'meta' => ['token' => 'ddd'],
    ], 200);
    return $response;
    //
    //    return new JsonResponse([
    //      'user' => [
    //        'email' => $user->getEmail(),
    //        'username' => $user->getUsername(),
    //        'password' => $user->getPassword(),
    //      ],
    //    ]);
  }
}
