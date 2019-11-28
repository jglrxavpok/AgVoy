<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{

    /**
     * @Route("/mine", name="my_profile")
     */
    public function myProfile() {
        return $this->redirectToRoute("profile", array("user" => $this->getUser()->getId()));
    }

    /**
     * @Route("/{user}", requirements={"user": "\d+"}, name="profile")
     */
    public function show(User $user)
    {

        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }
}
