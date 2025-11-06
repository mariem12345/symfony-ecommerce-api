<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductIntegrationTest extends WebTestCase
{
    public function testListProducts(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/products');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testCreateProductWithoutAuthentication(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/products', [], [], [], json_encode([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100.00,
            'category' => 'electronics'
        ]));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testCreateProductWithAuthentication(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer admintoken'
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100.00,
            'category' => 'electronics'
        ]));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('price_with_vat', $data);
    }
}
