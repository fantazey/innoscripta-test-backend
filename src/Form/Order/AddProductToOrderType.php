<?php


namespace App\Form\Order;

use App\Entity\Order;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;

class AddProductToOrderType extends BaseProductOrderType
{
    public function processOrderSubmitListener(FormEvent $event, ?Order $order)
    {
        if ($order && !$order->canModifyProducts()) {
            $event->getForm()->addError(new FormError('incorrect-order-status'));
            return;
        }
        parent::processOrderSubmitListener($event, $order);
    }
}