<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/users')]
class UserApiController extends AbstractController
{
    private const ID_PATH = '/{id}';

    #[Route('', name: 'api_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = array_map(fn($user) => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], $users);
        return $this->json($data);
    }

    #[Route('', name: 'api_user_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setEmail($data['email'] ?? '');
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);
        if (!empty($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }
        $em->persist($user);
        $em->flush();
        return $this->json(['id' => $user->getId()], Response::HTTP_CREATED);
    }

    #[Route(self::ID_PATH, name: 'api_user_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route(self::ID_PATH, name: 'api_user_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, User $user, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }
        if (isset($data['password']) && $data['password']) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }
        $em->flush();
        return $this->json(['status' => 'updated']);
    }

    #[Route(self::ID_PATH, name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();
        return $this->json(['status' => 'deleted']);
    }
}
