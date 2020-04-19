<?php


namespace App\Form\Order;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BaseProductOrderType extends BaseOrderForm
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'constraints' => [new NotBlank()]
            ]);
    }
}