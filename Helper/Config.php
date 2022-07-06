<?php
namespace Swissup\UpwardConnector\Helper;

use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_XML_PATH_ENABLE = 'web/upward/enabled';
    const CONFIG_XML_PATH_NODE_ENV = 'web/upward/NODE_ENV';
    const CONFIG_XML_PATH_MAGENTO_BACKEND_URL = 'web/upward/MAGENTO_BACKEND_URL';

    /**
     * @param  string $key
     * @param  null|int|string $scopeCode
     * @return mixed
     */
    private function getConfig($key, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    /**
     * @param  string $key
     * @param null|int|string $scopeCode
     * @return boolean
     */
    private function isSetFlag($key, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($key, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    /**
     *
     * @param  int  $store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return $this->isSetFlag(self::CONFIG_XML_PATH_ENABLE, $store);
    }

    /**
     * @return bool
     */
    public function isNotPwaPath()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->_getRequest();
        $path = $request->getPathinfo();
        $pathesToSkip = [
            '/stores/store/switch/'
        ];
        return in_array($path, $pathesToSkip);
    }

    /**
     * @param null|int|string $store
     * @return string
     */
    public function getNodeEnv($store = null)
    {
        return (string) $this->getConfig(self::CONFIG_XML_PATH_NODE_ENV, $store);
    }

    /**
     * @param null|int|string $store
     * @return string
     */
    public function getMagentoBackendUrl($store = null)
    {
        return (string) $this->getConfig(self::CONFIG_XML_PATH_MAGENTO_BACKEND_URL, $store);
    }
}
