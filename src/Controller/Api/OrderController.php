<?php

namespace App\Controller\Api;

use App\Form\Order\{AddProductToOrderType, ConfirmOrderType, RemoveProductFromOrderType};
use App\Manager\OrderManager;
use App\Repository\OrderRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request, Response, Session\SessionInterface};

class OrderController extends AbstractFOSRestController
{

    private $orderManager;

    const SESSION_ORDER_KEY = 'order-uid';

    public function __construct(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    /**
     * @Rest\Get("/order/{uid}")
     * @param string $uid
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function index(string $uid, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneByUID($uid);
        if (!$order) {
            return $this->handleView($this->view(['error' => 'order_not_found'], Response::HTTP_NOT_FOUND));
        }
        return $this->handleView($this->view(['order' => $order], Response::HTTP_OK));
    }

    /**
     * @Rest\Get("/orderCheck")
     * @param OrderRepository $orderRepository
     * @param SessionInterface $session
     * @return Response
     */
    public function check(OrderRepository $orderRepository, SessionInterface $session): Response
    {
//        $uid = $session->get(self::SESSION_ORDER_KEY);
        $uid = '7700c7df-8f16-45f3-85ad-b6d9adb70ff3';
        if (!$uid) {
            return $this->handleView($this->view(['order' => false], Response::HTTP_OK));
        }
        $order = $orderRepository->findOneByUID($uid);
        if ($order && $order->canModifyProducts()) {
            return $this->handleView($this->view(['order' => $order], Response::HTTP_OK));
        }
        return $this->handleView($this->view(['order' => false], Response::HTTP_OK));
    }

    /**
     * @Rest\Post("/addToCart")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function add(Request $request, SessionInterface $session)
    {
        $form = $this->createForm(AddProductToOrderType::class);
        $data = $request->request->all();
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->handleInvalidForm($form);
        }

        try {
            $order = $this->orderManager->addProductToOrder($form->getData());
            $session->set(self::SESSION_ORDER_KEY, $order->getUid());
            return $this->handleSuccess($order);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @Rest\Post("/removeFromCart")
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request)
    {
        $form = $this->createForm(RemoveProductFromOrderType::class);
        $data = $request->request->all();
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->handleInvalidForm($form);
        }

        try {
            $order = $this->orderManager->removeProductFromOrder($form->getData());
            return $this->handleSuccess($order);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @Rest\Post("/confirmOrder")
     * @param Request $request
     * @return Response
     */
    public function confirm(Request $request)
    {
        $form = $this->createForm(ConfirmOrderType::class);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $this->handleInvalidForm($form);
        }
        try {
            $order = $this->orderManager->confirmOrder($form->getData());
            return $this->handleSuccess($order);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @Rest\Post("/addressCheck")
     * @param Request $request
     * @return Response
     */
    public function checkAddress(Request $request)
    {
        try {
            $address = $request->request->get('address');
            $deliveryCost = $this->orderManager->calculateDeliveryCost($address);
            return $this->handleView(
                $this->view(
                    ['deliveryCost' => $deliveryCost],
                    Response::HTTP_ACCEPTED
                )
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function handleInvalidForm(FormInterface $form)
    {
        return $this->handleView(
            $this->view(
                ['error' => 'form-validation', 'form_errors' => $form->getErrors()],
                Response::HTTP_BAD_REQUEST
            )
        );
    }

    public function handleSuccess($order)
    {
        return $this->handleView(
            $this->view(
                ['order' => $order],
                Response::HTTP_ACCEPTED
            )
        );
    }

    public function handleException(\Exception $e)
    {
        return $this->handleView(
            $this->view(
                ['error' => 'exception', 'message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            )
        );
    }
}
