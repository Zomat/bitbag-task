<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Order\AdminNoteAwareInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class UpdateOrderAdminNoteHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UpdateOrderAdminNoteCommand $command): void
    {
        $order = $this->orderRepository->find($command->orderId);

        if (!$order instanceof OrderInterface || !$order instanceof AdminNoteAwareInterface) {
            throw new InvalidArgumentException(sprintf('Order with id "%d" does not exist.', $command->orderId));
        }

        $order->setAdminNote($command->adminNote);

        $this->entityManager->flush();
    }
}
