<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserRegistrationForm;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends BaseController {

  /**
   * @Rest\Post("/api/sign-up")
   * {
   *   "email":"asdalexey@yandex.ru",
   *   "plainPassword":{
   *      "first":"121314",
   *      "second":"121314"
   *    }
   * }
   */
  public function signUpAction(Request $request) {
    $form = $this->createForm(UserRegistrationForm::class);
    $form->submit($request->request->all());

    if ($form->isValid()) {
      $formData = $form->getData();

      /**
       * @var User $user
       */
      $user = new User();
      $user->setEmail($formData->getEmail());
      $encodedPassword = $this->container->get('security.password_encoder')
                                         ->encodePassword($user, $formData->getPlainPassword());
      $user->setPassword($encodedPassword);
      $user->setRoles(['ROLE_ADMIN']);
      $user->setCreatedAt();

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      $token = $this->get('lexik_jwt_authentication.encoder')->encode([
        'id' => $user->getId(),
        'username' => $user->getEmail(),
        'exp' => time() + 3600 * 24 * 2 // 2 days expiration
      ]);

      $responseData = ["data" => $user, "meta" => ["token" => $token]];
      return $this->createApiResponse($responseData);
    } else {
      $errors = $form->getErrors()->getForm();
      $responseData = ["errors" => $errors];
      return $this->createApiResponse($responseData, 400);
    }
  }
}
