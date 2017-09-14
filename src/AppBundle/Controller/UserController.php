<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\BaseController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends BaseController {
  /**
   * @Route("/api/sign-up", name="user_login")
   * @Method("POST")
   */
  //  public function signUpAction(Request $request) {
  //    $email = $request->request->get('email');
  //    $username = $request->request->get('username');
  //    $plainPassword = $request->request->get('password');
  //
  //    $user = new User();
  //    $user->setEmail($email);
  //    $user->setUsername($username);
  //    $encodedPassword = $this->container->get('security.password_encoder')->encodePassword($user, $plainPassword);
  //    $user->setPassword($encodedPassword);
  //
  //    $em = $this->getDoctrine()->getManager();
  //    $em->persist($user);
  //    $em->flush();
  //
  //    $token = $this->get('lexik_jwt_authentication.encoder')->encode([
  //      'id' => $user->getId(),
  //      'username' => $user->getEmail(),
  //      'exp' => time() + 3600 // 1 hour expiration
  //    ]);
  //
  //    $response = $this->createApiResponse([
  //      'data' => [
  //        'id' => $user->getId(),
  //        'email' => $user->getEmail(),
  //        'username' => $user->getUsername(),
  //        'password' => $user->getPassword(),
  //      ],
  //      'meta' => ['token' => $token],
  //    ], 200);
  //
  //    return $response;
  //  }

  /**
   * @Route("/api/sign-up", name="user_sign_up")
   * @Method("POST")
   */
  public function signUpAction(Request $request) {
//    $user = new User();
    $form = $this->createForm(UserRegistrationForm::class);
    $form->submit($request->request->all());

    if ($form->isValid()) {
      return $this->createApiResponse([
        "data" => $form->getData(),
        "meta" => [
          "errors" => null,
        ],
      ]);
    } else {
      $errors = $form->getErrors()->getForm();
      return $this->createApiResponse([
        "data" => null,
        "meta" => [
          "errors" => $errors,
        ],
      ]);
    }

    /*$email = $request->request->get('email');
    $plainPassword = $request->request->get('plainPassword');

    $user = new User();
    $user->setEmail($email);
    $user->setPlainPassword($plainPassword);
    $errors = $this->get('validator')->validate($user);
    return $this->createApiResponse([
      "data" => $user,
      "errors" => $errors,
    ]);*/

    //    $user = new User();
    //    $user->setEmail($email);
    //    $errors = $this->get('validator')->validate($user);

    //    $encodedPassword = $this->container->get('security.password_encoder')->encodePassword($user, $plainPassword);
    //    $user->setPlainPassword($plainPassword);
    //        $form = $this->createForm(new UserRegistrationForm(), $user);

    //    $errors = $this->validator->validate($user);

    //    if (count($errors) > 0) {
    //      throw new \Exception('Validation Errors: Receipt Invalid Data');
    //    }


    //    $response = [];
    //    if ($form->isValid()) {
    //      /**
    //       * @var User $user
    //       */
    //      $user = $form->getData();
    //      $user->setRoles(['ROLE_ADMIN']);
    //
    //      $em = $this->getDoctrine()->getManager();
    //      $em->persist($user);
    //      $em->flush();
    //
    //      $token = $this->get('lexik_jwt_authentication.encoder')->encode([
    //        'id' => $user->getId(),
    //        'username' => $user->getEmail(),
    //        'exp' => time() + 3600 * 24 * 2 // 2 days expiration
    //      ]);
    //
    //      $response = $this->createApiResponse([
    //        'data' => [
    //          'id' => $user->getId(),
    //          'email' => $user->getEmail(),
    //          'password' => $user->getPassword(),
    //        ],
    //        'meta' => ['token' => $token],
    //      ], 200);
    //    } else {
    //      $response = $this->createApiResponse([
    //        'data' => null,
    //        'meta' => ['errors' => $form->getErrors()],
    //      ], 400);
    //    }
    //
    //    return $response;
  }
}
