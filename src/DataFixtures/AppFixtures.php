<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Owner;
use App\Entity\Region;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public const IDF_REGION_REFERENCE = 'idf-region';
    public const NORD_PAS_DE_CALAIS_REGION_REFERENCE = 'npdc-region';
    public const NEW_YORK_REGION_REFERENCE = 'ny-region';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    private function loadUsers(ObjectManager $manager) {
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
        $ownerObj->setFirstName("John");
        $ownerObj->setFamilyName("OfTheGarden");
        $ownerObj->setCountry("UK");

        $owner = new User();
        $owner->setOwner($ownerObj);
        $owner->setEmail("owner@example.com");
        $owner->setPassword($this->passwordEncoder->encodePassword($owner, "abc"));
        $owner->addRole("ROLE_OWNER");

        $manager->persist($owner);


        $manager->flush();
    }

    private function createFakeUser(ObjectManager $manager, Owner $owner) {
        $user = new User();
        $user->setEmail(strtolower($owner->getFirstName()) . '.' . strtolower($owner->getFamilyName()) . "@example.com");
        $user->setPassword($this->passwordEncoder->encodePassword($user, "abc"));
        $user->addRole("ROLE_OWNER");
        $owner->setUser($user);
        $manager->persist($user);
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $marcDupond = new Owner();
        $marcDupond->setFirstName("Marc");
        $marcDupond->setFamilyName("Dupond");
        $marcDupond->setCountry("FR");
        $this->createFakeUser($manager, $marcDupond);

        $manager->persist($marcDupond);

        $jackSmith = new Owner();
        $jackSmith->setFirstName("Jack");
        $jackSmith->setFamilyName("Smith");
        $jackSmith->setCountry("US");
        $this->createFakeUser($manager, $jackSmith);

        $manager->persist($jackSmith);

        $regionIDF = new Region();
        $regionIDF->setCountry("FR");
        $regionIDF->setName("Ile de France");
        $regionIDF->setPresentation("La région française capitale");

        $manager->persist($regionIDF);

        $regionNPDC = new Region();
        $regionNPDC->setCountry("FR");
        $regionNPDC->setName("Nord Pas De Calais");
        $regionNPDC->setPresentation("La région la plus au Nord de France");

        $manager->persist($regionNPDC);

        $regionNY = new Region();
        $regionNY->setCountry("US");
        $regionNY->setName("New York State");
        $regionNY->setPresentation("L'État de New York (« State of New York ») est un État des États-Unis, le quatrième plus peuplé du pays avec 19,8 millions d'habitants en 2017. Il se trouve dans le Nord-Est du pays et a pour capitale la ville d'Albany, située dans l'Est de l'État.");

        $manager->persist($regionNY);

        $manager->flush();
        // Une fois l'instance de Region sauvée en base de données,
        // elle dispose d'un identifiant généré par Doctrine, et peut
        // donc être sauvegardée comme future référence.
        $this->addReference(self::IDF_REGION_REFERENCE, $regionIDF);
        $this->addReference(self::NORD_PAS_DE_CALAIS_REGION_REFERENCE, $regionNPDC);
        $this->addReference(self::NEW_YORK_REGION_REFERENCE, $regionNY);

        // ...

        $roomTourEiffel = new Room();
        $roomTourEiffel->setDescription("très joli espace sur paille");
        $roomTourEiffel->setAddress("1 rue de la paix, 75000 Paris");
        $roomTourEiffel->setCapacity(1);
        $roomTourEiffel->setPrice(123499); // 1234.99€/nuit
        $roomTourEiffel->setSummary("Petit appartement charmant avec vue sur la Tour Eiffel");
        $roomTourEiffel->setSuperficy(10);
        $roomTourEiffel->setImageName("Eiffel.jpg");
        //$roomTourEiffel->addRegion($regionIDF);
        // On peut plutôt faire une référence explicite à la référence
        // enregistrée précédamment, ce qui permet d'éviter de se
        // tromper d'instance de Region :
        $roomTourEiffel->addRegion($this->getReference(self::IDF_REGION_REFERENCE));
        $marcDupond->addRoom($roomTourEiffel);
        $manager->persist($roomTourEiffel);

        $roomEntrepot = new Room();
        $roomEntrepot->setDescription("A moins de 10min des digues");
        $roomEntrepot->setAddress("2 rue de la paix, 59140 Dunkerque");
        $roomEntrepot->setCapacity(10);
        $roomEntrepot->setPrice(199); // 1.99€/nuit
        $roomEntrepot->setSummary("Entrepôt désaffecté");
        $roomEntrepot->setSuperficy(200);
        $roomEntrepot->setImageName("Dunkerque.jpg");
        //$roomTourEiffel->addRegion($regionIDF);
        // On peut plutôt faire une référence explicite à la référence
        // enregistrée précédamment, ce qui permet d'éviter de se
        // tromper d'instance de Region :
        $roomEntrepot->addRegion($this->getReference(self::NORD_PAS_DE_CALAIS_REGION_REFERENCE));
        $marcDupond->addRoom($roomEntrepot);

        $manager->persist($roomEntrepot);


        $roomCityHall = new Room();
        $roomCityHall->setDescription("Devenez maire de la ville d'Albany!");
        $roomCityHall->setAddress("24 Eagle St, Albany, NY 12207, États-Unis");
        $roomCityHall->setCapacity(1);
        $roomCityHall->setPrice(20099); // 1.99€/nuit
        $roomCityHall->setSummary("Poste de maire à prendre");
        $roomCityHall->setSuperficy(100);
        $roomCityHall->addRegion($this->getReference(self::NEW_YORK_REGION_REFERENCE));
        $roomCityHall->setImageName("WhiteHouse.jpg");
        $jackSmith->addRoom($roomCityHall);

        $manager->persist($roomCityHall);

        $manager->flush();
    }
}
