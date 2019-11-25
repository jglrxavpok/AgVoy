<?php

namespace App\Controller;

use App\Entity\Room;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/room")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/favorites", name="room_favorites")
     */
    public function showFavorites() {
        $session = $this->get("session");
        $likes = $session->get("likes");
        if(! $likes) {
            $likes = [];
        }
        // on convertit des IDs des chambres vers les objets correspondants
        $mapByID = function($id) {
            return $this->getRooms()->findOneBy(['id' => $id]);
        };
        $favorites = array_map($mapByID, $likes);
        return $this->render("room/favorites.html.twig", ["rooms" => $favorites]);
    }

    /**
     * @Route("/all", name="room_all")
     */
    public function showAll() {
        $rooms = $this->getRooms()->findAll();
        if(!$rooms) {
            $rooms = [];
        }
        // on convertit des IDs des chambres vers les objets correspondants
        return $this->render("room/all.html.twig", ["rooms" => $rooms]);
    }

    /**
     * @Route("/{id}", name="room_show", requirements={"id": "\d+"})
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(int $id)
    {
        $room = $this->getRooms()->findOneBy(array('id' => $id));
        $likes = $this->get("session")->get("likes");
        $isFavorite = true;
        $commentaries = $room->getCommentaries();
        if(! $likes || ! in_array($room->getId(), $likes)) {
            $isFavorite = false;
        }
        if (!$commentaries){
            $commentaries=[];
        }
        if($room) {
            $user = $this->getUser();
            $owner = $user->getOwner();
        $user = $this->getUser();
        if($room) {
            $isOwnRoom = false;
            $reservations = [];
            $delete_form = null;
            if($user) {
                $owner = $user->getOwner();
                if($owner && $owner == $room->getOwner()) {
                    $isOwnRoom = true;
                    $reservations = $room->getReservations();
                    $delete_form = $this->createFormBuilder()->create("delete-room")->getForm()->createView();
                }

            }
            return $this->render('room/show.html.twig', [
                'room' => $room, 'favorite' => $isFavorite, 'commentaries' => $commentaries, 'isOwnRoom' => $isOwnRoom, 'reservations' => $reservations, 'isOwnRoom' => $isOwnRoom, 'reservations' => $reservations, 'delete_form' => $delete_form
            ]);
        } else {
            return $this->render('room/404.html.twig');
        }
    }

    /**
     * @Route("/", name="room_index")
     */
    public function index()
    {
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @Route("/new", name="room_new")
     */
    public function new_room()
    {
        return $this->render('room/new.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @Route("/{id}/like", name="room_like")
     */
    public function toggleLike(Room $room) {
        $session = $this->get("session");
        $likes = $session->get("likes");

        if(! $likes) {
            $likes = [];
        }

        if(! in_array($room->getId(), $likes)) {
            $likes[] = $room->getId();
        } else {
            $likes = array_diff($likes, array($room->getId()));
        }

        $session->set("likes", $likes);
        return $this->redirectToRoute("room_index");
    }

    /**
     * @Route("/{id}/book", name="room_reservation", requirements={"id": "\d+"})
     * @Security("is_granted('ROLE_CLIENT')")
     */
    public function showReservation(Room $room) {

        return $this->render("room/reservation.html.twig", [
           "room" => $room
        ]);
    }

    /**
     * @return \App\Repository\RoomRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRooms() {
        return $this->getDoctrine()->getManager()->getRepository(Room::class);
    }

}
