<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Command\UpdateOrderAdminNoteCommand;
use App\Command\UpdateOrderAdminNoteHandler;
use App\Entity\Order\AdminNoteAwareInterface;
use App\Form\Type\Order\OrderAdminNoteType;
use App\Twig\OrderAdminNoteFormExtension;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class OrderAdminNoteController extends AbstractController
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UpdateOrderAdminNoteHandler $updateOrderAdminNote,
    ) {
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $order = $this->findOrderOrFail($id);

        $form = $this->createForm(OrderAdminNoteType::class, $order);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->redirectToOrder($order);
        }

        if (!$form->isValid()) {
            $this->flashFormErrors($form, $order);

            return $this->redirectToOrder($order);
        }

        ($this->updateOrderAdminNote)(new UpdateOrderAdminNoteCommand($order->getId(), $order->getAdminNote()));

        $this->addFlash('success', 'app.ui.admin_note_updated');

        return $this->redirectToOrder($order);
    }

    public function remove(Request $request, int $id): RedirectResponse
    {
        $order = $this->findOrderOrFail($id);

        if (!$this->isCsrfTokenValid('app_admin_order_remove_admin_note', $request->request->getString('_csrf_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        ($this->updateOrderAdminNote)(new UpdateOrderAdminNoteCommand($order->getId(), null));

        $this->addFlash('success', 'app.ui.admin_note_removed');

        return $this->redirectToOrder($order);
    }

    private function flashFormErrors(FormInterface $form, OrderInterface&AdminNoteAwareInterface $order): void
    {
        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('error', $error->getMessage());
        }

        $this->addFlash(
            OrderAdminNoteFormExtension::invalidSubmissionFlashKey($order->getId()),
            (string) $form->get('adminNote')->getData(),
        );
    }

    private function findOrderOrFail(int $id): OrderInterface&AdminNoteAwareInterface
    {
        $order = $this->orderRepository->find($id);

        if (!$order instanceof OrderInterface || !$order instanceof AdminNoteAwareInterface) {
            throw $this->createNotFoundException();
        }

        return $order;
    }

    private function redirectToOrder(OrderInterface $order): RedirectResponse
    {
        return $this->redirectToRoute('sylius_admin_order_show', ['id' => $order->getId()]);
    }
}
