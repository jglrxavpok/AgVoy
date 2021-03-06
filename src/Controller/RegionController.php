<?php

namespace App\Controller;

use App\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/region")
 */
class RegionController extends AbstractController
{
    /**
     * @Route("/", name="region_index", methods="GET")
     */
    public function index()
    {
        return $this->render('region/index.html.twig', [
            'regions' => $this->getRegions()->findAll()
        ]);
    }

    /**
     * @Route("/{name}", name="region_show", methods="GET")
     * @param Region $myregion
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(string $name)
    {
        $region = $this->getRegionByName($name);
        if($region) {
            return $this->render('region/show.html.twig', [
                'region' => $region,
            ]);
        } else {
            return $this->render('region/404.html.twig', [
                'name' => $name
            ]);
        }
    }

    /**
     * Récupères une region avec son nom. Renvoie 'null' si aucune trouvée avec le nom donné
     * @param string $name
     * @return Region|object|null
     */
    private function getRegionByName(string $name) {
        return $this->getRegions()->findOneBy(['name' => $name]);
    }

    /**
     * Renvoies le repository avec toutes les régions disponibles
     * @param string $name
     * @return \App\Repository\RegionRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRegions() {
        $em = $this->getDoctrine()->getManager();
        $regionRepository = $em->getRepository(Region::class);
        return $regionRepository;
    }

}
