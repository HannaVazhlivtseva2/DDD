# DDD

Symfony 7.4 application running in Docker (PHP 8.3-FPM, nginx, MariaDB, Mailpit).

## Getting started

```bash
docker compose up -d --build
```

- App: http://localhost:8080
- Mailpit (mail web UI): http://localhost:8026
- MariaDB: localhost:3306 (credentials in `.env.local`)

Run migrations and create a user to log in with:

```bash
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php bin/console app:user:create you@example.com "YourPassword123"
```

Every route except `/login`, `/forgot-password` and `/reset-password` requires being logged in.
Password reset emails are sent asynchronously by the `messenger-worker` container — check Mailpit to see them.

## Architecture

The `Auth` bounded context (`src/Auth/`) implements email/password login and password reset using:

- **DDD** — `Auth/Domain/` holds the `User` aggregate, value objects (`Email`, `UserId`, `HashedPassword`), and domain events.
- **Hexagonal (Ports & Adapters)** — `Auth/Domain/Repository`, `Auth/Domain/Service` and `Auth/Application/*/Bus` are ports (interfaces); `Auth/Infrastructure/` (Doctrine, Symfony Security, Messenger, Mailer) and `Auth/UI/Http/` are adapters.
- **CQRS** — `Auth/Application/Command/*` (command bus) and `Auth/Application/Query/*` (query bus) are dispatched through dedicated Messenger buses; controllers only depend on the `CommandBus`/`QueryBus` ports.
- **Event-Driven** — command handlers publish domain events (`Auth/Domain/Event/*`) through an `event.bus`, routed to the async transport. The `messenger-worker` container consumes them; e.g. `PasswordResetRequested` triggers `SendPasswordResetEmailHandler` to email the reset link via Mailpit.

## Useful commands

```bash
docker compose exec php bin/console <command>
docker compose exec php composer <command>
docker compose logs -f php
docker compose logs -f messenger-worker
docker compose down
```
