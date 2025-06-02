<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskForm;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route(name: 'app_task_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        TaskRepository $taskRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ): Response {
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', '');

        $isDone = null;
        if ($status === 'done') {
            $isDone = true;
        } elseif ($status === 'not_done') {
            $isDone = false;
        }

        $queryBuilder = $taskRepository->createQueryBuilder('t');

        if (!empty($search)) {
            $queryBuilder
                ->andWhere('t.title LIKE :search OR t.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($isDone !== null) {
            $queryBuilder
                ->andWhere('t.isDone = :done')
                ->setParameter('done', $isDone);
        }

        $queryBuilder->orderBy('t.createdAt', 'DESC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        // ⬇ Crée le formulaire pour la modale (ajout direct dans la page)
        $task = new Task();
        $form = $this->createForm(TaskForm::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle tâche ajoutée avec succès.');
            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $pagination,
            'search' => $search,
            'status' => $status,
            'form' => $form->createView(), 
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskForm::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index');
    }
}
