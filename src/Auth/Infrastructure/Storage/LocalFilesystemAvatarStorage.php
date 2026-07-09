<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Storage;

use App\Auth\Domain\Model\UserId;
use App\Auth\Domain\Service\AvatarStorage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

final readonly class LocalFilesystemAvatarStorage implements AvatarStorage
{
    private string $uploadDir;

    public function __construct(
        private Filesystem $filesystem,
        #[Autowire('%kernel.project_dir%')]
        string $projectDir,
    ) {
        $this->uploadDir = $projectDir.'/public/uploads/avatars';
    }

    public function store(UserId $userId, string $content, string $extension): string
    {
        $extension = preg_replace('/[^a-z0-9]/', '', strtolower($extension));

        if ('' === $extension) {
            throw new \InvalidArgumentException('Invalid avatar file extension.');
        }

        $this->filesystem->mkdir($this->uploadDir);

        foreach (glob($this->uploadDir.'/'.$userId->toString().'.*') ?: [] as $existingFile) {
            $this->filesystem->remove($existingFile);
        }

        $filename = $userId->toString().'.'.$extension;
        $this->filesystem->dumpFile($this->uploadDir.'/'.$filename, $content);

        return $filename;
    }

    public function delete(string $filename): void
    {
        $this->filesystem->remove($this->uploadDir.'/'.basename($filename));
    }
}
