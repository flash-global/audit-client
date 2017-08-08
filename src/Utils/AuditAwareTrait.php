<?php
/**
 * AuditAwareTrait.php
 *
 * @date        8/08/17
 * @file        AuditAwareTrait.php
 */

namespace Fei\Service\Audit\Client\Utils;

use Fei\Service\Audit\Client\Audit;

/**
 * AuditAwareTrait
 */
class AuditAwareTrait
{
    /**
     * @var Audit
     */
    protected $auditClient;

    /**
     * @return Audit
     */
    public function getAuditClient()
    {
        return $this->auditClient;
    }

    /**
     * @param Audit $auditClient
     *
     * @return AuditAwareTrait
     */
    public function setAuditClient($auditClient)
    {
        $this->auditClient = $auditClient;

        return $this;
    }
}