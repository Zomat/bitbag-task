<?php

declare(strict_types=1);

namespace App\Command;

final class UpdateOrderAdminNoteCommand
{
    public function __construct(
        public readonly int $orderId,
        public readonly ?string $adminNote,
    ) {
    }
}
