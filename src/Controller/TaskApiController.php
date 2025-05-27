<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
class TaskApiController extends AbstractController
{
    #[Route('', name: 'api_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findAll();
        $data = array_map(fn($task) => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'isDone' => $task->isDone(),
            'createdAt' => $task->getCreatedAt()?->format('Y-m-d H:i:s'),
        ], $tasks);
        return $this->json($data);
    }

    #[Route('', name: 'api_task_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $task = new Task();
        $task->setTitle($data['title'] ?? '');
        $task->setDescription($data['description'] ?? '');
        $task->setIsDone($data['isDone'] ?? false);
        $task->setCreatedAt(new \DateTimeImmutable());
        $em->persist($task);
        $em->flush();
        return $this->json(['id' => $task->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_task_show', methods: ['GET'])]
    public function show(Task $task): JsonResponse
    {
        return $this->json([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'isDone' => $task->isDone(),
            'createdAt' => $task->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/{id}', name: 'api_task_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Task $task, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['title'])) $task->setTitle($data['title']);
        if (isset($data['description'])) $task->setDescription($data['description']);
        if (isset($data['isDone'])) $task->setIsDone($data['isDone']);
        $em->flush();
        return $this->json(['status' => 'updated']);
    }

    #[Route('/{id}', name: 'api_task_delete', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($task);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
