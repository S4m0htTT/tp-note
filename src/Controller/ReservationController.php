<?php

namespace App\Controller;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'get all reservation', methods: ['GET'])]
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

    #[Route('/reservation', name: 'create reservation', methods: ['POST'])]
    public function createReservation(Request $request, EntityManagerInterface $entityManager): JsonResponse {
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
}
