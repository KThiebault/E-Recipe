<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ingredient")
 */
class IngredientController extends AbstractController
{
    /**
     * @Route(name="ingredient_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(IngredientRepository $repository): Response
    {
        return $this->render("ingredient/index.html.twig", [
            "ingredients" => $repository->findBy(["user" => $this->getUser()])
        ]);
    }
}
