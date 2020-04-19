<?php


namespace App\Form\Order;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoveProductFromOrderType extends BaseProductOrderType
{
    public function processOrderSubmitListener(FormEvent $event, ?Order $order)
    {
        if (!$order) {
            $event->getForm()->addError(new FormError('order-does-not-exist'));
            return;
        }
        if ($order && !$order->canModifyProducts()) {
            $event->getForm()->addError(new FormError('incorrect-order-status'));
            return;
        }
        parent::processOrderSubmitListener($event, $order);
    }
}