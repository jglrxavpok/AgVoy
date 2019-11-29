<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\Region;
use App\Entity\Room;
use App\Form\CommentaryType;
use App\Form\RoomType;
use http\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentaryController extends AbstractController
{
    /**
     * @Route("/commentary/{id}", requirements={"id": "\d+"}, name="commentary", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_CLIENT')")
     */
    public function createCommentary(Request $request, Room $room)
    {
        $user = $this->getUser();
        $client = $user->getClient();
        $roomId = $room ->getId();



        if(! $client) {
            throw $this->createAccessDeniedException("Vous devez être client pour créer une chambre!");
        }

        $commentary = new Commentary();
        $commentary->setClient($client);
        $commentary->setRoom($room);


        $form = $this->createForm(CommentaryType::class, $commentary);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentary = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentary);
            $entityManager->flush();

            return $this->redirectToRoute('room_show', array('id' => $roomId));
        }

        return $this->render('commentary/modify.html.twig', [ 'create_com' => $form->createView() ]);
    }
}
