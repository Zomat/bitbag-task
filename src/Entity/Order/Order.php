<?php

declare(strict_types=1);

namespace App\Entity\Order;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Order as BaseOrder;
use Sylius\MolliePlugin\Entity\AbandonedEmailOrderTrait;
use Sylius\MolliePlugin\Entity\MolliePaymentIdOrderTrait;
use Sylius\MolliePlugin\Entity\OrderInterface;
use Sylius\MolliePlugin\Entity\QRCodeOrderTrait;
use Sylius\MolliePlugin\Entity\RecurringOrderTrait;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends BaseOrder implements OrderInterface, AdminNoteAwareInterface
{
    use MolliePaymentIdOrderTrait;
    use QRCodeOrderTrait;
    use RecurringOrderTrait;
    use AbandonedEmailOrderTrait;

    #[ORM\Column(name: 'admin_note', type: Types::TEXT, nullable: true)]
    private ?string $adminNote = null;

    public function getAdminNote(): ?string
    {
        return $this->adminNote;
    }

    public function setAdminNote(?string $adminNote): void
    {
        $normalizedNote = null !== $adminNote ? trim($adminNote) : null;

        $this->adminNote = '' === $normalizedNote ? null : $normalizedNote;
    }

    public function hasAdminNote(): bool
    {
        return null !== $this->adminNote;
    }
}
