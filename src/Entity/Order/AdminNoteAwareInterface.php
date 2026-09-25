<?php

declare(strict_types=1);

namespace App\Entity\Order;

interface AdminNoteAwareInterface
{
    public const MAX_ADMIN_NOTE_LENGTH = 500;

    public function getAdminNote(): ?string;

    public function setAdminNote(?string $adminNote): void;

    public function hasAdminNote(): bool;
}