<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserApiControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');
        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testCreateAndDeleteUser(): void
    {
        $client = static::createClient();
        $email = 'phpunit_test_' . uniqid() . '@example.com';
        $password = 'testpass';
        // Create user
        $client->request('POST', '/api/users', [
            'CONTENT_TYPE' => 'application/json',
        ], [], [], json_encode([
            'email' => $email,
            'password' => $password
        ]));
        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $userId = $data['id'];
        // Delete user
        $client->request('DELETE', '/api/users/' . $userId);
        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString('{"status":"deleted"}', $client->getResponse()->getContent());
    }
}
