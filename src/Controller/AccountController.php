<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="account_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render("account/index.html.twig");
    }
}
