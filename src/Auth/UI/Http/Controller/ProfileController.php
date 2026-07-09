<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controller;

use App\Auth\Application\Command\CommandBus;
use App\Auth\Application\Command\UpdateProfile\UpdateProfileCommand;
use App\Auth\Infrastructure\Security\SecurityUser;
use App\Auth\UI\Http\Form\Model\ProfileEditFormModel;
use App\Auth\UI\Http\Form\ProfileEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ProfileController extends AbstractController
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    #[Route('/edit/user', name: 'edit_user', methods: ['GET', 'POST'])]
    public function request(Request $request, #[CurrentUser] SecurityUser $user): Response
    {
        $formModel = ProfileEditFormModel::fromUser($user->domainUser());
        $form = $this->createForm(ProfileEditType::class, $formModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new UpdateProfileCommand(
                $user->getId(),
                $formModel->firstName,
                $formModel->lastName,
                $formModel->phone,
                $formModel->gender->value,
            ));

            $this->addFlash('success', 'Your profile has been updated.');

            return $this->redirectToRoute('edit_user');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
