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
  const ORDER_BY = 'createdAt';
  const DIRECTION = 'ASC';

  /**
   * @Rest\Get("/api/products")
   */
  public function index(Request $request) {
    $query = $request->query->all();
    $em = $this->getDoctrine()->getManager();
    $errors = [];

    /**
     * @var User $user
     */
    $user = $this->getUser();

    // validation query
    if (isset($query['offset']) && !is_numeric($query['offset'])) $errors['offset'] = 'offset should be number';
    if (isset($query['limit']) && !is_numeric($query['limit'])) $errors['limit'] = 'limit should be number';
    if (!empty($errors)) return $this->createApiResponse(["errors" => $errors]);

    $offset = isset($query['offset']) ? $query['offset'] : self::OFFSET;
    $limit = isset($query['limit']) ? $query['limit'] : self::LIMIT;
    $repository = $em->getRepository('AppBundle:Product');
    $params = ["user" => $user];
    $products = $repository->findBy($params, ['createdAt' => 'ASC'], $limit, $offset);
    $count = count($repository->findBy($params));
    return $this->createApiResponse(["data" => $products, "meta" => ["count" => $count]]);
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
  public function update(Request $request,
                         Product $product
  ) {
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
