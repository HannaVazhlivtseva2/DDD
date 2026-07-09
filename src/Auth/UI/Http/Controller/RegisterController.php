<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controller;

use App\Auth\Application\Command\CommandBus;
use App\Auth\Application\Command\RegisterUser\RegisterUserCommand;
use App\Auth\Domain\Exception\EmailAlreadyExists;
use App\Auth\Domain\Exception\WeakPassword;
use App\Auth\UI\Http\Form\Model\RegisterFormModel;
use App\Auth\UI\Http\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $formModel = new RegisterFormModel();
        $form = $this->createForm(RegisterType::class, $formModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->dispatch(new RegisterUserCommand(
                    $formModel->firstName,
                    $formModel->lastName,
                    $formModel->email,
                    $formModel->plainPassword,
                    $formModel->phone,
                    $formModel->gender->value,
                ));

                $this->addFlash('success', 'Your account has been created. You can now log in.');

                return $this->redirectToRoute('app_login');
            } catch (EmailAlreadyExists $exception) {
                $form->get('email')->addError(new FormError($exception->getMessage()));
            } catch (WeakPassword $exception) {
                $form->get('plainPassword')->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form,
        ]);
    }
}
