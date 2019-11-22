<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Reservation;
use App\Entity\Room;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/{id}/make", name="room_make_reservation", methods={"POST"}, requirements={"id": "\d+"})
     * @Security("is_granted('ROLE_CLIENT')")
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
            ->where("r.room = :room AND NOT (:endTime < r.startTime OR :startTime > r.endTime OR r.endTime < :startTime OR r.startTime > :endTime)")
            ->setParameter("startTime", $startTime)
            ->setParameter("endTime", $endTime)
            ->setParameter("room", $room)
            ->getQuery()->getResult();
        ;
        if(count($clashing) > 0) {
            return new JsonResponse([
                "error" => "Une réservation existe déjà pour cette chambre!"
            ], 403);
        }

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash("success", "Réservation effectuée!");

        return new Response('', 200);
    }

    /**
     * @Route("/{id}/cancel", name="room_cancel_reservation", methods={"POST"}, requirements={"id": "\d+"})
     * @Security("is_granted('ROLE_CLIENT')")
     */
    public function cancel(Reservation $reservation)
    {
        $user = $this->getUser();
        $client = $user->getClient();
        if(!$client) {
            throw $this->createAccessDeniedException("Not a client");
        }

        if($reservation->getClient() != $client) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash("warning", "Réservation annulée");
        return new Response('', 200);
    }

    /**
     * @Route("/", name="my_reservations", methods={"GET"})
     * @Security("is_granted('ROLE_CLIENT')")
     * @throws Exception
     */
    public function myReservations() {
        $user = $this->getUser();
        if(!$user) {
            throw new Exception("Not connected");
        }

        $client = $user->getClient();
        if(!$client) {
            throw $this->createAccessDeniedException("Not a client");
        }

        return $this->render("reservation/index.html.twig",[
            "reservations" => $client->getReservations()
            ]);
    }

    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"}, requirements={"id": "\d+"})
     * @Security("is_granted('ROLE_CLIENT')")
     * @throws Exception
     */
    public function showReservation(Reservation $reservation) {
        $user = $this->getUser();
        $client = $user->getClient();
        if(!$client) {
            throw $this->createAccessDeniedException("Not a client");
        }

        if($reservation->getClient() != $client) {
            throw $this->createAccessDeniedException();
        }

        return $this->render("reservation/show.html.twig", ["reservation" => $reservation]);
    }
}
