<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Controller\BaseController;

class ProductController extends BaseController {
  /**
   * @Route("/api/products", name="product_list")
   * @Method("GET")
   */
  public function listAction(Request $request) {
    $response = $this->createApiResponse([
      'data' => [
        'name' => 'Products',
      ],
      'meta' => null,
    ], 200);

    return $response;
  }
}
