<?php

namespace Aztech\Phinject\Validation;

use Aztech\Phinject\Config;
use Aztech\Phinject\Util\ArrayResolver;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Validator
{

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var Config
     */
    private $config;

    /**
     *
     * @var ConfigurationValidator[]
     */
    private $validators = array();

    private $errors = array();

    private $callbacks = array();

    public function __construct(Config $config, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger ?  : new NullLogger();
    }

    public function add(ConfigurationValidator $validator)
    {
        $this->validators[] = $validator;
    }

    /**
     *
     * @param string $error
     */
    public function addError($error)
    {
        $this->logger->error(' - ' . $error);
    }

    /**
     *
     * @param string $warning
     */
    public function addWarning($warning)
    {
        $this->logger->warning(' - ' . $warning);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *
     * @param \Closure $callback
     */
    public function onNodeInspected($callback)
    {
        $this->callbacks[] = $callback;
    }

    private function triggerNodeInspected($name, ArrayResolver $node)
    {
        foreach ($this->callbacks as $callback) {
            $callback($name, $node);
        }
    }

    public function validate()
    {
        $resolver = new ArrayResolver($this->config->load());
        $services = $resolver->resolve('classes', null);

        foreach ($services as $serviceName => $serviceConfig) {
            $this->logger->info('Validating service configuration : ' . $serviceName);

            foreach ($this->validators as $validator) {
                $this->logger->debug('Running : ' . get_class($validator));
                $validator->validateService($this, $resolver, $serviceName, $serviceConfig);
            }

            $this->triggerNodeInspected($serviceName, $serviceConfig);
        }
    }
}
