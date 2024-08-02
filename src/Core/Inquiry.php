<?php

namespace Iidev\Kount\Core;

use XLite\InjectLoggerTrait;
use XLite\Core\Config;
use Iidev\Kount\Model\InquiryOrders;
use XLite\Core\Database;

class Inquiry
{
    use InjectLoggerTrait;

    public function doRequest($order, $sessionID, $ipAddress)
    {
        $transactionId = $order->getPaymentTransactions()?->last()?->getTransactionId();

        if (!$transactionId)
            return;

        $isInquiryExist = Database::getRepo('Iidev\Kount\Model\InquiryOrders')->findOneBy([
            'orderid' => $order->getOrderId(),
        ]);

        if ($isInquiryExist)
            return;

        $transaction = Database::getRepo('Iidev\CloverPayments\Model\Payment\XpcTransactionData')->findOneBy([
            'transaction' => $transactionId,
        ]);

        if (!$transaction)
            return;

        try {
            $profile = $order->getProfile();
            $name = $profile->getBillingAddress()->getFirstname() . " " . $profile->getBillingAddress()->getLastname();

            $billingAddress = $profile->getBillingAddress();
            $shippingAddress = $profile->getShippingAddress();

            $cardNumber = $transaction->getCardNumber();

            $settings = $this->getSettings();
            $inquiry = new \Kount_Ris_Request_Inquiry($settings);

            $inquiry->setBillingAddress(
                $billingAddress->getStreet(),
                "",
                $billingAddress->getCity(),
                $billingAddress->getState()->getCode(),
                $billingAddress->getZipcode(),
                $billingAddress->getCountry()->getCode(),
            );
            $inquiry->setShippingAddress(
                $shippingAddress->getStreet(),
                "",
                $shippingAddress->getCity(),
                $shippingAddress->getState()->getCode(),
                $shippingAddress->getZipcode(),
                $shippingAddress->getCountry()->getCode(),
            );

            $inquiry->setName($name);
            $inquiry->setUnique($profile->getProfileId());
            $inquiry->setOrderNumber($order->getOrderNumber());
            $inquiry->setBillingPhoneNumber($profile->getBillingAddress()->getPhone());
            $inquiry->setShippingPhoneNumber($profile->getShippingAddress()->getPhone());
            $inquiry->setCurrency($order->getCurrency()->getCode());
            $inquiry->setSessionId($sessionID);
            $inquiry->setPayment('CARD', $cardNumber);
            $inquiry->setTotal($this->getValidNumber($order->getTotal()));
            $inquiry->setEmail($profile->getLogin());
            $inquiry->setIpAddress($ipAddress);
            $inquiry->setMack('Y');
            $inquiry->setWebsite("DEFAULT");
            $inquiry->setCart($this->getCartItems($order->getItems()));
            $inquiry->setAuth('A');

            // CVV matchs by default for post transaction check
            $inquiry->setCvvr('M');

            $transactionData = $this->getTransactionDetails($transactionId);

            if (isset($transactionData['zip-check'])) {
                $inquiry->setAvsz(($transactionData['zip-check'] === 'pass') ? 'M' : 'N');
            }
            if (isset($transactionData['address-check'])) {
                $inquiry->setAvst(($transactionData['address-check'] === 'pass') ? 'M' : 'N');
            }

            $cardZipcode = '';

            if (isset($transactionData['card-zip'])) {
                $cardZipcode = $transactionData['card-zip'];
            }

            $response = $inquiry->getResponse();

            if ($response->getMode() === 'E') {
                $this->getLogger('Kount')->error('Error response: ' . __FUNCTION__ . $response);
            } else {
                $this->saveInquiryOrder($order->getOrderId(), $response, $cardZipcode);
            }

        } catch (\Exception $e) {
            $this->getLogger('Kount')->error('Error Response: ' . $e->getMessage());
        }
    }

    private function getSettings()
    {
        $settings = new \Kount_Ris_ArraySettings([
            'MERCHANT_ID' => Config::getInstance()->Iidev->Kount->client_id,
            'API_KEY' => Config::getInstance()->Iidev->Kount->api_key,
            'URL' => $this->getRequestUrl(),
        ]);

        return $settings;
    }

    private function getTransactionDetails($transactionId)
    {
        $transactionDataCollection = Database::getRepo('XLite\Model\Payment\TransactionData')->findBy([
            'transaction' => $transactionId,
        ]);

        if (!$transactionDataCollection)
            return [];

        $data = [];

        foreach ($transactionDataCollection as $transactionData) {
            switch ($transactionData->getName()) {
                case 'card-zip':
                    $data['card-zip'] = $transactionData->getValue();
                    break;
                case 'zip-check':
                    $data['zip-check'] = $transactionData->getValue();
                    break;
                case 'address-check':
                    $data['address-check'] = $transactionData->getValue();
                    break;
            }
        }

        return $data;
    }


    private function getRequestUrl()
    {
        return Config::getInstance()->Iidev->Kount->test_mode ? "https://risk.test.kount.net" : "https://risk.kount.net";
    }

    private function getCartItems($items)
    {
        $cart = [];

        foreach ($items as $item) {
            $product = $item->getProduct();

            $name = $this->getValidLengthString($item->getName(), 255);
            $description = $item->getSku();
            $price = $this->getValidNumber($product->getPrice());

            $cart[] = new \Kount_Ris_Data_CartItem($this->getCategory($product), $name, $description, $item->getAmount(), $price);
        }

        return $cart;
    }

    private function getValidLengthString($string, $maxLength)
    {
        if (strlen($string) > $maxLength) {
            return substr($string, 0, $maxLength);
        }
        return $string;
    }

    private function getValidNumber($value)
    {
        return intval(floatval($value) * 100);
    }

    private function getCategory($product)
    {
        $categories = $product->getCategories();

        $lastCategory = reset($categories);

        return $lastCategory->getName();
    }

    private function saveInquiryOrder($orderid, $data, $zipcode)
    {
        $inquiry = new InquiryOrders();

        $inquiry->setOrderid($orderid);
        $inquiry->setTransactionId($data->getTransactionId());
        $inquiry->setWarnings(implode(', ', $data->getWarnings()));
        $inquiry->setScore($data->getScore());
        $inquiry->setOmniscore($data->getOmniscore());
        $inquiry->setAuto($data->getAuto());
        $inquiry->setIpAddress($data->getIPAddress());
        $inquiry->setUserAgent($data->getUserAgentString());
        $inquiry->setZipcode($zipcode);

        Database::getEM()->persist($inquiry);
        Database::getEM()->flush();
    }
}
