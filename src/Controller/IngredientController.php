<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\Category\Categorytype;
use App\Form\Ingredient\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/create", name="ingredient_create", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IngredientType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());

            $entityManager->persist($ingredient);
            $entityManager->flush();

            return $this->redirectToRoute("ingredient_index");
        }

        return $this->render("ingredient/create.html.twig", [
            "ingredient_form" => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id<[0-9]+>}", name="ingredient_update", methods={"GET", "POST"})
     * @IsGranted("OWNER", subject="ingredient")
     */
    public function update(Ingredient $ingredient, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute("ingredient_index");
        }

        return $this->render("ingredient/update.html.twig", [
            "ingredient_form" => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="ingredient_delete", methods={"POST"})
     * @IsGranted("OWNER", subject="ingredient")
     */
    public function delete(Ingredient $ingredient, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('ingredient_index');
        }
        $entityManager->remove($ingredient);
        $entityManager->flush();
        return $this->redirectToRoute("ingredient_index");
    }
}
