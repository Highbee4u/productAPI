<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testGetProducts()
    {
        $client = static::createClient();
        $client->request('GET', '/api/product');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateProduct()
    {
        $client = static::createClient();
        $client->request('POST', '/api/product', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Test Product', 'description' => 'Test Description', 'price' => 9.99])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

}