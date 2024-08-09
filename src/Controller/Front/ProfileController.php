<?php

namespace App\Controller\Front;

use App\Entity\Orders;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile', name: 'front_profile_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('front/profile/index.html.twig');
    }


    #[Route('/details/{id}', name: 'details')]
    public function details(Orders $order): Response
    {
        $user = $this->getUser();

        if (!$user || $order->getCustomer() !== $user) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette facture.');
        }

        return $this->render('front/profile/details.html.twig', [
            'order' => $order,
        ]);
    }
}