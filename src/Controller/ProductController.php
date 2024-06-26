<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    private $productRepository;
    private $validator;

    public function __construct(ProductRepository $productRepository, ValidatorInterface $validator)
    {
        $this->productRepository = $productRepository;
        $this->validator = $validator;
    }
    

    #[Route('/product', name: 'app_product')]
    public function index(): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $products = $this->productRepository->findAllWithPagination($page, $limit);

        return $this->json([
            'status' => true,
            'message' => 'all products',
            'data' => $products
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return $this->json([
                'status' => false,
                'message' => 'Unable to create product',
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->productRepository->create($product);

        return $this->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->findOneById($id);

        if (!$product) {
            return $this->json([
                'status' => false,
                'message' => 'No record march the ID supplied'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'status' => true,
            'message' => 'Product detail',
            'data' => $product
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->findOneById($id);

        if (!$product) {
            return $this->json([
                'status' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $this->validator->validate($product);
        
        if (count($errors) > 0) {
            return $this->json([
                'status' => false,
                'message' => 'Error while updating product details',
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->productRepository->updateProduct($product);

        return $this->json([
            'status' => true,
            'message' => 'Product record updated successfully',
            'data' => $product
        ], Response::HTTP_OK);
    }


    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->productRepository->findOneById($id);

        if (!$product) {
            return $this->json([
                'status' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $this->productRepository->remove($product);

        return $this->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ], Response::HTTP_NO_CONTENT);
    }
}
