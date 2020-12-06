<?php

namespace App\Controller;

use App\Form\Recipe\RecipeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recipe")
 * @IsGranted("ROLE_USER")
 */
class RecipeController extends AbstractController
{
    /**
     * @Route("/create", name="recipe_create", methods={"GET", "POST"})
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(RecipeType::class);
        $form->handleRequest($request);

        return $this->render("recipe/create.html.twig", [
            "recipe_form" => $form->createView()
        ]);
    }
}
