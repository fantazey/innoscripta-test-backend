<?php

namespace App\Form\Order;

use App\Entity\Order;
use App\Entity\User;
use App\Enum\PaymentMethodEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfirmOrderType extends BaseOrderForm
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('client', EntityType::class, [
                'class' => User::class
            ])
            ->add('clientName', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('clientPhone', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => PaymentMethodEnum::getForFormChoices(),
                'constraints' => [new NotBlank()]
            ])
            ->add('deliveryDate', DateTimeType::class, [
                'constraints' => [new NotBlank()],
                'date_format' => DateTimeType::HTML5_FORMAT,
                'widget' => 'single_text'
            ])
            ->add('deliveryAddress', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
        ;
    }

    public function processOrderSubmitListener(FormEvent $event, ?Order $order)
    {
        if (!$order) {
            $event->getForm()->addError(new FormError('order-does-not-exist'));
            return;
        }
        if ($order && !$order->canConfirm()) {
            $event->getForm()->addError(new FormError('incorrect-order-status'));
            return;
        }
        parent::processOrderSubmitListener($event, $order);
    }
}


















