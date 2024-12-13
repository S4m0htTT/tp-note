<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user', name: 'get user', methods: ['GET'])]
    public function getAllusers(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'phone_number' => $user->getPhoneNumber(),
                'role' => $user->getRoles(),
                'reservations' => $user->getReservations()
            ];
        }
        return $this->json($data);
    }

    #[Route('/user', name: 'create user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['name'], $data['phone_number'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $email = $data['email'];
        $password = $data['password'];
        $name = $data['name'];
        $phone_number = $data['phone_number'];

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Email is already in use'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setName($name);
        $user->setRoles(['ROLE_USER']);
        $user->setPhoneNumber($phone_number);
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse('User created successfully', 201);
    }

    #[Route('/user/{id}', name: 'delete user', methods: ['DELETE'])]
    public function deleteUser(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(
                ['message' => 'User not found'],
                404
            );
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse(
            ['status' => 'User deleted!'],
            204
        );
    }

    #[Route('/user/{id}', name: 'update user', methods: ['PUT'])]
    public function updateUser(int $id, Request $request, EntityManagerInterface
    $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(
                ['message' => 'user not found'],
                404
            );
        }
        $data = json_decode($request->getContent(), true);
        $user->setPhoneNumber($data['phone_number']);
        $user->setName($data['name']);
        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        }

        $entityManager->flush();
        return new JsonResponse(['status' => 'user updated!'], 200);
    }

    #[Route('/user/reservation/{id}', name: 'get reservation by user', methods: ['GET'])]
    public function getReservationByUserId(int $id, Request $request, EntityManagerInterface
    $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(
                ['message' => 'user not found'],
                404
            );
        }
        $reservations = $user->getReservations();
        return new JsonResponse($reservations, 200);
    }

    #[Route('/user/{id}', name: 'get user by id', methods: ['GET'])]
    public function getuserById(int $id, Request $request, EntityManagerInterface
    $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(
                ['message' => 'user not found'],
                404
            );
        }
        return new JsonResponse(
            [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'phone_number' => $user->getPhoneNumber(),
                'reservations' => $user->getReservations()
            ],
            200
        );
    }
}
