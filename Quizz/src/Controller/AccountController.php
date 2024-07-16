<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'AccountPage')]
    public function AccountPage(UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $username = $user->getUsername();
        $email = $user->getEmail();

        return $this->render('account/account.html.twig', [
            'username' => $username,
            'email' => $email,
        ]);
    }

    #[Route('/change-account', name: 'ChangeAccount')]
    public function ChangeAccount(): Response
    {
        return $this->render('account/change-account.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}
