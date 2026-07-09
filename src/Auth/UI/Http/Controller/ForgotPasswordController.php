<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controller;

use App\Auth\Application\Command\CommandBus;
use App\Auth\Application\Command\RequestPasswordReset\RequestPasswordResetCommand;
use App\Auth\UI\Http\Form\Model\RequestPasswordResetFormModel;
use App\Auth\UI\Http\Form\RequestPasswordResetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ForgotPasswordController extends AbstractController
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        $formModel = new RequestPasswordResetFormModel();
        $form = $this->createForm(RequestPasswordResetType::class, $formModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new RequestPasswordResetCommand($formModel->email));

            return $this->redirectToRoute('app_forgot_password_sent');
        }

        return $this->render('auth/forgot_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/forgot-password/sent', name: 'app_forgot_password_sent', methods: ['GET'])]
    public function sent(): Response
    {
        return $this->render('auth/forgot_password_sent.html.twig');
    }
}
