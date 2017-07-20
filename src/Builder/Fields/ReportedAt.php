<?php
namespace Fei\Service\Audit\Client\Builder\Fields;

use Fei\Service\Audit\Client\Builder\OperatorBuilder;

/**
 * Class ReportedAt
 * @package Fei\Service\Audit\Client\Builder\Fields
 */
class ReportedAt extends OperatorBuilder
{
    /**
     * @param $value string the value used to filter this field
     * @param string $operator the operator used to filter this field
     *
     * @return mixed|void
     */
    public function build($value, $operator = '=')
    {
        $search = $this->builder->getParams();
        $search['auditEvent_reportedAt'] = $value;
        $search['auditEvent_reportedAt_operator'] = $operator;

        $this->builder->setParams($search);
    }

    /**
     * @param $value string the starting date to filter
     * @return $this
     */
    public function from($value)
    {
        $search = $this->builder->getParams();
        $search['auditEvent_reportedAt'] = $value;

        $this->builder->setParams($search);

        return $this;
    }

    /**
     * @param $value string the ending date to filter
     * @return $this
     */
    public function till($value)
    {
        $search = $this->builder->getParams();
        $search['auditEvent_reportedAt_till'] = $value;

        $this->builder->setParams($search);

        return $this;
    }
}
