<?php


namespace App\Enum;


class PaymentMethodEnum
{
    const
        CASH = 0, // 'cash',
        CARD_ON_LINE = 1, // 'card_on_line',
        CARD_UPON_RECEIPT = 2; //'card_upon_receipt'


    public static function getForFormChoices()
    {
        return [
            'cash' => self::CASH,
            'card_on_line' => self::CARD_ON_LINE,
            'card_upon_receipt' => self::CARD_UPON_RECEIPT
        ];
    }
}