# DDD

Symfony 7.4 application running in Docker (PHP 8.3-FPM, nginx, MariaDB, Mailpit).

## Getting started

```bash
docker compose up -d --build
```

- App: http://localhost:8080
- Mailpit (mail web UI): http://localhost:8026
- MariaDB: localhost:3306 (credentials in `.env.local`)
- MinIO console (S3-compatible, for local avatar storage testing): http://localhost:9001 (`minioadmin` / `minioadmin`)

Run migrations, then either register at http://localhost:8080/register or create a user from the CLI:

```bash
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php bin/console app:user:create you@example.com "YourPassw0rd!" Your Name "+15551234567" male
```

Passwords must be at least 8 characters and include an uppercase letter, a lowercase letter, a digit, and a symbol.
Gender accepts `male`, `female`, or `other`.

Every route except `/login`, `/register`, `/forgot-password` and `/reset-password` requires being logged in.
Password reset emails are sent asynchronously by the `messenger-worker` container — check Mailpit to see them.

## Avatar storage

Avatars are stored behind the `AvatarStorage` port (`Auth/Domain/Service/AvatarStorage.php`), switchable via `AVATAR_STORAGE_DRIVER` in `.env.local`:

- `local` (default) — files under `public/uploads/avatars/`, served directly by nginx.
- `s3` — uploaded to an S3-compatible bucket via `Auth/Infrastructure/Storage/S3AvatarStorage.php`. A local MinIO container is included for testing this without real AWS credentials (bucket `app-storage`, pre-created by the `minio-init` service). To try it, set in `.env.local`:
  ```
  AVATAR_STORAGE_DRIVER=s3
  AWS_S3_BUCKET=app-storage
  AWS_S3_REGION=us-east-1
  AWS_S3_ENDPOINT=http://minio:9000
  AWS_S3_PUBLIC_ENDPOINT=http://localhost:9000
  AWS_ACCESS_KEY_ID=minioadmin
  AWS_SECRET_ACCESS_KEY=minioadmin
  ```
  `AWS_S3_ENDPOINT` is what the app container uses internally; `AWS_S3_PUBLIC_ENDPOINT` is what gets baked into avatar URLs sent to the browser — they differ because `minio` (the Docker service name) isn't resolvable outside the Docker network. For real AWS S3, leave both endpoint variables blank.

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
