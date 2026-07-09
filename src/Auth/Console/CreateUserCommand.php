<?php

declare(strict_types=1);

namespace App\Auth\Console;

use App\Auth\Application\Command\CommandBus;
use App\Auth\Application\Command\RegisterUser\RegisterUserCommand;
use App\Auth\Domain\Exception\EmailAlreadyExists;
use App\Auth\Domain\Exception\WeakPassword;
use App\Auth\Domain\Model\Gender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:user:create', description: 'Create a user that can log in.')]
final class CreateUserCommand extends Command
{
    public function __construct(private readonly CommandBus $commandBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain text password')
            ->addArgument('firstName', InputArgument::REQUIRED, 'First name')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Last name')
            ->addArgument('phone', InputArgument::REQUIRED, 'Phone number')
            ->addArgument('gender', InputArgument::REQUIRED, \sprintf(
                'Gender (%s)',
                implode('|', array_map(static fn (Gender $g): string => $g->value, Gender::cases())),
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $userId = $this->commandBus->dispatch(new RegisterUserCommand(
                $input->getArgument('firstName'),
                $input->getArgument('lastName'),
                $input->getArgument('email'),
                $input->getArgument('password'),
                $input->getArgument('phone'),
                $input->getArgument('gender'),
            ));
        } catch (EmailAlreadyExists|WeakPassword $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success(\sprintf('User created with id %s', $userId));

        return Command::SUCCESS;
    }
}
