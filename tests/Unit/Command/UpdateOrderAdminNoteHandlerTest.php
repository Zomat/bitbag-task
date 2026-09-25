<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\UpdateOrderAdminNoteCommand;
use App\Command\UpdateOrderAdminNoteHandler;
use App\Entity\Order\Order;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class UpdateOrderAdminNoteHandlerTest extends TestCase
{
    public function testItUpdatesTheAdminNoteAndFlushesWhenOrderExists(): void
    {
        $order = new Order();
        $order->setAdminNote('old note');

        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->method('find')->with(42)->willReturn($order);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('flush');

        $handler = new UpdateOrderAdminNoteHandler($orderRepository, $entityManager);
        $handler(new UpdateOrderAdminNoteCommand(42, 'new note'));

        self::assertSame('new note', $order->getAdminNote());
    }

    public function testItRemovesTheAdminNoteWhenCommandCarriesNull(): void
    {
        $order = new Order();
        $order->setAdminNote('existing note');

        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->method('find')->with(7)->willReturn($order);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('flush');

        $handler = new UpdateOrderAdminNoteHandler($orderRepository, $entityManager);
        $handler(new UpdateOrderAdminNoteCommand(7, null));

        self::assertNull($order->getAdminNote());
    }

    public function testItThrowsWhenOrderDoesNotExist(): void
    {
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->method('find')->with(999)->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');

        $handler = new UpdateOrderAdminNoteHandler($orderRepository, $entityManager);

        $this->expectException(InvalidArgumentException::class);

        $handler(new UpdateOrderAdminNoteCommand(999, 'note'));
    }
}
