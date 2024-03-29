<?php

namespace App\Controller;

use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePassword(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $em->flush();
            $this->addFlash('success', 'Password updated successfully !');
            
            return $this->redirectToRoute('app_profile');
        }

        return $this->renderForm('profile/index.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}