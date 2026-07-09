<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controller;

use App\Auth\Application\Command\CommandBus;
use App\Auth\Application\Command\ResetPassword\ResetPasswordCommand;
use App\Auth\Application\Query\GetUserByValidResetToken\GetUserByValidResetTokenQuery;
use App\Auth\Application\Query\GetUserByValidResetToken\ResetTokenView;
use App\Auth\Application\Query\QueryBus;
use App\Auth\Domain\Exception\InvalidOrExpiredResetToken;
use App\Auth\UI\Http\Form\Model\ResetPasswordFormModel;
use App\Auth\UI\Http\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, string $token): Response
    {
        /** @var ResetTokenView $view */
        $view = $this->queryBus->ask(new GetUserByValidResetTokenQuery($token));

        if (!$view->valid) {
            return $this->render('auth/reset_password.html.twig', ['valid' => false, 'form' => null]);
        }

        $formModel = new ResetPasswordFormModel();
        $form = $this->createForm(ResetPasswordType::class, $formModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->dispatch(new ResetPasswordCommand($token, $formModel->plainPassword));
            } catch (InvalidOrExpiredResetToken) {
                return $this->render('auth/reset_password.html.twig', ['valid' => false, 'form' => null]);
            }

            $this->addFlash('success', 'Your password has been reset. You can now log in.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/reset_password.html.twig', [
            'valid' => true,
            'email' => $view->email,
            'form' => $form,
        ]);
    }
}
