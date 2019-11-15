<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Owner;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $client = new Client();
        $client->setEmail("user@example.com");
        $user = new User();
        $user->setEmail("user@example.com");
        $user->setPassword($this->passwordEncoder->encodePassword($user, "abc"));
        $user->setClient($client);
        $user->addRole("ROLE_CLIENT");

        $manager->persist($user);

        $admin = new User();
        $admin->setEmail("admin@example.com");
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, "abc"));
        $admin->addRole("ROLE_ADMIN");

        $manager->persist($admin);

        $ownerObj = new Owner();
        $ownerObj->setFirstName("Owner");
        $ownerObj->setFamilyName("Owner");
        $ownerObj->setCountry("FR");

        $owner = new User();
        $owner->setOwner($ownerObj);
        $owner->setEmail("owner@example.com");
        $owner->setPassword($this->passwordEncoder->encodePassword($owner, "abc"));
        $owner->addRole("ROLE_OWNER");

        $manager->persist($owner);


        $manager->flush();
    }
}
