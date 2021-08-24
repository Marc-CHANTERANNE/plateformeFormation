<?php

namespace App\Controller;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    /**
     * @Route("/formation", name="formation")
     */
    public function index(): Response
    {
        $formations = $this->getDoctrine()->getRepository(Formation::class)->findAll();

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }
}
