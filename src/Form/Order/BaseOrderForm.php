<?php


namespace App\Form\Order;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BaseOrderForm extends AbstractType
{
    /** @var OrderRepository $orderRepository */
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uid', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('order', EntityType::class, [
                'class' => Order::class,
            ])
            ->addEventListener(FormEvents::SUBMIT, [$this, 'submitListener']);
    }

    public function submitListener(FormEvent $event)
    {
        $data = $event->getData();
        $order = null;
        if (array_key_exists('uid', $data)) {
            $order = $this->orderRepository->findOneByUID($data['uid']);
        }
        $this->processOrderSubmitListener($event, $order);
    }

    public function processOrderSubmitListener(FormEvent $event, ?Order $order)
    {
        $data = $event->getData();
        if ($order) {
            $event->setData(array_replace_recursive($data, ['order' => $order]));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('csrf_protection', false);
    }

}