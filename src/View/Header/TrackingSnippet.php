<?php

namespace Iidev\Kount\View\Header;

use XLite\View\AView;
use XLite\Core\Config;
use XCart\Extender\Mapping\ListChild;
use \XLite\Core\Auth;

/**
 * @ListChild (list="head", zone="customer")
 */
class TrackingSnippet extends AView
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Iidev/Kount/kount-web-client-sdk-bundle.js';

        return $list;
    }

    public function getLogin()
    {
        $profile = Auth::getInstance()->getProfile();

        return $profile ? $profile->getLogin() : null;
    }
    public function getClientID()
    {
        return Config::getInstance()->Iidev->Kount->client_id;
    }

    public function getSessionID()
    {
        return \XLite\Core\Session::getInstance()->getSessionId();
    }

    public function isTestMode()
    {
        return Config::getInstance()->Iidev->Kount->test_mode ? "TEST" : "PROD";
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/Kount/header/tracking_snippet.twig';
    }
}
