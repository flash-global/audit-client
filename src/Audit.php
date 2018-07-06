<?php

namespace Fei\Service\Audit\Client;

use Fei\ApiClient\AbstractApiClient;
use Fei\ApiClient\ApiRequestOption;
use Fei\ApiClient\RequestDescriptor;
use Fei\Service\Audit\Client\Builder\SearchBuilder;
use Fei\Service\Audit\Client\Exception\AuditException;
use Fei\Service\Audit\Entity\AuditEvent;
use Fei\Service\Audit\Validator\AuditEventValidator;

class Audit extends AbstractApiClient implements AuditInterface
{
    const OPTION_BASEURL = 'baseUrl';
    const OPTION_FILTER = 'filterLevel';
    const OPTION_BACKTRACE = 'includeBacktrace';
    const OPTION_LOGFILE = 'exceptionLogFile';
    const OPTION_ENABLED = 'enabled';

    /**
     * @var int
     */
    protected $filterLevel = AuditEvent::LVL_ERROR;

    /**
     * @var string
     */
    protected $exceptionLogFile = '/tmp/logger.log';

    /**
     * @var bool
     */
    protected $includeBacktrace = true;

    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var mixed
     */
    protected $previousErrorHandler;

    /**
     * @param string|AuditEvent $message
     * @param array               $params
     *
     * @return bool|\Fei\ApiClient\ResponseDescriptor
     */
    public function notify($message, array $params = array())
    {
        try {
            if (!$this->getEnabled()) {
                return true;
            }
            
            $this->registerErrorHandler();

            if (is_string($message)) {
                $auditEvent = new AuditEvent();
                $auditEvent->setMessage($message)
                    ->setLevel(AuditEvent::LVL_INFO)
                    ->setCategory(AuditEvent::BUSINESS);
            } else {
                $auditEvent = $message;
            }

            $this->prepareAuditEvent($auditEvent, $params);

            if ($auditEvent->getLevel() < $this->filterLevel) {
                $this->restoreErrorHandler();
                return false;
            }

            if ($this->includeBacktrace) {
                $auditEvent->setBackTrace($this->getBackTrace());
            }

            $validator = new AuditEventValidator();
            $validator->validate($auditEvent);

            $errors = $validator->getErrors();
            if (!empty($errors)) {
                throw new \LogicException(
                    sprintf('AuditEvent validator errors: %s', $validator->getErrorsAsString())
                );
            }

            $arrayData = $auditEvent->toArray();
            $arrayData = $this->formatContexts($arrayData);

            $serialized = @json_encode($arrayData);
            if (is_null($serialized)) {
                $this->restoreErrorHandler();
                return false;
            }

            $request = new RequestDescriptor();
            $request->addBodyParam('auditEvent', $serialized);

            $request->setUrl($this->buildUrl('/api/audit-events?version=2.0'));
            $request->setMethod('POST');

            $return = $this->send($request, ApiRequestOption::NO_RESPONSE);

            $this->restoreErrorHandler();

            return $return;
        } catch (\Exception $e) {
            $this->writeToExceptionLogFile($e->getMessage());
            $this->restoreErrorHandler();
        }

        return false;
    }

    /**
     * Retrive audit events
     *
     * @param array|SearchBuilder $criteria
     *
     * @return bool|\Fei\ApiClient\ResponseDescriptor
     */
    public function retrieve($criteria)
    {
        if ($criteria instanceof SearchBuilder) {
            $criteria = $criteria->getParams();
        }

        if (!is_array($criteria)) {
            throw new AuditException('$criteria has to be of type array of SearchBuilder');
        }

        try {
            $this->registerErrorHandler();

            $request = new RequestDescriptor();

            $request->setUrl($this->buildUrl('/api/audit-events?criteria=' . urlencode(json_encode($criteria))));
            $request->setMethod('GET');

            $return = $this->send($request, ApiRequestOption::NO_RESPONSE);

            $this->restoreErrorHandler();

            return $return;
        } catch (\Exception $e) {
            $this->writeToExceptionLogFile($e->getMessage());
            $this->restoreErrorHandler();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        try {
            $this->registerErrorHandler();

            parent::commit();

            $this->restoreErrorHandler();
        } catch (\Exception $e) {
            $this->writeToExceptionLogFile($e->getMessage());
            $this->restoreErrorHandler();
        }
    }

    /**
     * @return int
     */
    public function getFilterLevel()
    {
        return $this->filterLevel;
    }

    /**
     * @param int $filterLevel
     *
     * @return $this
     */
    public function setFilterLevel($filterLevel)
    {
        $this->filterLevel = $filterLevel;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }


    protected function formatContexts($auditEventData)
    {
        $contexts = $auditEventData['context'];
        if (count($contexts)) {
            $formatedContext = [];
            foreach ($contexts as $context) {
                $formatedContext[$context['key']] = $context['value'];
            }
            $auditEventData['context'] = $formatedContext;
        }

        return $auditEventData;
    }

    /**
     * @param AuditEvent $auditEvent
     * @param array        $params
     *
     * @return AuditEvent
     */
    protected function prepareAuditEvent(AuditEvent $auditEvent, $params = array())
    {
        $data = array_filter($auditEvent->toArray());
        // Prevent from duplicating context items
        unset($data['context']);

        $params += array('origin' => php_sapi_name() == 'cli' ? 'cli' : 'http');
        $params += array('reported_at' => new \DateTime());
        $params += array('server' => $this->getServerName());

        $data = array_merge($data, $params);

        $auditEvent->hydrate($data);

        return $auditEvent;
    }

    /**
     * Add a error handler
     */
    protected function registerErrorHandler()
    {
        $instance = $this;
        $this->previousErrorHandler = set_error_handler(
            function ($errno, $errstr, $errfile, $errline) use ($instance) {
                $message = sprintf('%d: %s - File: %s - Line: %d', $errno, $errstr, $errfile, $errline);
                throw new \Exception($message, $errno);
            }
        );
    }

    /**
     * Restore previous error handler
     */
    protected function restoreErrorHandler()
    {
        restore_error_handler();
    }

    /**
     * @return string
     */
    protected function getServerName()
    {
        $uname = posix_uname();

        return $uname['nodename'];
    }

    /**
     * @return array
     */
    protected function getBackTrace()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);

        $methodExcluded = [
            'Fei\Service\Audit\Client\Audit->getBackTrace',
            'Fei\Service\Audit\Client\Audit->notify'
        ];

        $sanitized = array();

        foreach ($backtrace as $key => $trace) {
            if (isset($trace['file']) && $trace['line']) {
                $sanitized[$key]['file'] = $trace['file'] . ':' . $trace['line'];
            }

            if (isset($trace['class'])) {
                $sanitized[$key]['method'] = $trace['class'] . $trace['type'] . $trace['function'];
                if (in_array($sanitized[$key]['method'], $methodExcluded)) {
                    unset($sanitized[$key]);
                    continue;
                }
            } elseif (isset($trace['function'])) {
                $sanitized[$key]['function'] = $trace['function'];
            }

            if (!empty($trace['args'])) {
                foreach ($trace['args'] as $arg) {
                    if (is_scalar($arg)) {
                        $sanitized[$key]['args'][] = sprintf('(%s) %s', gettype($arg), (string) $arg);
                    } elseif (is_array($arg)) {
                        $sanitized[$key]['args'][] = sprintf('array(%d)', count($arg));
                    } elseif (is_object($arg)) {
                        $sanitized[$key]['args'][] = sprintf('Instance of %s', get_class($arg));
                    } elseif (is_resource($arg)) {
                        $sanitized[$key]['args'][] = sprintf('(resource) %s', get_resource_type($arg));
                    }
                }
            }
        }

        return array_values($sanitized);
    }

    /**
     * Write to exception log file
     *
     * @param string $message
     */
    protected function writeToExceptionLogFile($message)
    {
        if (is_writable($this->exceptionLogFile) || is_writable(dirname($this->exceptionLogFile))) {
            $message = sprintf('[%s] %s' . PHP_EOL, (new \DateTime())->format(\DateTime::ISO8601), $message);
            @file_put_contents($this->exceptionLogFile, $message, FILE_APPEND);
        }
    }
}
