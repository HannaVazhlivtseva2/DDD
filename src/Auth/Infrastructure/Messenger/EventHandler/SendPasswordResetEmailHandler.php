<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Messenger\EventHandler;

use App\Auth\Domain\Event\PasswordResetRequested;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class SendPasswordResetEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire(env: 'MAILER_FROM')]
        private string $fromAddress,
    ) {
    }

    public function __invoke(PasswordResetRequested $event): void
    {
        $resetUrl = $this->urlGenerator->generate(
            'app_reset_password',
            ['token' => $event->rawToken],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $email = (new TemplatedEmail())
            ->from(new Address($this->fromAddress, 'DDD'))
            ->to($event->email)
            ->subject('Password reset request')
            ->htmlTemplate('emails/password_reset.html.twig')
            ->context([
                'resetUrl' => $resetUrl,
                'expiresAt' => $event->expiresAt,
            ]);

        $this->mailer->send($email);
    }
}
