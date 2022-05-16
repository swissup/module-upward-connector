<?php
declare(strict_types=1);

namespace Swissup\UpwardConnector\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Upward\Controller as UpwardController;
use Magento\UpwardConnector\Api\UpwardPathManagerInterface;
use Magento\UpwardConnector\Resolver\Computed;

class UpwardFactory extends \Magento\UpwardConnector\Controller\UpwardControllerFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\UpwardConnector\Api\UpwardPathManagerInterface
     */
    private $pathManager;

    /**
     * @var \Swissup\UpwardConnector\Helper\Config
     */
    private $helperConfig;

    /**
     * @var string
     */
    private $upwardControllerClassName = UpwardController::class;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param UpwardPathManagerInterface $pathManager
     * @param \Swissup\UpwardConnector\Helper\Config $helperConfig
     * @param $upwardControllerClassName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        UpwardPathManagerInterface $pathManager,
        \Swissup\UpwardConnector\Helper\Config $helperConfig,
        $upwardControllerClassName = UpwardController::class
    ) {
        $this->objectManager = $objectManager;
        $this->pathManager = $pathManager;
        $this->helperConfig = $helperConfig;
        $this->upwardControllerClassName = $upwardControllerClassName;
    }

    /**
     * Create new UPWARD PHP controller for Request
     *
     * @param RequestInterface $request
     *
     * @return UpwardController
     */
    public function create(RequestInterface $request): UpwardController
    {
        $upwardConfig = $this->pathManager->getPath();

        if (empty($upwardConfig)) {
            throw new \RuntimeException('Path to UPWARD configuration file not set.');
        }

        $helperConfig = $this->helperConfig;

        $additionalResolvers = [
            Computed::RESOLVER_TYPE => Computed::class
        ];

        return $this->objectManager->create(
            $this->upwardControllerClassName,
            compact(
                'request',
                'upwardConfig',
                'helperConfig',
                'additionalResolvers'
            )
        );
    }
}
