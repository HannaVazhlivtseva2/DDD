# DDD

Symfony 7.4 application running in Docker (PHP 8.3-FPM, nginx, MariaDB, Mailpit).

## Getting started

```bash
docker compose up -d --build
```

- App: http://localhost:8080
- Mailpit (mail web UI): http://localhost:8026
- MariaDB: localhost:3306 (credentials in `.env.local`)

## Useful commands

```bash
docker compose exec php bin/console <command>
docker compose exec php composer <command>
docker compose logs -f php
docker compose down
```
