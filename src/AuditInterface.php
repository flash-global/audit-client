<?php

namespace Fei\Service\Audit\Client;

interface AuditInterface
{
    /**
     * @param       $message
     * @param array $params
     *
     * @return mixed|bool
     */
    public function notify($message, array $params);
}
