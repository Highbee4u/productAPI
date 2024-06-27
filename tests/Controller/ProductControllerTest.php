<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class ProductControllerTest extends WebTestCase
{    
    private $client;
    private $productRepository;
    private $jwtToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->productRepository = static::$container->get('doctrine')->getRepository(Product::class);

        // Log in and obtain JWT token for authentication (replace with your login details)
        $this->client->request('POST', '/api/login_check', [
            'username' => 'your_default_username',
            'password' => 'your_default_password',
        ]);

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->jwtToken = $data['token'];
    }

    public function testCreateProduct()
    {
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken],
            json_encode(['name' => 'Test Product'])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('Test Product', $data['name']);
    }

    public function testGetProduct()
    {
        $product = new Product();
        $product->setName('Test Product');
        $this->productRepository->add($product, true);

        $this->client->request(
            'GET',
            '/api/products/' . $product->getId(),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals($product->getId(), $data['id']);
        $this->assertEquals('Test Product', $data['name']);
    }

    public function testUpdateProduct()
    {
        $product = new Product();
        $product->setName('Test Product');
        $this->productRepository->add($product, true);

        $this->client->request(
            'PUT',
            '/api/products/' . $product->getId(),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken],
            json_encode(['name' => 'Updated Product'])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals($product->getId(), $data['id']);
        $this->assertEquals('Updated Product', $data['name']);
    }

    public function testDeleteProduct()
    {
        $product = new Product();
        $product->setName('Test Product');
        $this->productRepository->add($product, true);

        $this->client->request(
            'DELETE',
            '/api/products/' . $product->getId(),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $deletedProduct = $this->productRepository->find($product->getId());
        $this->assertNull($deletedProduct);
    }

    public function testListProducts()
    {
        $product1 = new Product();
        $product1->setName('Product 1');
        $this->productRepository->add($product1, true);

        $product2 = new Product();
        $product2->setName('Product 2');
        $this->productRepository->add($product2, true);

        $this->client->request(
            'GET',
            '/api/products',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertCount(2, $data);
    }
}