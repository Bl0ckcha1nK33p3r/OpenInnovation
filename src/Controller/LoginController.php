<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Throwable;

class LoginController extends AbstractController
{
    private $limiterFactory;

    public function __construct(RateLimiterFactory $loginLimiter)
    {
        $this->limiterFactory = $loginLimiter;
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $limiter = $this->limiterFactory->create($lastUsername);
        if (false === $limiter->consume()->isAccepted()) {
            $error = [
                'messageKey' => 'Too many failed attempts, please try again in %count% minutes.',
                'messageData' => ['%count%' => 5]
            ];
        }

        $form = $this->createForm(LoginType::class);

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }
}