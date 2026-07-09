<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Controller;

use App\Auth\Application\Command\CommandBus;
use App\Auth\Application\Command\UpdateAvatar\UpdateAvatarCommand;
use App\Auth\Application\Command\UpdateProfile\UpdateProfileCommand;
use App\Auth\Domain\Service\AvatarStorage;
use App\Auth\Infrastructure\Security\SecurityUser;
use App\Auth\UI\Http\Form\AvatarUploadType;
use App\Auth\UI\Http\Form\Model\AvatarUploadFormModel;
use App\Auth\UI\Http\Form\Model\ProfileEditFormModel;
use App\Auth\UI\Http\Form\ProfileEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ProfileController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly AvatarStorage $avatarStorage,
    ) {
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

        $avatarFilename = $user->domainUser()->avatarFilename();
        $avatarUrl = null;

        if (null !== $avatarFilename) {
            if ($this->avatarStorage->exists($avatarFilename)) {
                $avatarUrl = $this->avatarStorage->publicUrl($avatarFilename);
            } else {
                $this->addFlash('error', 'Your avatar file is missing from storage — please upload it again.');
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form,
            'avatarForm' => $this->createForm(AvatarUploadType::class, new AvatarUploadFormModel()),
            'avatarUrl' => $avatarUrl,
            'user' => $user,
        ]);
    }

    #[Route('/edit/user/avatar', name: 'edit_user_avatar', methods: ['POST'])]
    public function uploadAvatar(Request $request, #[CurrentUser] SecurityUser $user): Response
    {
        $formModel = new AvatarUploadFormModel();
        $form = $this->createForm(AvatarUploadType::class, $formModel);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('edit_user');
        }

        $filename = $this->avatarStorage->store(
            $user->domainUser()->id(),
            file_get_contents($formModel->avatarFile->getPathname()),
            $formModel->avatarFile->guessExtension() ?? 'bin',
        );

        $this->commandBus->dispatch(new UpdateAvatarCommand($user->getId(), $filename));

        $this->addFlash('success', 'Avatar updated.');

        return $this->redirectToRoute('edit_user');
    }
}
