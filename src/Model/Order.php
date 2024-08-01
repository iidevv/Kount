<?php

namespace Iidev\Kount\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Config;

/**
 * Order
 * 
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    public function getInquiry()
    {
        $inquiry = Database::getRepo('Iidev\Kount\Model\InquiryOrders')->findOneBy([
            'orderid' => $this->getOrderId()
        ]);

        if (!$inquiry) {
            return [];
        }

        return [$inquiry];
    }

    public function getRequestUrl()
    {
        return Config::getInstance()->Iidev->Kount->test_mode ? "https://awc.test.kount.net/workflow/detail.html?id=" : "https://awc.kount.net/workflow/detail.html?id=";
    }
}