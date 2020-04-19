<?php


namespace App\Manager;

use App\Enum\OrderStatusEnum;
use App\Enum\PaymentMethodEnum;
use App\Service\CalcDeliveryCostService;
use Doctrine\ORM\EntityManager;
use App\Entity\{Order, OrderProduct, Product, User};

class OrderManager
{
    /** @var EntityManager $em */
    private $em;

    /** @var CalcDeliveryCostService $deliveryCostCalculator */
    private $deliveryCostCalculator;


    public function __construct(EntityManager $em, CalcDeliveryCostService $calcDeliveryCostService)
    {
        $this->em = $em;
        $this->deliveryCostCalculator = $calcDeliveryCostService;
    }

    /**
     * @param $formData
     * @return Order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addProductToOrder($formData)
    {
        /** @var Order $order */
        $order = $formData['order'];
        /** @var Product $product */
        $product = $formData['product'];
        if (!$order) {
            $order = new Order();
            $order->setStatus(OrderStatusEnum::INITIAL);
            $order->setUid($formData['uid']);
            $order->setCreatedAt(new \DateTime());
        }
        $orderProduct = new OrderProduct();
        $orderProduct->setClientOrder($order);
        $orderProduct->setProduct($product);
        $order->addOrderProduct($orderProduct);
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    /**
     * @param $formData
     * @return Order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeProductFromOrder($formData)
    {
        /** @var Order $order */
        $order = $formData['order'];
        /** @var Product $product */
        $product = $formData['product'];
        $orderProducts = $order->getOrderProducts();
        $orderProducts = $orderProducts->filter(function(OrderProduct $orderProduct) use ($product) {
            return $orderProduct->getProduct() === $product;
        });
        if ($orderProducts->count() === 0) {
            throw new \Exception('product-not-in-order');
        }
        $order->removeOrderProduct($orderProducts->first());
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    public function confirmOrder($formData)
    {
        $address = $formData['deliveryAddress'];
        $deliveryCost = $this->calculateDeliveryCost($address);
        /** @var Order $order */
        $order = $formData['order'];
        $order->setDeliverAt($formData['deliveryDate']);
        $order->setDeliveryCost($deliveryCost);
        $order->setAddress($address);
        $order->setPaymentMethod($formData['paymentMethod']);
        $order->setCost($this->calculateOrderCost($order));

        /** @var User $client */
        $client = $formData['client'];
        if ($client) {
            $order->setClient($client);
            $order->setClientName($client->getFullName());
            $order->setClientPhone($client->getPhone());
        } else {
            $order->setClientName($formData['clientName']);
            $order->setClientPhone($formData['clientPhone']);
        }
        $order->setStatus(OrderStatusEnum::CONFIRMED);
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderPaid(Order $order)
    {
        $order->setPaid(true);
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderInProgress(Order $order)
    {
        $order->setStatus(OrderStatusEnum::IN_PROGRESS);
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrderDelivery(Order $order)
    {
        $order->setStatus(OrderStatusEnum::DELIVERY);
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function closeOrder(Order $order)
    {
        if (!$order->getPaid()) {
            throw new \Exception('can-not-close-unpaid-order');
        }
        $order->setStatus(OrderStatusEnum::CLOSED);
        $order->setClosedAt(new \DateTime());
        $this->em->persist($order);
        $this->em->flush();
    }


    /**
     * @param Order $order
     * @return float
     */
    protected function calculateOrderCost(Order $order): float
    {
        $result = array_reduce($order->getOrderProducts()->getValues(), function(float $current,OrderProduct $item) {
            return $current + $item->getProduct()->getPrice();
        }, 0);
        return (float)$result;
    }

    /**
     * @param string $address
     * @return float
     * @throws \Exception
     */
    protected function calculateDeliveryCost(string $address): float
    {
        $deliveryCost = $this->deliveryCostCalculator->calcDeliveryCost($address);
        if (!$deliveryCost) {
            throw new \Exception('can-not-calc-delivery-cost-for-address');
        }
        return $deliveryCost;
    }

}