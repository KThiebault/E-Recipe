<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Category\Categorytype;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route(name="category_index", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findBy(["user" => $this->getUser()]);

        return $this->render("category/index.html.twig", [
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/create", name="category_create", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
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

            return $this->redirectToRoute("category_index");
        }

        return $this->render("category/create.html.twig", [
            "category_form" => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id<[0-9]+>}", name="category_update", methods={"GET", "POST"})
     * @IsGranted("OWNER", subject="category")
     */
    public function update(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute("category_index");
        }

        return $this->render("category/update.html.twig", [
            "category_form" => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="category_delete", methods={"POST"})
     * @IsGranted("OWNER", subject="category")
     */
    public function delete(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('category_index');
        }
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute("category_index");
    }
}
