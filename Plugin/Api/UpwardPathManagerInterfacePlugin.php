<?php
declare(strict_types=1);

namespace Swissup\UpwardConnector\Plugin\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class UpwardPathManagerInterfacePlugin
{
    /**
     * @var \Swissup\UpwardConnector\Helper\Config
     */
    private $helperConfig;

    /**
     * @param \Swissup\UpwardConnector\Helper\Config $helperConfig
     */
    public function __construct(\Swissup\UpwardConnector\Helper\Config $helperConfig)
    {
        $this->helperConfig = $helperConfig;
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
        return $this->helperConfig->isEnabled() && !$this->helperConfig->isNotPwaPath();
    }
}
