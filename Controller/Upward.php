<?php

declare(strict_types=1);

namespace Swissup\UpwardConnector\Controller;

use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\Response;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Upward extends \Magento\Upward\Controller
{
    /**
     * @var \Magento\Upward\Context|null
     */
    private $context;

    /**
     * @var \Magento\Upward\Definition|null
     */
    private $definition;

    /**
     * @var \Magento\Upward\DefinitionIterator
     */
    private $definitionIterator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var \Swissup\UpwardConnector\Helper\Config
     */
    private $helperConfig;

    /**
     * @param Request $request
     * @param string $upwardConfig
     * @param \Swissup\UpwardConnector\Helper\Config $helperConfig
     * @param array<string>|null $additionalResolvers
     */
    public function __construct(
        Request $request,
        string $upwardConfig,
        \Swissup\UpwardConnector\Helper\Config $helperConfig,
        ?array $additionalResolvers = []
    ) {
        $this->request    = $request;
        $this->helperConfig = $helperConfig;
        $this->context    = $this->getContext();
        $this->definition = $this->getDefinition($upwardConfig);

        $this->definitionIterator = new \Magento\Upward\DefinitionIterator(
            $this->definition,
            $this->context,
            $additionalResolvers
        );
    }

    /**
     * @return \Magento\Upward\Context
     */
    public function getContext()
    {
        if ($this->context === null) {
            $request = $this->request;
            $this->context = \Magento\Upward\Context::fromRequest($request);

            if ($this->helperConfig->isEnabled()) {
                $lookupDefaults = [
                    'env.NODE_ENV' => (string) $this->helperConfig->getNodeEnv(),
                    'env.MAGENTO_BACKEND_URL' => (string) $this->helperConfig->getMagentoBackendUrl(),
                ];
                foreach ($lookupDefaults as $lookup => $value) {
                    if (!$this->context->has($lookup)) {
                        $this->context->set($lookup, $value);
                    }
                }
            }
        }

        return $this->context;
    }

    /**
     * @param $upwardConfig
     * @return \Magento\Upward\Definition
     */
    public function getDefinition($upwardConfig)
    {
        if ($this->definition === null) {
            $this->definition = \Magento\Upward\Definition::fromYamlFile($upwardConfig);

            foreach (self::STANDARD_FIELDS as $key) {
                if (!$this->definition->has($key)) {
                    throw new \RuntimeException("Definition YAML is missing required key: {$key}");
                }
            }
        }

        return $this->definition;
    }

    /**
     * Executes request and returns response.
     */
    public function __invoke(): Response
    {
        $status  = 200;
        $headers = [];
        $body = '';
        try {
            foreach (self::STANDARD_FIELDS as $key) {
                ${$key} = $this->definitionIterator->get($key);

                if (${$key} instanceof Response) {
                    return ${$key};
                }
            }
        } catch (\RuntimeException $e) {
            $status  = 500;
            $headers = [];
            $body    = json_encode(['error' => $e->getMessage()]);
        }

        $response = new Response();

        $response->setStatusCode($status);
        $response->getHeaders()->addHeaders($headers);
        $response->setContent($body);

        return $response;
    }
}
