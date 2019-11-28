<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Entity\Region;
use App\Entity\Room;
use App\Form\RoomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/owner")
 */
class OwnerController extends AbstractController
{
    /**
     * @Route("/", name="room_owned", methods={"GET"})
     * @Security("is_granted('ROLE_OWNER')")
     */
    public function index() {
        $user = $this->getUser();
        $owner = $user->getOwner();
        if(! $owner) {
            throw $this->createAccessDeniedException("Vous devez être propriétaire pour créer une chambre!");
        }

        return $this->render('owner/index.html.twig', [ 'rooms' => $owner->getRooms(), 'owner' => $owner ]);
    }

    /**
     * @Route("/create", name="room_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_OWNER')")
     */
    public function createRoom(Request $request)
    {
        $user = $this->getUser();
        $owner = $user->getOwner();
        if(! $owner) {
            throw $this->createAccessDeniedException("Vous devez être propriétaire pour créer une chambre!");
        }

        $room = new Room();
        $room->setOwner($owner);

        return $this->modifyRoom($room, $request);
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="room_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_OWNER')")
     */
    public function editRoom(Request $request, Room $room)
    {
        $user = $this->getUser();
        $owner = $user->getOwner();
        if(! $owner) {
            throw $this->createAccessDeniedException("Vous devez être propriétaire pour créer une chambre!");
        }
        if($owner != $room->getOwner()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas le propriétaire de cette chambre!");
        }

        return $this->modifyRoom($room, $request);
    }

    private function modifyRoom(Room $room, Request $request) {
        $regions = $this->getDoctrine()->getRepository(Region::class)->findAll();
        $form = $this->createForm(RoomType::class, $room, array("regions" => $regions));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $room = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('room_owned');
        }

        return $this->render('owner/modify.html.twig', [ 'create_room' => $form->createView() ]);
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="room_delete", methods={"POST"})
     * @Security("is_granted('ROLE_OWNER')")
     */
    public function removeRoom(Request $request, Room $room) {
        $user = $this->getUser();
        $owner = $user->getOwner();
        if(! $owner) {
            throw $this->createAccessDeniedException();
        }

        if($room->getOwner() != $owner) {
            throw $this->createAccessDeniedException();
        }

        $delete_form = $this->createFormBuilder()->create("delete-room")->getForm();

        $delete_form->handleRequest($request);

        $submittedToken = $request->request->get('token');

        // 'delete-item' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete-room', $submittedToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();

            return $this->redirectToRoute('room_owned');
        }

        throw $this->createAccessDeniedException();
    }
}
