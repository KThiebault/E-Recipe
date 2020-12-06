<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Category\Categorytype;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 * @IsGranted("ROLE_USER")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/create", name="category_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setUser($this->getUser());

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute("category_create");
        }

        return $this->render("category/create.html.twig", [
            "category_form" => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id<[0-9]+>}", name="category_update", methods={"GET", "POST"})
     */
    public function update(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute("category_create");
        }

        return $this->render("category/update.html.twig", [
            "category_form" => $form->createView()
        ]);
    }
}
