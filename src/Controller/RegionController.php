<?php

namespace App\Controller;

use App\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    /**
     * @Route("/region/{name}", name="region_show", methods="GET")
     * @param Region $region
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
     * @Route("/region/list", name="region_index", methods="GET")
     */
    public function index()
    {
        return $this->render('region/index.html.twig', [
            'controller_name' => 'RegionController',
        ]);
    }

    /**
     * @Route("/region/new", name="region_new")
     */
    public function new_region()
    {
        return $this->render('region/new.html.twig', [
            'controller_name' => 'RegionController',
        ]);
    }

    /**
     * @Route("/region/{id}/edit", name="region_edit")
     */
    public function edit()
    {
        return $this->render('region/edit.html.twig', [
            'controller_name' => 'RegionController',
        ]);
    }

    /**
     * @Route("/region/{id}/delete", name="region_delete", methods="DELETE")
     */
    public function delete()
    {
        return $this->render('region/delete.html.twig', [
            'controller_name' => 'RegionController',
        ]);
    }

    /**
     * Récupères une region avec son nom. Renvoie 'null' si aucune trouvée avec le nom donné
     * @param string $name
     * @return Region|object|null
     */
    private function getRegionByName(string $name) {
        $em = $this->getDoctrine()->getManager();
        $regionRepository = $em->getRepository(Region::class);
        $region = $regionRepository->findOneBy(array('name' => $name));
        return $region;
    }

}
