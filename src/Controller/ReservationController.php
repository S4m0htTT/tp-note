<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'get all reservation', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getAllReservation(EntityManagerInterface $entityManager): JsonResponse
    {
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();
        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'relation' => $reservation->getRelations(),
                'event_name' => $reservation->getEventName(),
                'time_slot' => $reservation->getTimeSlot(),
                'date' => $reservation->getDate()
            ];
        }
        return $this->json($data);
    }

    #[Route('/reservation/{id}', name: 'get reservation by Id', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getReservationById(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            return new JsonResponse(
                ['message' => 'Reservation not found'],
                404
            );
        }
        return $this->json([
            'id' => $reservation->getId(),
            'event_name' => $reservation->getEventName(),
            'time_slot' => $reservation->getTimeSlot(),
            'date' => $reservation->getDate(),
            'relation' => $reservation->getRelations()
        ], 200);
    }

    #[Route('/reservation', name: 'create reservation', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createReservation(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['date'], $data['time_slot'], $data['event_name'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $date = new \DateTime($data['date']);
        $timeSlot = ($data['time_slot']);
        $eventName = $data['event_name'];

        $reservation = new Reservation();
        $reservation->setDate($date);
        $reservation->setTimeSlot($timeSlot);
        $reservation->setEventName($eventName);

        $entityManager->persist($reservation);
        $entityManager->flush();
        return new JsonResponse('Reservation created successfully', 201);
    }

    #[Route('/reservation/user/{id}/{email}', name: 'Set reservation', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function setRelationTuUser(int $id, string $email, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse('User not found', 404);
        }

        $reservation = $entityManager->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            return new JsonResponse('Reservation not found', 404);
        }

        $user->addReservation($reservation);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse("Reservation added", 200);
    }

    #[Route('/reservation/{id}', name: 'delete reservation', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteReservation(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            return new JsonResponse(
                ['message' => 'Reservation not found'],
                404
            );
        }
        $entityManager->remove($reservation);
        $entityManager->flush();
        return new JsonResponse(
            ['status' => 'Reservation deleted!'],
            204
        );
    }
}
