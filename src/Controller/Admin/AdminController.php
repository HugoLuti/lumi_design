<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/admin/create', name: 'app_admin')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $formAdmin = $this->createForm(AdminType::class, $user);
        $formAdmin->handleRequest($request);

        if ($formAdmin->isSubmitted() && $formAdmin->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $formAdmin->get('plainPassword')->getData()
                ),
                $user->setRoles(['ROLE_ADMIN']),
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Un nouvel admin a été créer avec succes!');
        }
        return $this->render('admin/index.html.twig', [
            'formAdmin' => $formAdmin
        ]);
    }
}