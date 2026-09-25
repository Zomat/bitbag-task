<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Order;

use App\Entity\Order\Order;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    private Order $order;

    protected function setUp(): void
    {
        $this->order = new Order();
    }

    public function testItHasNoAdminNoteByDefault(): void
    {
        self::assertNull($this->order->getAdminNote());
        self::assertFalse($this->order->hasAdminNote());
    }

    public function testItStoresAnAdminNote(): void
    {
        $this->order->setAdminNote('Customer called about a delayed shipment.');

        self::assertSame('Customer called about a delayed shipment.', $this->order->getAdminNote());
        self::assertTrue($this->order->hasAdminNote());
    }

    public function testItTrimsWhitespaceAroundTheNote(): void
    {
        $this->order->setAdminNote('  padded note  ');

        self::assertSame('padded note', $this->order->getAdminNote());
    }

    public function testSettingNullRemovesTheNote(): void
    {
        $this->order->setAdminNote('some note');
        $this->order->setAdminNote(null);

        self::assertNull($this->order->getAdminNote());
        self::assertFalse($this->order->hasAdminNote());
    }

    public function testSettingAnEmptyStringRemovesTheNote(): void
    {
        $this->order->setAdminNote('some note');
        $this->order->setAdminNote('');

        self::assertNull($this->order->getAdminNote());
        self::assertFalse($this->order->hasAdminNote());
    }

    public function testSettingOnlyWhitespaceRemovesTheNote(): void
    {
        $this->order->setAdminNote('some note');
        $this->order->setAdminNote('   ');

        self::assertNull($this->order->getAdminNote());
        self::assertFalse($this->order->hasAdminNote());
    }
}
