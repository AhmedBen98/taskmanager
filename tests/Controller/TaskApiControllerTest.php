<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskApiControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks');
        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testCreateAndDeleteTask(): void
    {
        $client = static::createClient();
        $title = 'Task PHPUnit ' . uniqid();
        $description = 'Description for PHPUnit test';
        // Create task
        $client->request('POST', '/api/tasks', [
            'CONTENT_TYPE' => 'application/json',
        ], [], [], json_encode([
            'title' => $title,
            'description' => $description,
            'isDone' => false
        ]));
        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $taskId = $data['id'];
        // Delete task
        $client->request('DELETE', '/api/tasks/' . $taskId);
        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString('{"status":"deleted"}', $client->getResponse()->getContent());
    }
}
