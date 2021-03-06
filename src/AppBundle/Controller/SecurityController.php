<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\AutoLoginForm;
use AppBundle\Form\LoginForm;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class SecurityController extends BaseController {
  /**
   * @Rest\Post("/api/login")
   * {
   *   "email":"asdalexey@yandex.ru",
   *   "password": "121314"
   */
  public function login(Request $request) {
    $form = $this->createForm(LoginForm::class);
    $form->submit($request->request->all());

    if ($form->isValid()) {
      $formData = $form->getData();

      $em = $this->getDoctrine()->getManager();

      /**
       * @var User $user
       */
      $user = $em->getRepository('AppBundle:User')->findOneBy(['email' => $formData['email']]);
      if ($user) {
        $isValid = $this->get('security.password_encoder')
                        ->isPasswordValid($user, $formData['password']);
        if (!$isValid) {
          $responseData = ["errors" => 'Unauthorized'];
          return $this->createApiResponse($responseData, 401);
        }

        $token = $this->get('lexik_jwt_authentication.encoder')->encode([
          'id' => $user->getId(),
          'username' => $user->getEmail(),
          'exp' => time() + 3600 * 24 * 2 // 2 days expiration
        ]);

        $responseData = ["data" => $user, "meta" => ["token" => $token]];
        return $this->createApiResponse($responseData);
      } else {
        $responseData = ["errors" => 'Unauthorized'];
        return $this->createApiResponse($responseData, 401);
      }
    } else {
      $errors = $form->getErrors()->getForm();
      $responseData = ["errors" => $errors];
      return $this->createApiResponse($responseData, 400);
    }
  }

  /**
   * @Rest\Post("/api/auto-login")
   * {
   *   "token":"token_string",
   */
  public function autoLogin(Request $request) {
    $form = $this->createForm(AutoLoginForm::class);
    $form->submit($request->request->all());

    if ($form->isValid()) {
      $formData = $form->getData();

      $jwtData = $this->get('lexik_jwt_authentication.encoder')->decode($formData['token']);
      if (!($jwtData && isset($jwtData['id']))) {
        $responseData = ["errors" => "Invalid token"];
        return $this->createApiResponse($responseData, 400);
      }

      $em = $this->getDoctrine()->getManager();

      /**
       * @var User $user
       */
      $user = $em->getRepository('AppBundle:User')->find($jwtData['id']);
      if ($user) {
        $token = $this->get('lexik_jwt_authentication.encoder')->encode([
          'id' => $user->getId(),
          'username' => $user->getEmail(),
          'exp' => time() + 3600 * 24 * 2 // 2 days expiration
        ]);

        $responseData = [
          "data" => $user,
          "meta" => ["token" => $token]
        ];
        return $this->createApiResponse($responseData);
      } else {
        $responseData = ["errors" => 'Unauthorized'];
        return $this->createApiResponse($responseData, 401);
      }
    } else {
      $errors = $form->getErrors()->getForm();
      $responseData = ["errors" => $errors];
      return $this->createApiResponse($responseData, 400);
    }
  }
}
