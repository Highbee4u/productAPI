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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;



class ProductController extends AbstractController
{
    private $productRepository;
    private $validator;
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->productRepository = $productRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }
    

    #[Route('/product', name: 'app_product')]
    public function index(): JsonResponse
    {

        $products = $this->productRepository->findAll();
        $data = json_decode($this->serializer->serialize($products, 'json'));

        
        return $this->json([
            'status' => true,
            'message' => 'all products',
            'data' => $data
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validator->validate($data);

        if (count($errors) > 0) {
            return $this->json([
                'status' => false,
                'message' => 'Unable to create product',
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }


        $productObj = new Product();
        
        $productObj->setName($data['name']);
        $productObj->setDescription($data['description']);
        $productObj->setPrice($data['price']);

       
        $this->entityManager->persist($productObj);
        $this->entityManager->flush();

        $data = json_decode($this->serializer->serialize($productObj, 'json'));


        return $this->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => $data
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

        $data = json_decode($this->serializer->serialize($product, 'json'));

        return $this->json([
            'status' => true,
            'message' => 'Product detail',
            'data' => $data
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

        $this->entityManager->flush();

        $data = json_decode($this->serializer->serialize($product, 'json'));

        return $this->json([
            'status' => true,
            'message' => 'Product record updated successfully',
            'data' => $data
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

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ], Response::HTTP_OK);
    }
}
