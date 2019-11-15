<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/room/book")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/{id}", name="room_make_reservation", methods={"POST"}, requirements={"id": "\d+"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function make(Request $request, Room $room) {
        $user = $this->getUser();
        $client = $user->getClient();
        if(!$client) {
            throw new Exception("Not a client");
        }

        $start = $request->request->get('start');
        $end = $request->request->get('end');

        $startTime = DateTime::createFromFormat('Y-m-d', $start);
        $endTime = DateTime::createFromFormat('Y-m-d', $end);

        $reservation = new Reservation();
        $reservation->setRoom($room);
        $reservation->setClient($client);
        $reservation->setStartTime($startTime);
        $reservation->setEndTime($endTime);

        // TODO: vérifier qu'il n'y a pas déjà une réservation pour cet intervalle
        $entityManager = $this->getDoctrine()->getManager();

        $reservations = $entityManager->getRepository(Reservation::class);
        $clashing = $reservations->createQueryBuilder("r")
            ->where("NOT (:endTime < r.startTime OR :startTime > r.endTime OR r.endTime < :startTime OR r.startTime > :endTime)")
            ->setParameter("startTime", $startTime)
            ->setParameter("endTime", $endTime)
            ->getQuery()->getResult();
        ;
        if(count($clashing) > 0) {
            return new JsonResponse([
                "no" => "no",
            ]);
        }

        $entityManager->persist($reservation);
        $entityManager->flush();

        return new JsonResponse([
            "room" => $reservation->getRoom()->getSummary(),
            "start" => $reservation->getStartTime(),
            "end" => $reservation->getEndTime(),
        ]);
    }
}
