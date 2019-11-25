<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Room;
use App\Form\RoomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('owner/create.html.twig', [ 'create_room' => $form->createView() ]);
    }
}
