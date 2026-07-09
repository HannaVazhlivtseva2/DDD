<?php

declare(strict_types=1);

namespace App\Auth\Domain\Model;

use App\Auth\Domain\AggregateRoot;
use App\Auth\Domain\Event\PasswordWasReset;
use App\Auth\Domain\Event\ProfileWasUpdated;
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

    #[ORM\Column(type: 'string', length: 100)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 100)]
    private string $lastName;

    #[ORM\Column(type: 'email', unique: true)]
    private Email $email;

    #[ORM\Column(type: 'hashed_password')]
    private HashedPassword $password;

    #[ORM\Column(type: 'phone_number')]
    private PhoneNumber $phone;

    #[ORM\Column(type: 'string', length: 10, enumType: Gender::class)]
    private Gender $gender;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles;

    #[ORM\Column(type: 'datetime_immutable')]
    private readonly \DateTimeImmutable $createdAt;

    private function __construct(
        UserId $id,
        string $firstName,
        string $lastName,
        Email $email,
        HashedPassword $password,
        PhoneNumber $phone,
        Gender $gender,
        array $roles,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->firstName = self::requireNonBlank($firstName, 'First name');
        $this->lastName = self::requireNonBlank($lastName, 'Last name');
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->gender = $gender;
        $this->roles = $roles;
        $this->createdAt = $createdAt;
    }

    public static function register(
        UserId $id,
        string $firstName,
        string $lastName,
        Email $email,
        HashedPassword $password,
        PhoneNumber $phone,
        Gender $gender,
    ): self {
        $user = new self($id, $firstName, $lastName, $email, $password, $phone, $gender, ['ROLE_USER'], new \DateTimeImmutable());
        $user->recordEvent(new UserRegistered($id->toString(), $email->toString()));

        return $user;
    }

    public function changePassword(HashedPassword $newPassword): void
    {
        $this->password = $newPassword;
        $this->recordEvent(new PasswordWasReset($this->id->toString(), $this->email->toString()));
    }

    public function updateProfile(string $firstName, string $lastName, PhoneNumber $phone, Gender $gender): void
    {
        $this->firstName = self::requireNonBlank($firstName, 'First name');
        $this->lastName = self::requireNonBlank($lastName, 'Last name');
        $this->phone = $phone;
        $this->gender = $gender;
        $this->recordEvent(new ProfileWasUpdated($this->id->toString()));
    }

    public function recordLogin(): void
    {
        $this->recordEvent(new UserLoggedIn($this->id->toString(), $this->email->toString()));
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }

    public function phone(): PhoneNumber
    {
        return $this->phone;
    }

    public function gender(): Gender
    {
        return $this->gender;
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

    private static function requireNonBlank(string $value, string $fieldName): string
    {
        $trimmed = trim($value);

        if ('' === $trimmed) {
            throw new \InvalidArgumentException(\sprintf('%s cannot be blank.', $fieldName));
        }

        return $trimmed;
    }
}
