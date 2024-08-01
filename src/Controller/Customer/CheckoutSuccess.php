<?php

namespace Iidev\Kount\Controller\Customer;

use Iidev\Kount\Core\Inquiry;
use XCart\Extender\Mapping\Extender;

/**
 * Checkout success controller
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    protected function doNoAction()
    {
        parent::doNoAction();

        if (
            !\XLite\Core\Request::getInstance()->isAJAX()
            && in_array($this->getTarget(), ['checkout_success', 'checkoutSuccess'])
            && $this->getOrder()
        ) {
            $inquiry = new Inquiry;
            $ipAddress = \XLite\Core\Request::getInstance()->getClientIp();

            $inquiry->doRequest($this->getOrder(), \XLite\Core\Session::getInstance()->getSessionId(), $ipAddress);
        }
    }
}
