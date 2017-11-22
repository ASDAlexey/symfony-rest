<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\ProductFormType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class ProductController extends BaseController {
  const LIMIT = 5;
  const OFFSET = 0;

  /**
   * @Rest\Get("/api/products")
   */
  public function index(Request $request) {
    $em = $this->getDoctrine()->getManager();

    /**
     * @var User $user
     */
    $user = $this->getUser();
    $products = $em->getRepository('AppBundle:Product')
                   ->findBy(["user" => $user], ['createdAt' => 'ASC'], self::LIMIT, self::OFFSET);
    return $this->createApiResponse(["data" => $products]);
  }

  /**
   * @Rest\Get("/api/products/{id}")
   */
  public function show($id) {
    $em = $this->getDoctrine()->getManager();
    /**
     * @var Product $product
     */
    $product = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

    /**
     * @var User $user
     */
    if ($product) {
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
    } else return $this->createApiResponse(["meta" => ["errors" => "Not found"]], 404);
  }

  /**
   * @Rest\Post("/api/products")
   */
  public function create(Request $request) {
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
  public function update(Request $request, Product $product) {
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

  /**
   * @Rest\Delete("/api/products/{id}")
   */
  public function destroy(Product $product) {
    if (!$product) throw $this->createNotFoundException('No product found');

    $em = $this->getDoctrine()->getManager();
    $em->remove($product);
    $em->flush();

    return $this->createApiResponse(["data" => "The product was deleted"]);
  }
}
