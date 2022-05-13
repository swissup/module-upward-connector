<?php
declare(strict_types=1);

namespace Swissup\UpwardConnector\Plugin\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class UpwardPathManagerInterfacePlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Information about module is activate
     */
    const UPWARD_CONFIG_PATH_ENABLED = 'web/upward/enabled';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * if module disabled return null
     * @param \Magento\UpwardConnector\Api\UpwardPathManagerInterface $subject
     * @param string|null $result
     *
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPath(
        \Magento\UpwardConnector\Api\UpwardPathManagerInterface $subject,
        $result
    ) {
        return $this->isEnabled() ? $result : null;
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::UPWARD_CONFIG_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
