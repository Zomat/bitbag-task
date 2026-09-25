<?php

declare(strict_types=1);

namespace App\Form\Type\Order;

use App\Entity\Order\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderAdminNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('adminNote', TextareaType::class, [
            'label' => 'app.ui.admin_note',
            'required' => false,
            'empty_data' => null,
            'attr' => [
                'rows' => 3,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'method' => 'PUT',
            'validation_groups' => ['app_order_admin_note'],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_order_admin_note';
    }
}
