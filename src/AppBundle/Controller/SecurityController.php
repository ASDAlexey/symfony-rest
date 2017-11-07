<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\LoginForm;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SecurityController extends BaseController {

  /**
   * @Route("/api/login", name="security_login")
   * @Method("POST")
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
      $user = $em->getRepository('AppBundle:User')->findOneBy(['email' => $formData['email']]);


      $token = $this->get('lexik_jwt_authentication.encoder')->encode([
        'id' => $user->getId(),
        'username' => $user->getEmail(),
        'exp' => time() + 3600 * 24 * 2 // 2 days expiration
      ]);

      $responseData = ["data" => $user, "meta" => ["token" => $token]];
    } else {
      $errors = $form->getErrors()->getForm();
      $responseData = ["errors" => $errors];
    }

    return $this->createApiResponse($responseData);
  }
}
