<?php


namespace App\Service;


class CalcDeliveryCostService
{
    /**
     * @param string $address
     * @return float|null
     * @throws \Exception
     */
    public function calcDeliveryCost(string $address): ?float
    {
        $result = random_int(5, 15);
        if ($result % 2) {
            return null; //can not calc delivery cost for this address
        }
        return (float)$result * 0.15;
    }
}