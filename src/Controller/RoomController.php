<?php

namespace App\Controller;

use App\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    /**
     * @Route("/room/{id}", name="room_show", requirements={"id": "\d+"})
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(int $id)
    {
        $room = $this->getRooms()->findOneBy(array('id' => $id));
        if($room) {
            return $this->render('room/show.html.twig', [
                'room' => $room,
            ]);
        } else {
            return $this->render('room/404.html.twig');
        }
    }

    /**
     * @Route("/room/list", name="room_index")
     */
    public function index()
    {
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @Route("/room/new", name="room_new")
     */
    public function new_room()
    {
        return $this->render('room/new.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @Route("/room/{id}/edit", name="room_edit")
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Room $room)
    {
        return $this->render('room/edit.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @Route("/room/{id}/delete", name="room_delete")
     * @param Room $room
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Room $room)
    {
        return $this->render('room/delete.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    /**
     * @Route("/room/{id}/like", name="room_like")
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
     * @return \App\Repository\RoomRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRooms() {
        return $this->getDoctrine()->getManager()->getRepository(Room::class);
    }
}
