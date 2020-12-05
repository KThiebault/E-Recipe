<?php

namespace App\Controller;

use App\Form\Security\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="security_register", methods={"GET", "POST"})
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute("home");
        }

        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render("security/registration.html.twig", [
            "registration_form" => $form->createView()
        ]);
    }
}
