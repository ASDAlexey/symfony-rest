<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\ProductFormType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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

  /**
   * @Route("/api/products", name="product_new")
   */
  public function newAction(Request $request) {
    $form = $this->createForm(ProductFormType::class);
    $form->submit($request->request->all());

    if ($form->isValid()) {
      $formData = $form->getData();

      /**
       * @var Product $product
       */
      $product = new Product();
      $product->setName($formData->getName());
      $product->setPrice($formData->getPrice());
      $product->setDescription($formData->getDescription());
      $product->setColor($formData->getColor());
      $product->setYear($formData->getYear());
      if ($request->files->get('image')) {
        $product->setImage($request->files->get('image'));
      }

      // $file stores the uploaded file
      $file = $product->getImage();
      if ($file) {
        $fileName = $this->get('app.image')->move($file);

        // update the 'image' property to store file name
        $product->setImage($fileName);
      }

      $product->setUser($this->getUser());
      $product->setCreatedAt();

      $em = $this->getDoctrine()->getManager();
      $em->persist($product);
      $em->flush();

      /**
       * @var User $user
       */
      $user = $product->getUser();

      $responseData = [
        "data" => [
          "id" => $product->getId(),
          "name" => $product->getName(),
          "price" => $product->getPrice(),
          "description" => $product->getDescription(),
          "color" => $product->getColor(),
          "year" => $product->getYear(),
          "image" => $product->getImage(),
          "user" => [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "roles" => $user->getRoles(),
            "createdAt" => $user->getCreatedAt(),
            "updatedAt" => $user->getUpdatedAt(),
          ],
          "createdAt" => $product->getCreatedAt(),
          "updatedAt" => $product->getUpdatedAt(),
        ],
      ];
      return $this->createApiResponse($responseData);
    } else {
      $errors = $form->getErrors()->getForm();
      $responseData = ["errors" => $errors];
      return $this->createApiResponse($responseData, 400);
    }
  }
}
