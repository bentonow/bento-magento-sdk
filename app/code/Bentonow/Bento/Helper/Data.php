<?php
namespace Bentonow\Bento\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_USERNAME = 'bentonow_bento/general/username';
    const XML_PATH_PASSWORD = 'bentonow_bento/general/password';
    const XML_PATH_SITE_UUID = 'bentonow_bento/general/site_uuid';

    public function getUsername($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_USERNAME, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPassword($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PASSWORD, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSiteUuid($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SITE_UUID, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getBaseUrl()
    {
        return 'https://app.bentonow.com/api/v1/';
    }

    public function getUserAgent()
    {
        return 'bento-magento-' . $this->getSiteUuid();
    }
}