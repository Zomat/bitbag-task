<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Type\Order;

use App\Entity\Order\Order;
use App\Form\Type\Order\OrderAdminNoteType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

final class OrderAdminNoteTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->addYamlMapping(dirname(__DIR__, 5) . '/config/validator/Order.yaml')
            ->getValidator();

        return [
            ...parent::getExtensions(),
            new ValidatorExtension($validator),
        ];
    }

    public function testItAcceptsAValidNote(): void
    {
        $order = new Order();
        $form = $this->factory->create(OrderAdminNoteType::class, $order);

        $form->submit(['adminNote' => 'A valid note.']);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid());
        self::assertSame('A valid note.', $order->getAdminNote());
    }

    public function testItRejectsANoteLongerThan500Characters(): void
    {
        $order = new Order();
        $form = $this->factory->create(OrderAdminNoteType::class, $order);

        $form->submit(['adminNote' => str_repeat('a', 501)]);

        self::assertFalse($form->isValid());

        $errors = $form->get('adminNote')->getErrors();
        self::assertCount(1, $errors);
        self::assertSame(
            'sylius.order.admin_note.max_length',
            $errors[0]->getMessageTemplate(),
        );
    }

    public function testItAcceptsANoteOfExactly500Characters(): void
    {
        $order = new Order();
        $form = $this->factory->create(OrderAdminNoteType::class, $order);

        $form->submit(['adminNote' => str_repeat('a', 500)]);

        self::assertTrue($form->isValid());
    }

    public function testSubmittingAnEmptyNoteRemovesTheExistingOne(): void
    {
        $order = new Order();
        $order->setAdminNote('previous note');

        $form = $this->factory->create(OrderAdminNoteType::class, $order);
        $form->submit(['adminNote' => '']);

        self::assertTrue($form->isValid());
        self::assertNull($order->getAdminNote());
    }
}
