<?php


namespace App\Controller\Api;

use App\Entity\ProductType;
use App\Repository\ProductTypeRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\{Request, Response};

class MenuController extends AbstractFOSRestController
{

    /**
     * @Rest\Get("/types")
     * @param Request $request
     * @param ProductTypeRepository $productTypeRepository
     * @return Response
     */
    public function productTypeList(Request $request, ProductTypeRepository $productTypeRepository): Response
    {
        $types = $productTypeRepository->findAll();
        return $this->handleView($this->view(['types' => $types]));
    }

    /**
     * @Rest\Get("/types/{type}/products")
     * @param string $type
     * @param Request $request
     * @param ProductTypeRepository $productTypeRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function productsByType(string $type, Request $request, ProductTypeRepository $productTypeRepository): Response
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        /** @var ProductType $productType */
        $productType = $productTypeRepository->findOneByName($type);
        $products = $productType->getProducts()->slice($offset, $limit);
        $meta = [
            'total' => (int)$productType->getProducts()->count(),
            'limit' => (int)$limit,
            'offset' => (int)$offset
        ];
        return $this->handleView($this->view(['products' => $products, 'meta' => $meta]));
    }
}