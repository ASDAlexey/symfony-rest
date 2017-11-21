<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\ProductFormType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;

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
   * @Rest\Get("/api/products/{id}")
   */
  public function showAction($id) {
    $em = $this->getDoctrine()->getManager();
    /**
     * @var Product $product
     */
    $product = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

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
  }

  /**
   * @Rest\Post("/api/products")
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

  /**
   * @Rest\Post("/api/products/{id}")
   */
  public function editAction(Request $request, Product $product) {
    $form = $this->createForm(ProductFormType::class);
    $form->submit($request->request->all());

    if ($form->isValid()) {
      $formData = $form->getData();

      /**
       * @var Product $product
       */
      if ($formData->getName()) $product->setName($formData->getName());
      if ($formData->getPrice()) $product->setPrice($formData->getPrice());
      if ($formData->getDescription()) $product->setDescription($formData->getDescription());
      if ($formData->getColor()) $product->setColor($formData->getColor());
      if ($formData->getYear()) $product->setYear($formData->getYear());
      if ($request->files->get('image')) {
        $this->get('app.image')->remove($product->getImage());
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
