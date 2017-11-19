<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
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
      //      // Update the 'avatar' property to store file name
      //      // instead of its contents
      //      $product->setImage($fileName);

      /**
       * @var Product $product
       */
      $product = new Product();
      $product->setName($formData->getName());
      $product->setPrice($formData->getPrice());
      $product->setDescription($formData->getDescription());
      $product->setColor($formData->getColor());
      $product->setYear($formData->getYear());
      // $file stores the uploaded file
      //      $file = $product->getImage();
      //      $fileName = $this->get('app.image')->move($file);
      //      // Update the 'avatar' property to store file name
      //      // instead of its contents
      //      $product->setImage($fileName);

      $product->setUser($this->getUser());
      $product->setCreatedAt();

      $em = $this->getDoctrine()->getManager();
      $em->persist($product);
      $em->flush();

      $responseData = [
        "data" => $product,
      ];
      return $this->createApiResponse($responseData);
    } else {
      $errors = $form->getErrors()->getForm();
      $responseData = ["errors" => $errors];
      return $this->createApiResponse($responseData, 400);
    }
    //    // only handles data on POST
    //    $form->handleRequest($request);
    //
    //    if ($form->isSubmitted() && $form->isValid()) {
    //      $product = $form->getData();
    //
    //      // $file stores the uploaded file
    //      $file = $product->getImage();
    //
    //      $fileName = $this->get('app.image')->move($file);
    //
    //      // Update the 'avatar' property to store file name
    //      // instead of its contents
    //      $product->setImage($fileName);
    //
    //      $product->setUser($this->getUser());
    //
    //      $em = $this->getDoctrine()->getManager();
    //      $em->persist($product);
    //      $em->flush();
    //
    //      $this->addFlash('success', 'Product created');
    //
    //      return $this->redirectToRoute('product_list');
    //    }
    //
    //    return $this->render('product/edit.html.twig', ['productForm' => $form->createView(), 'imageName' => null]);
  }
}
