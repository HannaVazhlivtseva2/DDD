<?php

declare(strict_types=1);

namespace App\Auth\Domain\Model;

use App\Auth\Domain\AggregateRoot;
use App\Auth\Domain\Event\PasswordWasReset;
use App\Auth\Domain\Event\UserLoggedIn;
use App\Auth\Domain\Event\UserRegistered;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class User
{
    use AggregateRoot;

    #[ORM\Id]
    #[ORM\Column(type: 'user_id')]
    private readonly UserId $id;

    #[ORM\Column(type: 'email', unique: true)]
    private Email $email;

    #[ORM\Column(type: 'hashed_password')]
    private HashedPassword $password;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles;

    #[ORM\Column(type: 'datetime_immutable')]
    private readonly \DateTimeImmutable $createdAt;

    private function __construct(UserId $id, Email $email, HashedPassword $password, array $roles, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->createdAt = $createdAt;
    }

    public static function register(UserId $id, Email $email, HashedPassword $password): self
    {
        $user = new self($id, $email, $password, ['ROLE_USER'], new \DateTimeImmutable());
        $user->recordEvent(new UserRegistered($id->toString(), $email->toString()));

        return $user;
    }

    public function changePassword(HashedPassword $newPassword): void
    {
        $this->password = $newPassword;
        $this->recordEvent(new PasswordWasReset($this->id->toString(), $this->email->toString()));
    }

    public function recordLogin(): void
    {
        $this->recordEvent(new UserLoggedIn($this->id->toString(), $this->email->toString()));
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }

    /** @return list<string> */
    public function roles(): array
    {
        return $this->roles;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
