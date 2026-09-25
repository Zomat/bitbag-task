<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Order\AdminNoteAwareInterface;
use App\Form\Type\Order\OrderAdminNoteType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderAdminNoteFormExtension extends AbstractExtension
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack,
    ) {
    }

    public static function invalidSubmissionFlashKey(int $orderId): string
    {
        return sprintf('admin_note_invalid_submission_%d', $orderId);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_order_admin_note_form', $this->createFormView(...)),
        ];
    }

    private function createFormView(OrderInterface&AdminNoteAwareInterface $order): FormView
    {
        $form = $this->formFactory->create(OrderAdminNoteType::class, $order);

        $invalidSubmittedValue = $this->getInvalidSubmittedValue($order);

        if (null !== $invalidSubmittedValue) {
            $form->submit(['adminNote' => $invalidSubmittedValue]);
        }

        return $form->createView();
    }

    private function getInvalidSubmittedValue(OrderInterface $order): ?string
    {
        $session = $this->requestStack->getSession();

        if (!$session instanceof FlashBagAwareSessionInterface) {
            return null;
        }

        $flashBag = $session->getFlashBag();
        $key = self::invalidSubmissionFlashKey($order->getId());

        if (!$flashBag->has($key)) {
            return null;
        }

        return $flashBag->get($key)[0] ?? null;
    }
}
